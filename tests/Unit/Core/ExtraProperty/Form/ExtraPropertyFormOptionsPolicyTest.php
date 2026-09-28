<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Core\ExtraProperty\Form;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\ExtraProperty\Form\ExtraPropertyFormOptionsPolicy;

/**
 * The policy is the single authority for the form options a definition may declare, and
 * validate() (write) and sanitize() (read) must agree on every option: what one refuses, the
 * other drops.
 */
class ExtraPropertyFormOptionsPolicyTest extends TestCase
{
    public function testUsualAndCustomTypeOptionsPassUntouched(): void
    {
        $options = [
            'attr' => ['class' => 'custom', 'placeholder' => 'Type here', 'maxlength' => 64, 'autofocus' => true, 'data-toggle' => 'x'],
            'expanded' => true,
            'scale' => 2,
            'choices' => ['Small' => 'small', 'Large' => 'large'],
            'trim' => false,
            'translation_domain' => 'Modules.Demoextrafield.Admin',
            // An option of a custom form type: unknown to the policy, passed to the type as-is.
            'supplier_filter' => ['active' => true, 'limit' => 20],
        ];

        $this->assertSame([], ExtraPropertyFormOptionsPolicy::validate($options));
        $this->assertSame(['options' => $options, 'dropped' => []], ExtraPropertyFormOptionsPolicy::sanitize($options));
    }

    /**
     * The displayed options come back with their content checked: plain text, an enumerated
     * value, or a safe URL — enough for a rich field, without markup.
     */
    public function testDisplayedOptionsPassWithSafeContent(): void
    {
        $options = [
            'label_subtitle' => "Shown under the label (it's > 5 chars, éàü)",
            'label_tag_name' => 'h3',
            'label_help_box' => 'Help shown in a popover',
            'label_tab' => 'Tab name',
            'hint' => 'A hint below the field',
            'alert_title' => 'Careful',
            'alert_message' => ['First line', 'Second line'],
            'alert_type' => 'warning',
            'alert_position' => 'prepend',
            'data_list' => ['Paris', 'Lyon', 42],
            'download_url' => 'https://docs.example.com/guide.pdf',
            'external_link' => ['href' => '/admin-dev/index.php/sell/orders', 'text' => 'See orders', 'align' => 'right', 'open_in_new_tab' => false],
        ];

        $this->assertSame([], ExtraPropertyFormOptionsPolicy::validate($options));
        $this->assertSame(['options' => $options, 'dropped' => []], ExtraPropertyFormOptionsPolicy::sanitize($options));
        // A single string alert_message is accepted too.
        $this->assertSame([], ExtraPropertyFormOptionsPolicy::validate(['alert_message' => 'One line']));
    }

    public function testDisplayedOptionsWithUnsafeContentAreRefusedAndDropped(): void
    {
        $options = [
            'label_subtitle' => '<img src=x onerror=alert(1)>',
            'label_tag_name' => 'img src=x onerror=alert(1)',
            'label_help_box' => '<b>x</b>',
            'hint' => '<a href="https://evil">x</a>',
            'alert_title' => "Line\x00break",
            'alert_message' => ['fine', '<script>'],
            'alert_type' => 'info" onmouseover="alert(1)',
            'alert_position' => 'middle',
            'data_list' => ['fine', '<option>'],
            'download_url' => 'javascript:alert(1)',
            'external_link' => ['href' => 'https://ok.example', 'text' => 'ok', 'onclick' => 'alert(1)'],
        ];

        $errors = ExtraPropertyFormOptionsPolicy::validate($options);
        $sanitized = ExtraPropertyFormOptionsPolicy::sanitize($options);

        $this->assertCount(count($options), $errors);
        foreach (array_keys($options) as $option) {
            $this->assertStringContainsString(sprintf('"%s"', $option), implode("\n", $errors));
        }
        $this->assertSame([], $sanitized['options']);
        $this->assertSame(array_keys($options), $sanitized['dropped']);
    }

    /**
     * @dataProvider urlProvider
     */
    public function testLinkTargetsMustBeHttpOrRootRelative(string $url, bool $accepted): void
    {
        $errors = ExtraPropertyFormOptionsPolicy::validate(['download_url' => $url]);
        $linkErrors = ExtraPropertyFormOptionsPolicy::validate(['external_link' => ['href' => $url, 'text' => 'x']]);

        $this->assertSame($accepted, [] === $errors, $url);
        $this->assertSame($accepted, [] === $linkErrors, $url);
    }

    /**
     * @return array<string, array{string, bool}>
     */
    public static function urlProvider(): array
    {
        return [
            'https' => ['https://example.com/a?b=1#c', true],
            'http' => ['http://example.com', true],
            'root-relative' => ['/admin-dev/index.php/sell/orders', true],
            'javascript scheme' => ['javascript:alert(1)', false],
            'data scheme' => ['data:text/html,<script>alert(1)</script>', false],
            'protocol-relative' => ['//evil.example/x', false],
            // Browsers normalise "\" to "/" in special-scheme URLs: "/\evil" is "//evil" in disguise.
            'backslash after the leading slash' => ['/\\evil.example/x', false],
            'backslash inside an absolute URL' => ['https://ok.example/a\\b', false],
            'relative path' => ['orders/list', false],
            'quote inside' => ['https://example.com/" onmouseover="alert(1)', false],
            'whitespace' => ['https://example.com/a b', false],
        ];
    }

    public function testNothingDeclaredIsAccepted(): void
    {
        $this->assertSame([], ExtraPropertyFormOptionsPolicy::validate(null));
        $this->assertSame(['options' => [], 'dropped' => []], ExtraPropertyFormOptionsPolicy::sanitize(null));
    }

    /**
     * A null is not "unset": it overrides the form type's own default (demoextrafield passes
     * 'label_tag_name' => null precisely so its type stops rendering an h3 label), so it must reach
     * the type. Being contentless it cannot inject anything, hence no content rule applies to it.
     */
    public function testNullIsForwardedForAllowedOptions(): void
    {
        $options = ['label_tag_name' => null, 'scale' => null, 'placeholder' => null, 'my_custom_option' => null];

        $this->assertSame([], ExtraPropertyFormOptionsPolicy::validate($options));
        $this->assertSame(['options' => $options, 'dropped' => []], ExtraPropertyFormOptionsPolicy::sanitize($options));
    }

    public function testADeniedOptionIsRefusedEvenWhenNull(): void
    {
        $errors = ExtraPropertyFormOptionsPolicy::validate(['allow_html' => null]);

        $this->assertCount(1, $errors);
        $this->assertStringContainsString('"allow_html"', $errors[0]);
        $this->assertSame(
            ['options' => [], 'dropped' => ['allow_html']],
            ExtraPropertyFormOptionsPolicy::sanitize(['allow_html' => null])
        );
    }

    /**
     * The options that change what the field DOES — switch a value to raw rendering, select
     * rendering blocks, change what the field maps to, load classes or callables — are refused
     * on write and dropped on read whatever their content, whatever the form type.
     */
    public function testDeniedOptionsAreRefusedAndDropped(): void
    {
        $options = [
            'allow_html' => true,
            'label_html' => true,
            'help_html' => true,
            'block_prefix' => 'text_preview',
            'form_theme' => '@Evil/theme.html.twig',
            'label' => 'Label',
            'help' => 'Help',
            'mapped' => true,
            'data' => 'x',
            'constraints' => [],
            'property_path' => 'password',
            'choice_loader' => 'x',
            'entry_type' => 'x',
            'class' => 'x',
            'label_translation_parameters' => ['%name%' => '<a href="https://evil">x</a>'],
            'multistore_configuration_key' => 'PS_SHOP_EMAIL',
        ];

        $errors = ExtraPropertyFormOptionsPolicy::validate($options);
        $sanitized = ExtraPropertyFormOptionsPolicy::sanitize($options);

        $this->assertCount(count($options), $errors);
        foreach (array_keys($options) as $option) {
            $this->assertStringContainsString(sprintf('"%s"', $option), implode("\n", $errors));
        }
        $this->assertSame([], $sanitized['options']);
        $this->assertSame(array_keys($options), $sanitized['dropped']);
    }

    /**
     * Options that only affect the field itself are not denied: a read-only field, or Symfony's
     * own HTML sanitizer on the submitted value, are legitimate choices.
     */
    public function testFieldLocalOptionsAreNotDenied(): void
    {
        $options = ['disabled' => true, 'sanitize_html' => true];

        $this->assertSame([], ExtraPropertyFormOptionsPolicy::validate($options));
        $this->assertSame(['options' => $options, 'dropped' => []], ExtraPropertyFormOptionsPolicy::sanitize($options));
    }

    /**
     * Any attribute name is echoed verbatim by the form theme, so an "on*" attribute is an inline
     * event handler: those, inline styles and URL attributes are refused in EVERY attr bag, and
     * only the offending ones are removed.
     */
    public function testEventHandlerStyleAndUrlAttributesAreRefusedAndDropped(): void
    {
        $options = [
            'attr' => ['class' => 'ok', 'onfocus' => 'fetch("https://evil")', 'onFocus' => 'x', 'style' => 'x', 'data-toggle' => 'y'],
            'label_attr' => ['class' => 'ok', 'onmouseover' => 'x'],
            'row_attr' => ['href' => 'javascript:alert(1)', 'STYLE' => 'x'],
        ];

        $errors = ExtraPropertyFormOptionsPolicy::validate($options);
        $sanitized = ExtraPropertyFormOptionsPolicy::sanitize($options);

        $this->assertCount(6, $errors);
        $this->assertStringContainsString('"onfocus"', implode("\n", $errors));
        $this->assertSame(
            ['attr' => ['class' => 'ok', 'data-toggle' => 'y'], 'label_attr' => ['class' => 'ok'], 'row_attr' => []],
            $sanitized['options']
        );
        $this->assertSame(
            ['attr.onfocus', 'attr.onFocus', 'attr.style', 'label_attr.onmouseover', 'row_attr.href', 'row_attr.STYLE'],
            $sanitized['dropped']
        );
    }

    /**
     * HTML attribute names are case-insensitive, so every spelling of an event handler or of a
     * denied attribute must be refused: the rules compare the lowercased name instead of matching
     * it literally. A case-sensitive check here would leave "onFocus" and "ONFOCUS" live.
     *
     * @dataProvider caseVariantAttributeProvider
     */
    public function testAttributeRulesAreCaseInsensitive(string $attribute): void
    {
        $options = ['attr' => ['class' => 'ok', $attribute => 'alert(1)']];

        $this->assertCount(1, ExtraPropertyFormOptionsPolicy::validate($options), $attribute);
        $this->assertSame(
            ['options' => ['attr' => ['class' => 'ok']], 'dropped' => ['attr.' . $attribute]],
            ExtraPropertyFormOptionsPolicy::sanitize($options),
            $attribute
        );
    }

    /**
     * @return array<string, array{string}>
     */
    public static function caseVariantAttributeProvider(): array
    {
        return [
            // Event handlers.
            'onfocus lowercase' => ['onfocus'],
            'onFocus mixed case' => ['onFocus'],
            'ONFOCUS uppercase' => ['ONFOCUS'],
            'OnClick mixed case' => ['OnClick'],
            'ONMOUSEOVER uppercase' => ['ONMOUSEOVER'],
            // Denied attributes.
            'style lowercase' => ['style'],
            'Style mixed case' => ['Style'],
            'STYLE uppercase' => ['STYLE'],
            'href lowercase' => ['href'],
            'HREF uppercase' => ['HREF'],
            'srcDoc mixed case' => ['srcDoc'],
            'FORMACTION uppercase' => ['FORMACTION'],
            'XLink:Href mixed case' => ['XLink:Href'],
        ];
    }

    /**
     * Option names, unlike attribute names, are matched case-sensitively on purpose: Symfony's
     * options resolver is case-sensitive too, so "ALLOW_HTML" is NOT the denied "allow_html" —
     * it is an undefined option, refused by the form build (see
     * FormOptionsValidatorTest::testACaseVariantOfADeniedOptionIsRefusedByTheFormBuild). Lowercasing
     * option names here would instead risk refusing a legitimate camelCase option of a custom type.
     */
    public function testOptionNamesAreMatchedCaseSensitivelyAndLeftToTheFormBuild(): void
    {
        $this->assertSame([], ExtraPropertyFormOptionsPolicy::validate(['ALLOW_HTML' => true]));
        $this->assertSame(
            ['options' => ['ALLOW_HTML' => true], 'dropped' => []],
            ExtraPropertyFormOptionsPolicy::sanitize(['ALLOW_HTML' => true])
        );
    }

    /**
     * The form theme renders ` {{ attrname }}="{{ attrvalue }}"` and Twig's escaping of the name
     * does not touch whitespace, so a name carrying a space would emit a second, unchecked
     * attribute — and it would not start with "on". The name's shape is checked for that reason.
     *
     * @dataProvider malformedAttributeNameProvider
     */
    public function testMalformedAttributeNamesAreRefusedAndDropped(string $attribute): void
    {
        $options = ['attr' => ['class' => 'ok', $attribute => 'alert(1)']];

        $this->assertCount(1, ExtraPropertyFormOptionsPolicy::validate($options), $attribute);
        $this->assertSame(
            ['options' => ['attr' => ['class' => 'ok']], 'dropped' => ['attr.' . $attribute]],
            ExtraPropertyFormOptionsPolicy::sanitize($options),
            $attribute
        );
    }

    /**
     * @return array<string, array{string}>
     */
    public static function malformedAttributeNameProvider(): array
    {
        return [
            'leading space hides an event handler' => [' onfocus'],
            'inner space adds a second attribute' => ['x onfocus'],
            'leading tab' => ["\tonfocus"],
            'leading newline' => ["\nonfocus"],
            'quote and space' => ['x" onfocus'],
            'equals sign' => ['x=y'],
            'angle bracket' => ['x>y'],
            'slash' => ['x/y'],
            'empty name' => [''],
            'leading digit' => ['1class'],
            'leading hyphen' => ['-class'],
        ];
    }

    public function testWellFormedAttributeNamesStillPass(): void
    {
        $options = ['attr' => [
            'class' => 'a', 'data-toggle' => 'b', 'aria-label' => 'c',
            'maxlength' => 10, 'xlink:title' => 'd', 'my.attr' => 'e', 'my_attr' => 'f',
        ]];

        $this->assertSame([], ExtraPropertyFormOptionsPolicy::validate($options));
        $this->assertSame(['options' => $options, 'dropped' => []], ExtraPropertyFormOptionsPolicy::sanitize($options));
    }

    public function testChoiceLabelsFollowTheDisplayTextRuleAndStringChoicesAreRefused(): void
    {
        $options = ['choices' => ['Fine' => 'a', '<b>Bold</b>' => 'b'], 'preferred_choices' => 'strtoupper'];

        $errors = ExtraPropertyFormOptionsPolicy::validate($options);
        $sanitized = ExtraPropertyFormOptionsPolicy::sanitize($options);

        $this->assertCount(2, $errors);
        $this->assertSame(['choices' => ['Fine' => 'a']], $sanitized['options']);
        $this->assertSame(['choices.<b>Bold</b>', 'preferred_choices'], $sanitized['dropped']);
    }

    /**
     * A wrong-typed or out-of-range value of a KNOWN option would throw in Symfony's options
     * resolver while the entity form is built: refused on write, dropped on read, never a 500.
     */
    public function testWrongTypedAndOutOfRangeKnownValuesAreRefusedAndDropped(): void
    {
        $options = ['scale' => 'abc', 'expanded' => 'yes', 'widget' => 'not_a_widget', 'type' => 'fractional', 'trim' => false];

        $errors = ExtraPropertyFormOptionsPolicy::validate($options);
        $sanitized = ExtraPropertyFormOptionsPolicy::sanitize($options);

        $this->assertCount(3, $errors);
        $this->assertStringContainsString('"scale" must be of type integer', $errors[0]);
        $this->assertStringContainsString('"expanded" must be of type boolean', $errors[1]);
        $this->assertStringContainsString('"widget" must be one of', $errors[2]);
        $this->assertSame(['type' => 'fractional', 'trim' => false], $sanitized['options']);
        $this->assertSame(['scale', 'expanded', 'widget'], $sanitized['dropped']);
    }

    /**
     * translation_domain / choice_translation_domain select the catalogue labels are translated
     * with: a "+intl-icu" domain would route them through the ICU formatter, where a malformed
     * pattern throws on every render of the host form.
     */
    public function testTranslationDomainOptionsFollowTheDomainRule(): void
    {
        foreach (['translation_domain', 'choice_translation_domain'] as $option) {
            $this->assertSame([], ExtraPropertyFormOptionsPolicy::validate([$option => false]), $option);
            $this->assertSame([], ExtraPropertyFormOptionsPolicy::validate([$option => 'Modules.Demoextrafield.Admin']), $option);

            foreach (['Admin.Global+intl-icu', 'messages+intl-icu', 'not a domain', '{'] as $domain) {
                $errors = ExtraPropertyFormOptionsPolicy::validate([$option => $domain]);
                $this->assertCount(1, $errors, $option . ' / ' . $domain);
                $this->assertSame([$option], ExtraPropertyFormOptionsPolicy::sanitize([$option => $domain])['dropped'], $option . ' / ' . $domain);
            }
        }
    }

    /**
     * Write and read are built on the same walk: the number of refusals equals the number of
     * dropped entries, whatever the input.
     */
    public function testValidateAndSanitizeAgree(): void
    {
        $options = [
            'expanded' => true,
            'label_subtitle' => '<x>',
            'hint' => 'fine',
            'attr' => ['class' => 'ok', 'onblur' => 'x'],
            'choices' => ['Ok' => 1, '<i>' => 2],
            'rounding_mode' => 'not an int',
            'my_custom_option' => ['anything' => 'goes'],
            'translation_domain' => 'Bad+intl-icu',
            'download_url' => 'javascript:1',
            'external_link' => ['href' => 'https://ok', 'text' => '<b>'],
        ];

        $this->assertCount(
            count(ExtraPropertyFormOptionsPolicy::sanitize($options)['dropped']),
            ExtraPropertyFormOptionsPolicy::validate($options)
        );
    }
}
