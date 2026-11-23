<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\PrestaShopBundle\Form\Admin\Sell\Product\Details;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\ConstraintValidator\DefaultLanguageValidator;
use PrestaShop\PrestaShop\Core\Context\LanguageContext;
use PrestaShop\PrestaShop\Core\Domain\Feature\FeatureValueSettings;
use PrestaShopBundle\Form\Admin\Sell\Product\Details\FeatureValueType;
use PrestaShopBundle\Form\Admin\Type\TranslatableType;
use Symfony\Component\Form\Extension\Validator\ValidatorExtension;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\Forms;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Validator\ConstraintValidatorFactory;
use Symfony\Component\Validator\Validation;
use Symfony\Contracts\Translation\TranslatorInterface;

class FeatureValueTypeTest extends TestCase
{
    private const LOCALES = [
        ['id_lang' => 1, 'iso_code' => 'en', 'name' => 'English', 'active' => true],
        ['id_lang' => 2, 'iso_code' => 'fr', 'name' => 'Français', 'active' => true],
    ];

    /**
     * @dataProvider providesCustomValues
     */
    public function testCustomValueLength(bool $isCustom, array $customValue, bool $expectedValid): void
    {
        $form = $this->buildFormFactory()->create(FeatureValueType::class);
        $form->submit([
            'feature_value_id' => '',
            'is_custom' => $isCustom ? '1' : '',
            'custom_value' => $customValue,
        ]);

        $this->assertSame($expectedValid, $form->isValid(), (string) $form->getErrors(true));
    }

    public static function providesCustomValues(): iterable
    {
        $maxLength = str_repeat('a', FeatureValueSettings::VALUE_MAX_LENGTH);
        $tooLong = str_repeat('a', FeatureValueSettings::VALUE_MAX_LENGTH + 1);

        yield 'custom value at the max length' => [true, [1 => $maxLength, 2 => $maxLength], true];
        yield 'custom value too long in the default language' => [true, [1 => $tooLong, 2 => 'ok'], false];
        yield 'custom value too long in another language' => [true, [1 => 'ok', 2 => $tooLong], false];
        yield 'too long value ignored when the value is not custom' => [false, [1 => $tooLong, 2 => $tooLong], true];
    }

    private function buildFormFactory(): FormFactoryInterface
    {
        $translator = $this->createMock(TranslatorInterface::class);
        $languageContext = $this->createMock(LanguageContext::class);
        $languageContext->method('getId')->willReturn(1);
        $languageContext->method('getLocale')->willReturn('en-US');

        $validator = Validation::createValidatorBuilder()
            ->setConstraintValidatorFactory(new ConstraintValidatorFactory([
                DefaultLanguageValidator::class => new DefaultLanguageValidator($languageContext),
            ]))
            ->getValidator();

        return Forms::createFormFactoryBuilder()
            ->addExtension(new ValidatorExtension($validator))
            ->addType(new TranslatableType(
                $translator,
                self::LOCALES,
                self::LOCALES,
                $this->createMock(UrlGeneratorInterface::class),
                false,
                1,
                1
            ))
            ->addType(new FeatureValueType($translator, self::LOCALES))
            ->getFormFactory();
    }
}
