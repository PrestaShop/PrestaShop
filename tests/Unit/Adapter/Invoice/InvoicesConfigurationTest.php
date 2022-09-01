<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

declare(strict_types=1);

namespace Tests\Unit\Adapter\Invoice;

use PHPUnit\Framework\MockObject\MockObject;
use PrestaShop\PrestaShop\Adapter\Invoice\InvoiceOptionsConfiguration;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use PrestaShop\PrestaShop\Core\Form\FormChoiceProviderInterface;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
use Symfony\Component\OptionsResolver\Exception\UndefinedOptionsException;
use Tests\TestCase\AbstractConfigurationTestCase;

class InvoiceOptionsConfigurationTest extends AbstractConfigurationTestCase
{
    private const SHOP_ID = 42;

    private const VALID_CONFIGURATION = [
        'enable_invoices' => false,
        'enable_tax_breakdown' => true,
        'enable_product_images' => true,
        'invoice_prefix' => ['#EN', '#FR'],
        'add_current_year' => true,
        'reset_number_annually' => true,
        'year_position' => 0,
        'invoice_number' => 42,
        'legal_free_text' => [
            'Legal free text - Test - EN',
            'Legal free text - Test - FR',
        ],
        'footer_text' => [
            'Footer text - Test - EN',
            'Footer text - Test - FR',
        ],
        'invoice_model' => 'invoice',
        'use_disk_cache' => true,
    ];

    /**
     * @var FormChoiceProviderInterface|MockObject
     */
    private $invoiceModelChoiceProvider;

    protected function setUp(): void
    {
        $this->mockConfiguration = $this->createConfigurationMock();
        $this->mockShopConfiguration = $this->createShopContextMock();
        $this->mockMultistoreFeature = $this->createMultistoreFeatureMock();

        $this->invoiceModelChoiceProvider = $this->getMockBuilder(FormChoiceProviderInterface::class)
            ->setMethods(['getChoices'])
            ->getMock()
        ;

        $this->invoiceModelChoiceProvider
            ->method('getChoices')
            ->willReturn([
                'invoice' => 'invoice',
                'invoice-test' => 'invoice-test',
            ])
        ;
    }

    /**
     * @dataProvider provideInvalidConfiguration
     *
     * @param string $exception
     * @param array $values
     */
    public function testUpdateConfigurationWithInvalidConfiguration(string $exception, array $values): void
    {
        $invoiceOptionsConfiguration = new InvoiceOptionsConfiguration($this->mockConfiguration, $this->mockShopConfiguration, $this->mockMultistoreFeature, $this->invoiceModelChoiceProvider);

        $this->expectException($exception);
        $invoiceOptionsConfiguration->updateConfiguration($values);
    }

    /**
     * @return array[]
     */
    public function provideInvalidConfiguration(): array
    {
        return [
            [UndefinedOptionsException::class, ['does_not_exist' => 'does_not_exist']],
            [InvalidOptionsException::class, array_merge(self::VALID_CONFIGURATION, ['enable_invoices' => 'invalid_value_type'])],
            [InvalidOptionsException::class, array_merge(self::VALID_CONFIGURATION, ['enable_tax_breakdown' => 'invalid_value_type'])],
            [InvalidOptionsException::class, array_merge(self::VALID_CONFIGURATION, ['enable_product_images' => 'invalid_value_type'])],
            [InvalidOptionsException::class, array_merge(self::VALID_CONFIGURATION, ['invoice_prefix' => 'invalid_value_type'])],
            [InvalidOptionsException::class, array_merge(self::VALID_CONFIGURATION, ['add_current_year' => 'invalid_value_type'])],
            [InvalidOptionsException::class, array_merge(self::VALID_CONFIGURATION, ['reset_number_annually' => 'invalid_value_type'])],
            [InvalidOptionsException::class, array_merge(self::VALID_CONFIGURATION, ['year_position' => 'invalid_value_type'])],
            [InvalidOptionsException::class, array_merge(self::VALID_CONFIGURATION, ['year_position' => 999])],
            [InvalidOptionsException::class, array_merge(self::VALID_CONFIGURATION, ['invoice_number' => 'invalid_value_type'])],
            [InvalidOptionsException::class, array_merge(self::VALID_CONFIGURATION, ['invoice_number' => 0.42])],
            [InvalidOptionsException::class, array_merge(self::VALID_CONFIGURATION, ['invoice_number' => -42])],
            [InvalidOptionsException::class, array_merge(self::VALID_CONFIGURATION, ['legal_free_text' => 'invalid_value_type'])],
            [InvalidOptionsException::class, array_merge(self::VALID_CONFIGURATION, ['footer_text' => 'invalid_value_type'])],
            [InvalidOptionsException::class, array_merge(self::VALID_CONFIGURATION, ['invoice_model' => null])],
            [InvalidOptionsException::class, array_merge(self::VALID_CONFIGURATION, ['invoice_model' => 'invalid_value'])],
            [InvalidOptionsException::class, array_merge(self::VALID_CONFIGURATION, ['use_disk_cache' => 'invalid_value_type'])],
        ];
    }

    /**
     * @dataProvider provideValidConfiguration
     *
     * @param array $values
     */
    public function testSuccessfulUpdate(array $values): void
    {
        $invoiceOptionsConfiguration = new InvoiceOptionsConfiguration($this->mockConfiguration, $this->mockShopConfiguration, $this->mockMultistoreFeature, $this->invoiceModelChoiceProvider);

        $res = $invoiceOptionsConfiguration->updateConfiguration($values);
        $this->assertSame([], $res);
    }

    /**
     * @return array[]
     */
    public function provideValidConfiguration(): array
    {
        return [
            [self::VALID_CONFIGURATION],
            [array_merge(self::VALID_CONFIGURATION, ['year_position' => 1])],
            [array_merge(self::VALID_CONFIGURATION, ['invoice_number' => 0])],
            [array_merge(self::VALID_CONFIGURATION, ['invoice_model' => 'invoice-test'])],
        ];
    }

    /**
     * @return array[]
     */
    public function provideShopConstraints(): array
    {
        return [
            [ShopConstraint::shop(self::SHOP_ID)],
            [ShopConstraint::shopGroup(self::SHOP_ID)],
            [ShopConstraint::allShops()],
        ];
    }
}
