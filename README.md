# Khan Travel – B2B Partner Registration

A lightweight Core PHP landing page at **`/become-a-partner`** where travel agencies apply to become Khan Travel B2B partners. Applications are saved in the existing TravelBookingPanel database (`khantravel.users`) as **pending, inactive agents**, and two emails go out through Resend: one to the admin and one to the applicant.

- Stack: PHP 8.1+ (PDO, cURL), MySQL/MariaDB, Apache with `mod_rewrite`, Tailwind CSS (compiled), vanilla JS.
- No framework, no Composer packages. Node is only used to build the CSS.
- Nothing in the existing Laravel app is changed.

---

## 1. How it works

```
/become-a-partner ──► form (client-side validation)
        │ POST (fetch, or a normal POST without JS)
        ▼
rate limit (per IP) → CSRF + Origin check → honeypot → server validation
→ Friendly Captcha verification → duplicate email check
→ INSERT users (user_type=agent, approval_status=pending, status=inactive, agent_code=AGT-xxxx)
→ Resend: admin notification + applicant confirmation (failures logged only)
→ success screen
```

| Situation | Response | What the visitor sees |
|---|---|---|
| Invalid fields / CAPTCHA | 422 | "Please check the highlighted fields and try again." + inline errors |
| Email already in `users` | 409 | "This email address is already registered or has an existing application…" |
| CSRF token missing or expired | 403 | "Your session has expired. Please submit the form again." (a fresh token is issued automatically) |
| Too many attempts | 429 | Friendly "please wait" message |
| DB / unexpected error | 500 | "Something went wrong while submitting your application…" (details go to the log only) |

### Database record

| Column | Value |
|---|---|
| `user_type` | `agent` |
| `approval_status` | `pending` |
| `status` | `inactive` |
| `first_name`, `last_name`, `company_name`, `email`, `phone` | from the form (email stored in lower case; phone 7–15 digits, `+`, spaces, `-`, `()` allowed) |
| `previous_contact` | `none`, `sales_team`, `business_development`, `support_team`, `other` (**new column**, see §3) |
| `agent_code` | next `AGT-0001`-style code, same format as `User::generateAgentCode()` in the Laravel app. Collisions are retried. |
| `password` | bcrypt hash of a random secret that is discarded immediately (see §6) |
| `internal_notes` | "Applied via the B2B partner registration page on …" |
| `created_at`, `updated_at` | current time in `APP_TIMEZONE` |

The agent wallet is **not** created here. The Laravel app creates it when an admin approves the agent (`User::approveAgent()`).

---

## 2. Project structure

```
/
├── .htaccess                 routing, clean URLs, 404, file protection
├── .env.example              every configuration key, documented
├── index.php                 "/" → redirects to /become-a-partner
├── partner.php               /become-a-partner (GET page, POST application)
├── 404.php                   branded 404 page
├── privacy.php               /privacy-policy (privacy notice for the form)
├── config/config.php         reads environment variables / .env (no secrets inside)
├── includes/
│   ├── bootstrap.php         config, error handling, includes
│   ├── helpers.php           escaping, URLs, JSON responses, logging, icons
│   ├── database.php          PDO connection
│   ├── csrf.php              secure session + CSRF token
│   ├── validation.php        server-side validation + "previous contact" options
│   ├── captcha.php           Friendly Captcha v2 verification
│   ├── rate_limit.php        per-IP submission limit
│   ├── mail.php              reusable Resend mail service + partner emails
│   └── partner_application.php   the application workflow + DB insert
├── views/                    page templates (landing page + partials)
├── emails/                   email templates (HTML + plain text) and shared layout
├── assets/                   compiled CSS, JS, fonts (self-hosted Inter), logo
├── database/migrations/      SQL migration (+ rollback)
├── storage/                  logs, rate-limit data, mail previews (not web-accessible)
├── src/tailwind.css          Tailwind source → assets/css/app.css
└── tailwind.config.js, package.json   build tooling only
```

---

## 3. Database migration (required once)

`users` had no column for "Have you already had contact with one of our employees?", so one nullable column is added. It is additive only: existing rows, columns and Laravel code are unaffected.

```sql
ALTER TABLE `users`
    ADD COLUMN `previous_contact` VARCHAR(50) NULL DEFAULT NULL AFTER `company_name`;

-- Rollback
ALTER TABLE `users` DROP COLUMN `previous_contact`;
```

File: `database/migrations/2026_09_24_000001_add_previous_contact_to_users.sql`

```bash
mysql -u <user> -p khantravel < database/migrations/2026_09_24_000001_add_previous_contact_to_users.sql
```

> Tip: if you'd rather manage the schema from Laravel, create an equivalent Laravel migration in the main app instead of running the SQL directly. Don't do both.

---

## 4. Installation

1. **Upload** the project to its own (sub)domain or folder, e.g. `partners.khantravel.com`.
   `node_modules/`, `src/`, `package*.json` and `tailwind.config.js` are not needed on the server.
2. **Configure**: copy `.env.example` → `.env` and fill it in (see §5), or set the same keys as real server environment variables (these take precedence).
3. **Run the migration** (§3).
4. **Apache**: `mod_rewrite` enabled and `AllowOverride All` (or at least `FileInfo Options Limit`) for this directory.
   - Installed in a **sub-folder**? Update the two `ErrorDocument` lines in `.htaccess` to include the folder, e.g. `/partners/404.php`. Rewrite rules need no changes.
5. **Permissions**: the web server user must be able to write to `storage/` (logs, rate-limit files).
6. **HTTPS**: serve the site over HTTPS. Secure cookies and HSTS switch on automatically.
7. Open `https://your-domain/become-a-partner`.

Requirements: PHP ≥ 8.1 with `pdo_mysql`, `curl`, `mbstring`, `openssl`. A MySQL user with `SELECT, INSERT` on `khantravel.users` is enough.

---

## 5. Configuration (`.env`)

| Key | Purpose |
|---|---|
| `APP_ENV` | `production` or `local`. In production the CAPTCHA placeholder is never shown. |
| `APP_DEBUG` | Show PHP errors in the browser. **Keep `false` in production.** |
| `APP_URL` | Public base URL, no trailing slash. Used for canonical URL and email links. |
| `APP_TIMEZONE` | Must match the Laravel app's `app.timezone` (currently `UTC`). |
| `MAIN_SITE_URL` | Khan Travel website ("Back to Khan Travel" buttons, footer). |
| `PRIVACY_POLICY_URL` | Optional external Privacy Policy link. Empty = this app's own `/privacy-policy` page (review its content with your legal adviser and add your company address). |
| `CONTACT_EMAIL` | Optional contact email shown beside the form. |
| `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD` | Existing `khantravel` database. |
| `RESEND_API_KEY` | Resend API key. **Server-side only, never in JS or committed files.** |
| `MAIL_FROM` | Sender on a domain **verified in Resend**, e.g. `contact@travelbookingpanel.com`. Do not use a Gmail address. (`MAIL_FROM_ADDRESS` also works.) |
| `MAIL_FROM_NAME` | Sender name, e.g. `Khan Travel`. |
| `MAIL_REPLY_TO` | Reply-To on the applicant confirmation (`hm.younas22@gmail.com`). |
| `ADMIN_EMAIL` | Receives new-application notifications (comma-separate for several). |
| `LOGO_URL` | Optional public HTTPS URL of the email logo. When empty (recommended), `assets/images/khan-travel-logo-email.png` is embedded in each email as an inline `cid:` image, so it shows in Gmail/Outlook even from localhost. |
| `ADMIN_REVIEW_URL` | Optional. Shows a **Review Application** button in the admin email. `{id}` becomes the user ID, e.g. `https://admin.example.com/admin/agents/{id}`. When empty, the email tells the admin to open *Agents → Pending* instead. |
| `MAIL_PREVIEW` | `true` writes a copy of each email to `storage/mail-preview/` (handy locally). |
| `FRIENDLY_CAPTCHA_SITE_KEY` / `FRIENDLY_CAPTCHA_SECRET_KEY` | Friendly Captcha v2 site key and API key. |
| `FRIENDLY_CAPTCHA_ENDPOINT` | `global` (default) or `eu`. |
| `FRIENDLY_CAPTCHA_SHOW_PLACEHOLDER` | `true` shows a "CAPTCHA not configured" notice in the form (local only). Default `false`. |
| `RATE_LIMIT_MAX_ATTEMPTS` / `RATE_LIMIT_WINDOW` | Submission attempts per IP per window (default 8 per 900 s). |

### Emails (Resend)

- **Admin:** subject "New Khan Travel B2B Partner Application", sent to `ADMIN_EMAIL`, Reply-To = the applicant (so you can answer directly).
- **Applicant:** subject "We Received Your Khan Travel B2B Partner Application", Reply-To = `MAIL_REPLY_TO`.
- Both have HTML and plain-text versions and use table-based, inline-styled markup with Outlook (MSO) fallbacks.
- Each send carries an `Idempotency-Key`, so a retry can't deliver twice.
- If sending fails, the application is still saved and the error is logged in `storage/logs/`.

> ⚠️ The Resend API key that was shared earlier should be treated as compromised. **Rotate it** in the Resend dashboard and put only the new key in `.env` on the server.

### Friendly Captcha

1. Create an application at <https://app.friendlycaptcha.eu> → copy the **site key**.
2. Create an **API key** → this is `FRIENDLY_CAPTCHA_SECRET_KEY`.
3. Add your domain to the application's allowed domains.

The widget script (Friendly Captcha SDK 1.1.1 from jsDelivr, pinned with SRI hashes) is only loaded when both keys are set. Without keys:
- `local`: a clearly labelled placeholder is shown in the form.
- both: submissions are accepted (honeypot and rate limit still apply) and a warning is logged on every submission.

If the Friendly Captcha API itself is unreachable or rejects the API key, the submission is **allowed and an error is logged**. This follows Friendly Captcha's recommendation, so an outage never locks real agents out. Invalid, expired or reused solutions are always rejected.

---

## 6. Account security & password setup

- The public form **never** asks for, generates, emails or logs a password.
- `users.password` is `NOT NULL`, so a bcrypt hash of 32 random bytes is stored and the secret is thrown away. Nobody knows it, so **the account can't be logged into.**
- `approval_status = pending` keeps the agent out of the agent area even if a login were attempted (`AgentMiddleware`).

**Password creation is deliberately left out of this public page.** The existing Laravel app currently has no password-reset/invitation flow for agents, so after an admin approves an application the agent still needs a way to set a password. Recommended (in the Laravel app, outside this project): on approval, send a Laravel password-reset link (`Password::sendResetLink`, which needs the standard `password_reset_tokens` table), or let the admin set a password manually. Never send passwords by email.

Also note: approving in Laravel sets `approval_status = active` but leaves `status` as `inactive`. The Laravel agent login doesn't check `status` today, so this is harmless, but set `status = active` on approval if you start relying on it.

---

## 7. Security summary

- PDO prepared statements everywhere (no user input in SQL); `ATTR_EMULATE_PREPARES = false`.
- Server-side validation mirrors the client-side rules; all output escaped with `htmlspecialchars`.
- CSRF token (session, `hash_equals`) plus an Origin check. Session cookie is `HttpOnly`, `SameSite=Lax`, and `Secure` on HTTPS.
- Honeypot field, per-IP rate limit, Friendly Captcha.
- Duplicate protection: pre-check plus the unique index on `users.email` (case-insensitive collation). The button is disabled while submitting, and the no-JS path uses Post/Redirect/Get.
- Errors are never shown to visitors. They go to `storage/logs/app-YYYY-MM-DD.log`, with applicant emails masked (`ab***@domain.com`).
- `.env`, dotfiles, docs, SQL and internal folders (`config/`, `includes/`, `views/`, `emails/`, `storage/`, `database/`, `src/`) are not web-accessible.
- Security headers: `X-Content-Type-Options`, `X-Frame-Options`, `Referrer-Policy`, `Permissions-Policy`, and HSTS on HTTPS.

---

## 8. Front-end build

The compiled `assets/css/app.css` is committed, so a build is only needed after editing classes or `src/tailwind.css`:

```bash
npm install
npm run build      # or: npm run watch
```

Fonts are self-hosted (Inter variable, latin + latin-ext), so no Google Fonts requests are made.

---

## 9. Testing checklist

**Routing**
- [ ] `/become-a-partner` → 200 (also with a trailing slash)
- [ ] `/partner.php` → 301 to `/become-a-partner`; `/` → 302 to `/become-a-partner`
- [ ] `/random-page`, `/a/b/c`, `/includes/helpers.php`, `/config/config.php` → branded 404
- [ ] `/.env`, `/README.md`, `/package.json`, `/storage/logs/` → not served

**Form (with JavaScript)**
- [ ] Submit empty → inline errors, summary alert, focus moves to the first invalid field
- [ ] Invalid email / name with digits / `<` in company → matching messages
- [ ] Unchecked legal boxes → both errors
- [ ] CAPTCHA unsolved → "Please complete the verification"
- [ ] Valid submit → spinner, disabled button, then "Application Submitted Successfully"
- [ ] Same email again (any capitalisation) → duplicate message on the email field
- [ ] Keyboard only: Tab through everything, focus rings visible, checkboxes toggle with Space

**Form (JavaScript disabled)**
- [ ] Errors re-render with the entered values kept; a valid submit redirects and shows the success panel once

**Database** (after a test submission)
```sql
SELECT id, user_type, approval_status, status, agent_code, company_name, previous_contact, email, created_at
FROM users ORDER BY id DESC LIMIT 1;
-- expect: agent | pending | inactive | AGT-xxxx | …
```

**Emails**
- [ ] Locally: set `MAIL_PREVIEW=true` and open the files in `storage/mail-preview/`
- [ ] With a real key: both emails arrive; check Gmail (web + app), Outlook desktop and Apple Mail
- [ ] Resend dashboard → Emails shows both as *delivered*

**Responsive**: 1920, 1440, 1280, 1024, 768, 430, 390, 375, 360 px, with no horizontal scrolling at any width.

**Clean up** test rows afterwards (only the ones you created):
```sql
DELETE FROM users WHERE email = 'your-test-address@example.com' AND approval_status = 'pending';
```

---

## 10. Logs

`storage/logs/app-YYYY-MM-DD.log` records: applications created (user ID, agent code, masked email), email send results with Resend IDs or errors, CAPTCHA/API problems, honeypot and rate-limit hits, and unexpected exceptions. PHP-level errors go to `storage/logs/php-errors.log`.

---

## 11. Languages (English / German)

All visible text lives in JSON files – no database:

| File | Purpose |
|---|---|
| `lang/en.json` | English texts (reference file) |
| `lang/de.json` | German texts (same keys as English) |
| `lang/languages.json` | Default language + which languages are active |

- Visitors switch language with the dropdown in the header (`?lang=de`). The choice is remembered in the `kt_lang` cookie for one year.
- New visitors get the **default language**. The dropdown is hidden when only one language is active.
- A missing text falls back to the default language, then to English, so the page never breaks.
- The **applicant confirmation email** is sent in the language the applicant used. The **admin email** is always English and shows the applicant's language.
- Search engines get `hreflang` links for every active language.

### Language settings page

`/language-settings` lets you choose the default language and turn languages on or off (it writes `lang/languages.json`).
It is protected by `LANGUAGE_ADMIN_PASSWORD` in `.env`; if that is empty the page is disabled. Login attempts are rate-limited and the page is `noindex`.
The web server needs write permission on `lang/languages.json`.

### Edit or add texts

- Edit a text: change it in `lang/en.json` / `lang/de.json` (keep the keys and `:placeholders` such as `:email`, `:company`, `:year`).
- Add a language: copy `lang/en.json` to e.g. `lang/fr.json`, translate it, add `"fr": { "name": "French", "native": "Français", "active": false }` to `lang/languages.json`, then activate it on `/language-settings`. The settings page lists any missing texts.
