<?php
/**
 * Form field renderers shared by the server-rendered form and the JS validation
 * (JS relies on the ids: {name}, {name}-help, {name}-error and [data-field]).
 */

declare(strict_types=1);

function field_error_html(string $name, ?string $error, string $extraClass = ''): string
{
    return '<p id="' . e($name) . '-error" class="field-error ' . e($extraClass) . '"' . ($error ? '' : ' hidden') . '>'
        . icon('alert-circle', 'mt-px h-4 w-4 flex-none')
        . '<span data-error-text>' . e($error ?? '') . '</span></p>';
}

/**
 * @param array{type?: string, placeholder?: string, help?: string, autocomplete?: string,
 *              maxlength?: int, inputmode?: string, value?: string, error?: ?string} $o
 */
function text_field(string $name, string $label, array $o = []): string
{
    $error    = $o['error'] ?? null;
    $describe = trim((!empty($o['help']) ? "{$name}-help " : '') . "{$name}-error");

    $attrs = [
        'id'               => $name,
        'name'             => $name,
        'type'             => $o['type'] ?? 'text',
        'class'            => 'field-control',
        'value'            => $o['value'] ?? '',
        'placeholder'      => $o['placeholder'] ?? null,
        'autocomplete'     => $o['autocomplete'] ?? null,
        'maxlength'        => isset($o['maxlength']) ? (string) $o['maxlength'] : null,
        'inputmode'        => $o['inputmode'] ?? null,
        'aria-describedby' => $describe,
        'aria-invalid'     => $error ? 'true' : null,
    ];

    $html  = '<div data-field="' . e($name) . '">';
    $html .= '<label for="' . e($name) . '" class="field-label">' . e($label) . '<span class="field-required" aria-hidden="true">*</span></label>';
    $html .= '<input';
    foreach ($attrs as $key => $value) {
        if ($value !== null) {
            $html .= ' ' . $key . '="' . e($value) . '"';
        }
    }
    $html .= ' required spellcheck="false">';
    if (!empty($o['help'])) {
        $html .= '<p id="' . e($name) . '-help" class="field-help">' . e($o['help']) . '</p>';
    }
    $html .= field_error_html($name, $error);
    $html .= '</div>';

    return $html;
}

/**
 * Checkbox with a rich (pre-escaped) label.
 */
function checkbox_field(string $name, string $labelHtml, bool $checked, ?string $error): string
{
    return '<div data-field="' . e($name) . '">'
        . '<div class="flex items-start gap-3">'
        . '<input id="' . e($name) . '" name="' . e($name) . '" type="checkbox" value="1" class="check-input" required'
        . ' aria-describedby="' . e($name) . '-error"' . ($checked ? ' checked' : '') . ($error ? ' aria-invalid="true"' : '') . '>'
        . '<label for="' . e($name) . '" class="cursor-pointer text-[15px] leading-relaxed text-ink-soft">' . $labelHtml
        . '<span class="field-required" aria-hidden="true">*</span></label>'
        . '</div>'
        . field_error_html($name, $error, 'ml-8')
        . '</div>';
}
