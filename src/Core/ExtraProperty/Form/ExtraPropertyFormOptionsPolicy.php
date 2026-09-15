<?php

/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\ExtraProperty\Form;

use PrestaShop\PrestaShop\Core\ExtraProperty\Validation\ExtraPropertyValidator;

/**
 * The single authority on the form options an extra property definition may declare for its
 * back-office field.
 *
 * A definition's formType/formOptions are rendered, unchanged, inside the entity forms every
 * employee opens (product, customer, order…), and the field type is deliberately free: modules
 * ship rich custom types and even core types such as TextPreviewType are legitimately useful.
 * What must stay out of a definition author's hands are the OPTIONS that make any type
 * dangerous. They fall in two groups:
 *
 *  - options whose VALUE is displayed — rendered raw or purified by the UI-kit theme
 *    (label_subtitle, hint, alert_*), interpolated as a tag name (label_tag_name), used as a
 *    link target (download_url, external_link) — are ALLOWED, with their content checked: plain
 *    display text (no "<", no control character, see ExtraPropertyValidator::isSafeDisplayText()),
 *    an enumerated value, or an http(s)/root-relative URL. Every attribute NAME of an attr bag is
 *    echoed verbatim, so "on*", inline style and URL attributes are stripped from every bag;
 *  - options that change what the field DOES — switch a value or label to raw rendering
 *    (allow_html, label_html), select which Twig blocks render it (block_prefix, form_theme),
 *    change what it maps to or how it validates (mapped, data, constraints, property_path…), or
 *    take callables and class names (choice_loader, entry_type, class…) — are DENIED by name:
 *    no content check can make them safe.
 *
 * Everything else — including the options a custom type defines for itself — passes, and a
 * definition is refused on write when the resulting field does not build (FormOptionsValidator),
 * or its field is dropped on read when it does not build (ExtraPropertiesFormBuilderModifier).
 *
 * The rules are enforced at BOTH ends from this one class, so they can never drift apart:
 *  - FormOptionsValidator (write, i.e. ExtraPropertyRegistry::register()) refuses a definition
 *    with an explicit error naming each refused option — the author learns immediately why the
 *    input is not accepted;
 *  - ExtraPropertiesFormBuilderModifier (read, i.e. at render) drops the very same options and
 *    logs them — a row written straight into the registry table cannot render what a
 *    registration would have refused.
 *
 * Static-only, like ExtraPropertyConstraintGrammar: a vocabulary, not a service.
 */
class ExtraPropertyFormOptionsPolicy
{
    /**
     * Options a definition may never set through form_options, whatever the form type: each one
     * changes what the field DOES, so no content check applies. Grouped by the reason it is denied.
     *
     * The policy only filters what a definition passes through form_options. A CUSTOM form type
     * is free to use any of these internally — it sets them in its own configureOptions() or
     * buildForm(), from module code the shop already trusts, and this policy never sees them.
     * That is the intended way to get, for instance, a collection of sub-fields: wrap a
     * CollectionType in a custom type that configures entry_type / allow_add / prototype itself,
     * declare that type as the definition's formType, and pair it with a JSON-typed property
     * (the writer json-encodes the submitted array, the reader decodes it).
     *
     * @var list<string>
     */
    public const DENIED_OPTIONS = [
        // Switch the stored VALUE (allow_html on TextPreviewType) or the label/help to raw HTML
        // rendering. The danger is in the text they would render unescaped, not in the option.
        'allow_html',
        'help_html',
        'label_html',
        // Select which Twig block or theme renders the field: a block that outputs raw could be picked.
        'block_name',
        'block_prefix',
        'form_theme',
        'use_default_themes',
        // Set by the modifier itself from the definition — label/help from its translatable
        // wording, required from isRequired(), data from the stored value, constraints from the
        // DSL. An override through form_options would bypass the definition.
        'constraints',
        'data',
        'help',
        'label',
        'required',
        // Change WHAT the field reads from and writes to. With mapped: true and a property_path,
        // the extra value would be written onto the HOST entity (mass assignment through the
        // product or customer form); getter/setter are callables, data_class loads a class.
        'by_reference',
        'compound',
        'data_class',
        'default_empty_data',
        'empty_data',
        'getter',
        'inherit_data',
        'mapped',
        'property_path',
        'setter',
        // Change how the field validates and where its errors land (validation_groups also
        // accepts a callable).
        'error_bubbling',
        'error_mapping',
        'validation_groups',
        // Substituted into the displayed label/help/attribute texts without any check.
        'attr_translation_parameters',
        'help_translation_parameters',
        'label_translation_parameters',
        // PrestaShop extensions that act beyond the field: write the value to every shop, bind
        // the field to a configuration key, or substitute the displayed/stored value when empty.
        'disabled_value',
        'empty_view_data',
        'modify_all_shops',
        'multistore_configuration_key',
        'multistore_dropdown',
        // Take a callable, a property path, a class name or a loader object, executed or loaded
        // while the field is built (ChoiceType and EntityType options).
        'choice_attr',
        'choice_filter',
        'choice_label',
        'choice_loader',
        'choice_name',
        'choice_value',
        'class',
        'em',
        'group_by',
        'query_builder',
        // CollectionType wiring: nested fields built from options (entry_type is a class name,
        // entry_options a full option set). Not available to a definition directly — see the
        // custom-type alternative in the docblock above.
        'allow_add',
        'allow_delete',
        'delete_empty',
        'entry_options',
        'entry_type',
        'prototype',
        'prototype_data',
        'prototype_name',
        'prototype_options',
        // Form-level options, meaningless on a field.
        'action',
        'csrf_field_name',
        'csrf_protection',
        'csrf_token_id',
        'method',
    ];

    /**
     * Attribute names refused inside every "attr" bag (attr, label_attr, row_attr, help_attr…),
     * on top of every "on*" event handler: inline CSS and URL-bearing attributes.
     *
     * @var list<string>
     */
    public const DENIED_ATTRIBUTES = [
        'action',
        'formaction',
        'href',
        'ping',
        'src',
        'srcdoc',
        'style',
        'xlink:href',
    ];

    /**
     * Options whose string value is displayed (rendered raw or purified by the theme): allowed as
     * plain display text.
     *
     * @var list<string>
     */
    public const TEXT_OPTIONS = ['alert_title', 'hint', 'label_help_box', 'label_subtitle', 'label_tab'];

    /**
     * Options carrying a list of displayed strings: allowed as lists of plain display texts
     * (alert_message also accepts a single string).
     *
     * @var list<string>
     */
    public const TEXT_LIST_OPTIONS = ['alert_message', 'data_list'];

    /**
     * Options used as a link target: allowed as an http(s) URL or a root-relative path.
     *
     * @var list<string>
     */
    public const URL_OPTIONS = ['download_url'];

    /**
     * Keys of the external_link array option and their rule.
     *
     * @var array<string, string> key => 'url'|'text'|'bool' or an enumerated list joined by "|"
     */
    private const EXTERNAL_LINK_KEYS = [
        'align' => 'left|right',
        'href' => 'url',
        'open_in_new_tab' => 'bool',
        'position' => 'append|prepend',
        'text' => 'text',
    ];

    /**
     * Options selecting the translation catalogue of labels: a string value follows the same rule
     * as the definition's own domains, so it can never route a label through the ICU formatter
     * ("+intl-icu"), where a malformed pattern throws on every render.
     *
     * @var list<string>
     */
    private const DOMAIN_OPTIONS = ['choice_translation_domain', 'translation_domain'];

    /**
     * Options carrying a list of choices: labels are displayed (and, expanded, rendered like any
     * other label) so they follow the display-text rule; values must be scalar. A string value —
     * a property path or a callable for Symfony — is refused.
     *
     * @var list<string>
     */
    private const CHOICE_LIST_OPTIONS = ['choices', 'preferred_choices'];

    /**
     * Value rule of the known scalar options: accepted PHP types (gettype() names) and, for
     * enumerated options, the accepted values. A wrong-typed or out-of-range value would throw in
     * Symfony's options resolver while the entity form is built; refusing it here gives the author
     * a clear error on write and drops it on read. label_tag_name is interpolated as an HTML tag
     * name and alert_type as a CSS class: only known values pass.
     *
     * @var array<string, array{types: list<string>, values?: list<string>}>
     */
    private const OPTION_VALUE_RULES = [
        'alert_position' => ['types' => ['string'], 'values' => ['append', 'prepend']],
        'alert_type' => ['types' => ['string'], 'values' => ['danger', 'info', 'primary', 'secondary', 'success', 'warning']],
        'currency' => ['types' => ['boolean', 'string']],
        'divisor' => ['types' => ['integer', 'double']],
        'expanded' => ['types' => ['boolean']],
        'grouping' => ['types' => ['boolean']],
        'html5' => ['types' => ['boolean']],
        'input' => ['types' => ['string'], 'values' => ['array', 'datetime', 'datetime_immutable', 'number', 'string', 'timestamp']],
        'invalid_message' => ['types' => ['string']],
        'label_tag_name' => ['types' => ['string'], 'values' => ['div', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'label', 'legend', 'p', 'span', 'strong']],
        'multiple' => ['types' => ['boolean']],
        'placeholder' => ['types' => ['boolean', 'string']],
        'rounding_mode' => ['types' => ['integer']],
        'scale' => ['types' => ['integer']],
        'symbol' => ['types' => ['boolean', 'string']],
        'trim' => ['types' => ['boolean']],
        'type' => ['types' => ['string'], 'values' => ['fractional', 'integer']],
        'widget' => ['types' => ['string'], 'values' => ['choice', 'single_text', 'text']],
        'with_minutes' => ['types' => ['boolean']],
        'with_seconds' => ['types' => ['boolean']],
    ];

    /**
     * Static-only class.
     */
    private function __construct()
    {
    }

    /**
     * Write-side check: every reason the options are refused, as human-readable messages naming
     * the offending option so the author can fix the definition. Empty when accepted.
     *
     * @param array<string, mixed>|null $formOptions
     *
     * @return list<string>
     */
    public static function validate(?array $formOptions): array
    {
        return self::inspect($formOptions ?? [])['errors'];
    }

    /**
     * Read-side counterpart of validate(), built on the very same walk: the options with every
     * refused entry removed, plus the list of what was removed (for logging). Options that
     * validate() accepts come back untouched.
     *
     * @param array<string, mixed>|null $formOptions
     *
     * @return array{options: array<string, mixed>, dropped: list<string>}
     */
    public static function sanitize(?array $formOptions): array
    {
        $inspected = self::inspect($formOptions ?? []);

        return ['options' => $inspected['options'], 'dropped' => $inspected['dropped']];
    }

    /**
     * Walks the options once, producing both the cleaned array and the error/dropped lists, so
     * write and read can never disagree on what is acceptable.
     *
     * @param array<mixed, mixed> $formOptions
     *
     * @return array{options: array<string, mixed>, errors: list<string>, dropped: list<string>}
     */
    private static function inspect(array $formOptions): array
    {
        $options = [];
        $errors = [];
        $dropped = [];

        foreach ($formOptions as $option => $value) {
            if (!is_string($option)) {
                $errors[] = sprintf('Form option "%s" is not a valid option name.', (string) $option);
                $dropped[] = (string) $option;
                continue;
            }

            // A null option is "not set": nothing to render, nothing to refuse.
            if (null === $value) {
                continue;
            }

            if (in_array($option, self::DENIED_OPTIONS, true)) {
                $errors[] = sprintf('Form option "%s" cannot be set on an extra property field.', $option);
                $dropped[] = $option;
                continue;
            }

            if ('attr' === $option || str_ends_with($option, '_attr')) {
                if (!is_array($value)) {
                    $errors[] = sprintf('Form option "%s" must be an object of attribute name => value.', $option);
                    $dropped[] = $option;
                    continue;
                }
                $attributes = [];
                foreach ($value as $attribute => $attributeValue) {
                    if (!is_string($attribute)
                        || 1 === preg_match('/^on/i', $attribute)
                        || in_array(strtolower($attribute), self::DENIED_ATTRIBUTES, true)
                        || (null !== $attributeValue && !is_scalar($attributeValue))
                    ) {
                        $errors[] = sprintf(
                            'HTML attribute "%s" of form option "%s" cannot be set on an extra property field (event handlers, inline styles and URL attributes are refused; values must be scalar).',
                            (string) $attribute,
                            $option
                        );
                        $dropped[] = $option . '.' . (string) $attribute;
                        continue;
                    }
                    $attributes[$attribute] = $attributeValue;
                }
                $options[$option] = $attributes;
                continue;
            }

            if (in_array($option, self::TEXT_OPTIONS, true)) {
                if (!is_string($value) || !ExtraPropertyValidator::isSafeDisplayText($value)) {
                    $errors[] = self::textError($option);
                    $dropped[] = $option;
                    continue;
                }
                $options[$option] = $value;
                continue;
            }

            if (in_array($option, self::TEXT_LIST_OPTIONS, true)) {
                $texts = is_string($value) && 'alert_message' === $option ? [$value] : $value;
                if (!is_array($texts) || !self::isSafeTextList($texts)) {
                    $errors[] = self::textError($option, true);
                    $dropped[] = $option;
                    continue;
                }
                $options[$option] = $value;
                continue;
            }

            if (in_array($option, self::URL_OPTIONS, true)) {
                if (!is_string($value) || !ExtraPropertyValidator::isSafeUrl($value)) {
                    $errors[] = sprintf('Form option "%s" must be an http(s) URL or a root-relative path.', $option);
                    $dropped[] = $option;
                    continue;
                }
                $options[$option] = $value;
                continue;
            }

            if ('external_link' === $option) {
                $error = self::externalLinkError($value);
                if (null !== $error) {
                    $errors[] = $error;
                    $dropped[] = $option;
                    continue;
                }
                $options[$option] = $value;
                continue;
            }

            if (in_array($option, self::CHOICE_LIST_OPTIONS, true)) {
                if (!is_array($value)) {
                    $errors[] = sprintf('Form option "%s" must be a list or an object of label => value.', $option);
                    $dropped[] = $option;
                    continue;
                }
                $choices = [];
                foreach ($value as $label => $choiceValue) {
                    if ((is_string($label) && !ExtraPropertyValidator::isSafeDisplayText($label))
                        || (null !== $choiceValue && !is_scalar($choiceValue))
                    ) {
                        $errors[] = sprintf(
                            'Choice "%s" of form option "%s" is not allowed: labels must not contain "<" or control characters and values must be scalar.',
                            (string) $label,
                            $option
                        );
                        $dropped[] = $option . '.' . (string) $label;
                        continue;
                    }
                    $choices[$label] = $choiceValue;
                }
                $options[$option] = $choices;
                continue;
            }

            if (in_array($option, self::DOMAIN_OPTIONS, true)) {
                if (!is_bool($value) && !(is_string($value) && ExtraPropertyValidator::isTranslationDomain($value))) {
                    $errors[] = sprintf(
                        'Form option "%s" must be false or a translation domain of 2 or 3 dot-separated PascalCase segments.',
                        $option
                    );
                    $dropped[] = $option;
                    continue;
                }
                $options[$option] = $value;
                continue;
            }

            $error = self::knownOptionValueError($option, $value);
            if (null !== $error) {
                $errors[] = $error;
                $dropped[] = $option;
                continue;
            }

            // Not denied and not a known core option: an option of the declared (possibly custom)
            // type. Its shape is checked by building the field — refused on write, dropped on read.
            $options[$option] = $value;
        }

        return ['options' => $options, 'errors' => $errors, 'dropped' => $dropped];
    }

    private static function textError(string $option, bool $list = false): string
    {
        return sprintf(
            'Form option "%s" must be %s: no "<" and no control character.',
            $option,
            $list ? 'a list of plain texts' : 'a plain text'
        );
    }

    /**
     * @param array<mixed, mixed> $texts
     */
    private static function isSafeTextList(array $texts): bool
    {
        foreach ($texts as $text) {
            if (null === $text || is_bool($text) || is_int($text) || is_float($text)) {
                continue;
            }
            if (!is_string($text) || !ExtraPropertyValidator::isSafeDisplayText($text)) {
                return false;
            }
        }

        return true;
    }

    /**
     * The reason an external_link value is refused, or null: the link needs its "href" and "text",
     * the href must be a safe URL, the text a plain display text, and no other key is accepted.
     */
    private static function externalLinkError(mixed $value): ?string
    {
        if (!is_array($value) || !isset($value['href'], $value['text'])) {
            return 'Form option "external_link" must be an object with at least "href" and "text".';
        }
        foreach ($value as $key => $item) {
            $rule = is_string($key) ? (self::EXTERNAL_LINK_KEYS[$key] ?? null) : null;
            $valid = match ($rule) {
                'url' => is_string($item) && ExtraPropertyValidator::isSafeUrl($item),
                'text' => is_string($item) && ExtraPropertyValidator::isSafeDisplayText($item),
                'bool' => is_bool($item),
                null => false,
                default => is_string($item) && in_array($item, explode('|', $rule), true),
            };
            if (!$valid) {
                return sprintf(
                    'Key "%s" of form option "external_link" is not allowed: "href" must be an http(s) URL or a root-relative path, "text" a plain text, "align" left|right, "position" append|prepend, "open_in_new_tab" a boolean.',
                    (string) $key
                );
            }
        }

        return null;
    }

    /**
     * The reason a known scalar option's value is refused, or null when it satisfies its rule
     * (or when the option is not a known one).
     */
    private static function knownOptionValueError(string $option, mixed $value): ?string
    {
        $rule = self::OPTION_VALUE_RULES[$option] ?? null;
        if (null === $rule) {
            return null;
        }
        if (!in_array(gettype($value), $rule['types'], true)) {
            return sprintf('Form option "%s" must be of type %s.', $option, implode(' or ', $rule['types']));
        }
        if (isset($rule['values']) && !in_array($value, $rule['values'], true)) {
            return sprintf('Form option "%s" must be one of: %s.', $option, implode(', ', $rule['values']));
        }

        return null;
    }
}
