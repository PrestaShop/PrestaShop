<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\PrestaShopBundle\Form\Admin\Improve\International\Tax;

use PrestaShop\PrestaShop\Core\Form\FormChoiceProviderInterface;
use PrestaShop\PrestaShop\Core\Tax\TaxOptionsConfiguration;
use PrestaShopBundle\Form\Admin\Improve\International\Tax\TaxOptionsType;
use PrestaShopBundle\Form\Extension\MultistoreExtension;
use ReflectionClass;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\PreloadedExtension;
use Symfony\Component\Form\Test\TypeTestCase;
use Symfony\Contracts\Translation\TranslatorInterface;

class TaxOptionsTypeTest extends TypeTestCase
{
    /**
     * Each field's multistore key decides which configuration value the back office reads to
     * tell whether the field is overridden for the current shop. A key that does not belong to
     * the field leaves the field disabled, and its override checkbox unticked, while the value
     * the merchant set for the shop is stored and used.
     */
    public function testEveryFieldDeclaresTheConfigurationKeyItReadsAndWrites(): void
    {
        $expectedKeys = [
            'enable_tax' => 'PS_TAX',
            'display_tax_in_cart' => 'PS_TAX_DISPLAY',
            'tax_address_type' => 'PS_TAX_ADDRESS_TYPE',
            'use_eco_tax' => 'PS_USE_ECOTAX',
            'eco_tax_rule_group' => 'PS_ECOTAX_TAX_RULES_GROUP_ID',
        ];

        $form = $this->factory->create(TaxOptionsType::class);

        foreach ($expectedKeys as $field => $expectedKey) {
            $this->assertTrue($form->has($field), sprintf('Field "%s" is missing from the form', $field));
            $this->assertSame(
                $expectedKey,
                $form->get($field)->getConfig()->getOption('multistore_configuration_key'),
                sprintf('Field "%s" declares the wrong multistore configuration key', $field)
            );
        }
    }

    /**
     * The form must cover exactly the fields the configuration object manages, so that a field
     * added on one side without the other is caught here.
     */
    public function testFormCoversEveryConfiguredField(): void
    {
        $form = $this->factory->create(TaxOptionsType::class);

        $formFields = array_keys($form->all());
        sort($formFields);

        $configuredFields = (new ReflectionClass(TaxOptionsConfiguration::class))
            ->getConstant('CONFIGURATION_FIELDS');
        sort($configuredFields);

        $this->assertSame($configuredFields, $formFields);
    }

    protected function getExtensions(): array
    {
        return [
            new PreloadedExtension([$this->createTaxOptionsType()], [
                FormType::class => [new MultistoreExtension()],
            ]),
        ];
    }

    private function createTaxOptionsType(): TaxOptionsType
    {
        $translator = $this->createMock(TranslatorInterface::class);
        $translator->method('trans')->willReturnArgument(0);

        $emptyChoiceProvider = $this->createMock(FormChoiceProviderInterface::class);
        $emptyChoiceProvider->method('getChoices')->willReturn([]);

        return new TaxOptionsType($translator, ['en'], true, $emptyChoiceProvider, $emptyChoiceProvider);
    }
}
