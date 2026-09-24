/**
 * Khan Travel – B2B partner application form.
 * Client-side validation, AJAX submission and success/error states.
 * Without JavaScript the form still posts normally and the server renders the result.
 */
(function () {
  'use strict';

  var form = document.getElementById('partner-form');
  if (!form || !window.fetch || !window.FormData) return;

  var MSG = {
    validation: 'Please check the highlighted fields and try again.',
    server: 'Something went wrong while submitting your application. Please try again or contact our team.',
    network: 'We could not reach our server. Please check your internet connection and try again.',
    captchaPending: 'The verification is still running. Please wait a moment and submit again.',
    captchaMissing: 'Please complete the verification.'
  };

  var alertBox = document.getElementById('form-alert');
  var alertText = alertBox.querySelector('[data-alert-text]');
  var button = document.getElementById('submit-button');
  var buttonLabel = button.querySelector('[data-button-label]');
  var spinner = button.querySelector('[data-spinner]');
  var liveStatus = document.getElementById('form-status');
  var formPanel = document.getElementById('form-panel');
  var successPanel = document.getElementById('success-panel');
  var successTitle = document.getElementById('success-title');
  var captchaOn = form.getAttribute('data-captcha') === 'on';
  var submitting = false;

  var NAME_RE = /^[\p{L}\p{M}][\p{L}\p{M} '’.\-]*$/u;
  var EMAIL_RE = /^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/;

  function nameRule(label) {
    return function (v) {
      if (!v) return 'Please enter your ' + label + '.';
      if (v.length > 100) return label.charAt(0).toUpperCase() + label.slice(1) + ' must be 100 characters or fewer.';
      if (!NAME_RE.test(v)) return 'Please use letters only (spaces, hyphens and apostrophes are fine).';
      return '';
    };
  }

  var rules = {
    company_name: function (v) {
      if (!v) return 'Please enter your company name.';
      if (v.length < 2 || v.length > 255) return 'Company name must be between 2 and 255 characters.';
      if (/[<>{}]/.test(v)) return 'Company name contains characters that are not allowed.';
      return '';
    },
    first_name: nameRule('first name'),
    last_name: nameRule('last name'),
    email: function (v) {
      if (!v) return 'Please enter your business email address.';
      if (v.length > 254 || !EMAIL_RE.test(v)) return 'Please enter a valid email address, e.g. name@youragency.com.';
      return '';
    },
    phone: function (v) {
      if (!v) return 'Please enter your phone number.';
      var digits = v.replace(/\D+/g, '');
      if (v.length > 30 || !/^\+?[0-9\s().\-]+$/.test(v) || digits.length < 7 || digits.length > 15) {
        return 'Please enter a valid phone number, e.g. +92 300 1234567.';
      }
      return '';
    },
    previous_contact: function (v) {
      return v ? '' : 'Please choose an option from the list.';
    },
    authorized_representative: function (checked) {
      return checked ? '' : 'Please confirm that you are authorized to represent this company.';
    },
    privacy_consent: function (checked) {
      return checked ? '' : 'Please accept the Privacy Policy to continue.';
    }
  };
  var fieldOrder = Object.keys(rules);

  function valueOf(name) {
    var el = form.elements[name];
    if (el.type === 'checkbox') return el.checked;
    return el.value.replace(/\s+/g, ' ').trim();
  }

  function setError(name, message) {
    var error = document.getElementById(name + '-error');
    var input = form.elements[name];
    if (!error) return;
    error.querySelector('[data-error-text]').textContent = message || '';
    error.hidden = !message;
    if (input && input.setAttribute) {
      if (message) input.setAttribute('aria-invalid', 'true');
      else input.removeAttribute('aria-invalid');
    }
  }

  function validateField(name) {
    var message = rules[name](valueOf(name));
    setError(name, message);
    return !message;
  }

  function isInvalid(el) {
    return el.getAttribute('aria-invalid') === 'true';
  }

  // Validate text fields when leaving them (only once something was typed).
  form.addEventListener('focusout', function (e) {
    var el = e.target;
    if (!rules[el.name] || el.type === 'checkbox') return;
    if (valueOf(el.name) !== '' || isInvalid(el)) validateField(el.name);
  });

  // Clear errors as soon as the visitor fixes a field.
  form.addEventListener('input', function (e) {
    var el = e.target;
    if (rules[el.name] && isInvalid(el)) validateField(el.name);
  });
  form.addEventListener('change', function (e) {
    var el = e.target;
    if (rules[el.name] && (el.type === 'checkbox' || el.tagName === 'SELECT')) validateField(el.name);
  });

  // ---- Friendly Captcha ----------------------------------------------------

  function captchaResponse() {
    var field = form.elements['frc-captcha-response'];
    return field ? field.value : '';
  }

  function captchaError() {
    if (!captchaOn) return '';
    var r = captchaResponse();
    if (r && r.charAt(0) !== '.') return '';
    return /^\.(REQUESTING|SOLVING|VERIFYING)/.test(r) ? MSG.captchaPending : MSG.captchaMissing;
  }

  function resetCaptcha() {
    try {
      if (window.frcaptcha && window.frcaptcha.getAllWidgets) {
        window.frcaptcha.getAllWidgets().forEach(function (w) { w.reset(); });
      }
    } catch (err) { /* widget not ready – nothing to reset */ }
  }

  var captchaEl = form.querySelector('.frc-captcha');
  if (captchaEl) {
    captchaEl.addEventListener('frc:widget.complete', function () { setError('captcha', ''); });
  }

  // ---- UI states -----------------------------------------------------------

  function showAlert(message) {
    alertText.textContent = message;
    alertBox.hidden = false;
  }

  function hideAlert() {
    alertBox.hidden = true;
    alertText.textContent = '';
  }

  function setLoading(on) {
    submitting = on;
    button.disabled = on;
    button.setAttribute('aria-busy', on ? 'true' : 'false');
    spinner.classList.toggle('hidden', !on);
    buttonLabel.textContent = on ? 'Submitting…' : 'Submit Partner Application';
    liveStatus.textContent = on ? 'Submitting your application, please wait.' : '';
  }

  function focusFirstError(fieldNames) {
    for (var i = 0; i < fieldNames.length; i++) {
      var el = form.elements[fieldNames[i]];
      if (el && el.focus) {
        el.focus({ preventScroll: true });
        (el.closest('[data-field]') || el).scrollIntoView({ block: 'center', behavior: 'smooth' });
        return;
      }
    }
    alertBox.focus();
  }

  function showSuccess() {
    formPanel.hidden = true;
    successPanel.hidden = false;
    successPanel.parentElement.scrollIntoView({ block: 'start', behavior: 'smooth' });
    successTitle.focus({ preventScroll: true });
  }

  // ---- Submit --------------------------------------------------------------

  form.addEventListener('submit', function (e) {
    e.preventDefault();
    if (submitting) return;

    hideAlert();

    var invalid = fieldOrder.filter(function (name) { return !validateField(name); });
    var captchaMsg = captchaError();
    setError('captcha', captchaMsg);

    if (invalid.length || captchaMsg) {
      showAlert(MSG.validation);
      if (invalid.length) focusFirstError(invalid);
      else form.querySelector('[data-field="captcha"]').scrollIntoView({ block: 'center', behavior: 'smooth' });
      return;
    }

    setLoading(true);
    var succeeded = false;

    // Browser autofill sometimes fills the hidden anti-spam field; a real visitor's
    // browser runs this script, simple spam bots do not.
    var body = new FormData(form);
    body.set('kt_hp_check', '');

    fetch(form.action, {
      method: 'POST',
      body: body,
      credentials: 'same-origin',
      headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
    })
      .then(function (res) {
        return res.json().catch(function () { return {}; }).then(function (data) {
          return { status: res.status, data: data || {} };
        });
      })
      .then(function (result) {
        var data = result.data;

        if (result.status === 200 && data.ok) {
          succeeded = true;
          showSuccess();
          return;
        }

        if (data.csrf_token && form.elements._token) {
          form.elements._token.value = data.csrf_token;
        }

        var errors = data.errors || {};
        Object.keys(errors).forEach(function (name) { setError(name, errors[name]); });
        showAlert(data.message || MSG.server);

        // The CAPTCHA response is single-use once the server has verified it.
        if (result.status === 409 || result.status >= 500 || errors.captcha) resetCaptcha();

        var names = Object.keys(errors).filter(function (n) { return rules[n]; });
        if (names.length) focusFirstError(names);
        else alertBox.focus();
      })
      .catch(function () {
        showAlert(MSG.network);
        alertBox.focus();
      })
      .then(function () {
        if (!succeeded) setLoading(false);
      });
  });
})();
