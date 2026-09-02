<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Core\ExtraProperty\Form;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\ExtraProperty\Definition\ExtraPropertyScope;
use PrestaShop\PrestaShop\Core\ExtraProperty\Definition\ExtraPropertyType;
use PrestaShop\PrestaShop\Core\ExtraProperty\Form\ExtraPropertyFormTypeMap;
use PrestaShop\PrestaShop\Core\ExtraProperty\Form\FormOptionsValidator;
use PrestaShopBundle\Form\Admin\Type\DatePickerType;
use PrestaShopBundle\Form\Admin\Type\TranslatableType;
use stdClass;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Validator\ValidatorExtension;
use Symfony\Component\Form\Forms;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Validator\Validation;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * The factory intentionally mirrors production capabilities without the full DI container:
 * types resolvable by "new $fqcn()" (TextType, SwitchType, IntegerType...) work out of the box,
 * TranslatableType (needed by the LANG wrapping path) is registered manually, and DI-dependent
 * types (DatePickerType, FormattedTextareaType) stay unresolvable — which is exactly what the
 * generic Throwable catch is for.
 */
class FormOptionsValidatorTest extends TestCase
{
    public function testNothingDeclaredNeedsNoValidation(): void
    {
        $validator = $this->buildValidator();

        $this->assertSame([], $validator->validate(null, ExtraPropertyType::STRING, null, ExtraPropertyScope::COMMON, null));
        $this->assertSame([], $validator->validate(null, ExtraPropertyType::STRING, null, ExtraPropertyScope::COMMON, []));
    }

    public function testValidOptionsOnMappedDefaultTypePass(): void
    {
        $errors = $this->buildValidator()->validate(
            null,
            ExtraPropertyType::STRING,
            null,
            ExtraPropertyScope::COMMON,
            ['attr' => ['class' => 'custom-class'], 'trim' => false]
        );

        $this->assertSame([], $errors);
    }

    public function testValidOptionsOnChoiceTypeWithEnumValuesPass(): void
    {
        $errors = $this->buildValidator()->validate(
            null,
            ExtraPropertyType::CHOICE,
            ['small', 'large'],
            ExtraPropertyScope::COMMON,
            ['expanded' => true]
        );

        $this->assertSame([], $errors);
    }

    public function testUnknownOptionKeyIsRejected(): void
    {
        $errors = $this->buildValidator()->validate(
            null,
            ExtraPropertyType::STRING,
            null,
            ExtraPropertyScope::COMMON,
            ['not_a_real_option' => true]
        );

        $this->assertCount(1, $errors);
        $this->assertStringContainsString('not_a_real_option', $errors[0]);
    }

    public function testInvalidOptionValueTypeIsRejected(): void
    {
        // 'attr' must be an array: SwitchType (mapped default for BOOL) inherits the core rule.
        $errors = $this->buildValidator()->validate(
            null,
            ExtraPropertyType::BOOL,
            null,
            ExtraPropertyScope::COMMON,
            ['attr' => 'not-an-array']
        );

        $this->assertCount(1, $errors);
        $this->assertStringContainsString('attr', $errors[0]);
    }

    /**
     * @dataProvider notAFormTypeProvider
     */
    public function testFqcnThatIsNotAFormTypeIsRejected(string $formTypeFqcn): void
    {
        $errors = $this->buildValidator()->validate(
            $formTypeFqcn,
            ExtraPropertyType::STRING,
            null,
            ExtraPropertyScope::COMMON,
            null
        );

        $this->assertCount(1, $errors);
        $this->assertStringContainsString('is not a Symfony form type', $errors[0]);
        $this->assertStringContainsString($formTypeFqcn, $errors[0]);
    }

    public static function notAFormTypeProvider(): array
    {
        return [
            'existing class that is no form type' => [stdClass::class],
            'unknown class' => ['Vendor\Unknown\FancyType'],
        ];
    }

    public function testExplicitFormTypeWithValidOptionsPasses(): void
    {
        $errors = $this->buildValidator()->validate(
            TextType::class,
            ExtraPropertyType::STRING,
            null,
            ExtraPropertyScope::COMMON,
            ['trim' => false]
        );

        $this->assertSame([], $errors);
    }

    public function testExplicitFormTypeWithUnknownOptionIsRejected(): void
    {
        $errors = $this->buildValidator()->validate(
            TextType::class,
            ExtraPropertyType::STRING,
            null,
            ExtraPropertyScope::COMMON,
            ['scale' => 3]
        );

        $this->assertCount(1, $errors);
        $this->assertStringContainsString('scale', $errors[0]);
    }

    public function testUnbuildableFormTypeIsReportedByTheGenericCatch(): void
    {
        // DatePickerType is a real form type but needs constructor DI, unavailable here:
        // the failure comes from the form factory (Throwable), not the options resolver.
        $errors = $this->buildValidator()->validate(
            DatePickerType::class,
            ExtraPropertyType::DATE,
            null,
            ExtraPropertyScope::COMMON,
            null
        );

        $this->assertCount(1, $errors);
        $this->assertStringContainsString('The form field could not be built with these options', $errors[0]);
    }

    public function testLangScopeValidatesInnerOptionsThroughTranslatableWrapping(): void
    {
        $validator = $this->buildValidator();

        // The wrapped per-language children accept core TextType options...
        $this->assertSame([], $validator->validate(
            null,
            ExtraPropertyType::STRING,
            null,
            ExtraPropertyScope::LANG,
            ['attr' => ['maxlength' => 10]]
        ));

        // ...and reject unknown ones, proving the options reach the inner type.
        $errors = $validator->validate(
            null,
            ExtraPropertyType::STRING,
            null,
            ExtraPropertyScope::LANG,
            ['not_a_real_option' => true]
        );

        $this->assertCount(1, $errors);
        $this->assertStringContainsString('not_a_real_option', $errors[0]);
    }

    private function buildValidator(): FormOptionsValidator
    {
        $locales = [
            ['id_lang' => 1, 'iso_code' => 'en', 'name' => 'English', 'active' => true],
            ['id_lang' => 2, 'iso_code' => 'fr', 'name' => 'Français', 'active' => true],
        ];

        $formFactory = Forms::createFormFactoryBuilder()
            // The modifier merge always carries a 'constraints' option, defined by the validator extension.
            ->addExtension(new ValidatorExtension(Validation::createValidator()))
            ->addType(new TranslatableType(
                $this->createMock(TranslatorInterface::class),
                $locales,
                $locales,
                $this->createMock(UrlGeneratorInterface::class),
                false,
                1,
                1
            ))
            ->getFormFactory();

        return new FormOptionsValidator($formFactory, new ExtraPropertyFormTypeMap());
    }
}
