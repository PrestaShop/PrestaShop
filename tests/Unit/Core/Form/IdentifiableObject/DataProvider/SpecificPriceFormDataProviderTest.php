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
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

declare(strict_types=1);

namespace Tests\Unit\Core\Form\IdentifiableObject\DataProvider;

use DateTime;
use Generator;
use PHPUnit\Framework\TestCase;
use PrestaShop\Decimal\DecimalNumber;
use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\ValueObject\NoCombinationId;
use PrestaShop\PrestaShop\Core\Domain\Product\SpecificPrice\QueryResult\CustomerInfo;
use PrestaShop\PrestaShop\Core\Domain\Product\SpecificPrice\QueryResult\SpecificPriceForEditing;
use PrestaShop\PrestaShop\Core\Domain\Product\SpecificPrice\ValueObject\FixedPrice;
use PrestaShop\PrestaShop\Core\Domain\Product\SpecificPrice\ValueObject\InitialPrice;
use PrestaShop\PrestaShop\Core\Domain\ValueObject\Reduction;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\DataProvider\SpecificPriceFormDataProvider;

class SpecificPriceFormDataProviderTest extends TestCase
{
    private const CONTEXT_SHOP_ID = 2;

    public function testGetDefaultData(): void
    {
        $queryBusMock = $this->createMock(CommandBusInterface::class);
        $provider = new SpecificPriceFormDataProvider($queryBusMock, self::CONTEXT_SHOP_ID);

        $expectedDefaultData = [
            'from_quantity' => 1,
            'impact' => [
                'reduction' => [
                    'value' => 0,
                    'type' => Reduction::TYPE_AMOUNT,
                    'include_tax' => true,
                ],
                'fixed_price_tax_excluded' => (float) InitialPrice::INITIAL_PRICE_VALUE,
            ],
            'groups' => [
                'shop_id' => self::CONTEXT_SHOP_ID,
            ],
            'combination_id' => NoCombinationId::NO_COMBINATION_ID,
        ];

        $this->assertEquals($expectedDefaultData, $provider->getDefaultData());
    }

    /**
     * @dataProvider getExpectedData
     *
     * @param SpecificPriceForEditing $specificPriceForEditing
     * @param array $expectedData
     */
    public function testGetData(SpecificPriceForEditing $specificPriceForEditing, array $expectedData): void
    {
        $queryBusMock = $this->createQueryBusMock($specificPriceForEditing);
        $provider = new SpecificPriceFormDataProvider($queryBusMock, self::CONTEXT_SHOP_ID);

        $formData = $provider->getData($specificPriceForEditing->getSpecificPriceId());
        // assertSame is very important here We can't assume null and 0 are the same thing
        $this->assertSame($expectedData, $formData);
    }

    public function getExpectedData(): Generator
    {
        yield [
            new SpecificPriceForEditing(
                500,
                'amount',
                new DecimalNumber('50'),
                false,
                new InitialPrice(),
                1,
                new DateTime('2021-10-20 01:00:00'),
                new DateTime('2021-10-20 08:00:00'),
                10,
                new CustomerInfo(1, 'John', 'Doe', 'pub@prestashop.com'),
                999,
                5,
                6,
                7,
                8
            ),
            [
                'product_id' => 10,
                'groups' => [
                    'currency_id' => 6,
                    'country_id' => 7,
                    'group_id' => 8,
                    'shop_id' => 5,
                ],
                'combination_id' => 999,
                'from_quantity' => 1,
                'date_range' => [
                    'from' => '2021-10-20 01:00:00',
                    'to' => '2021-10-20 08:00:00',
                ],
                'impact' => [
                    'reduction' => [
                        'type' => 'amount',
                        'value' => 50.0,
                        'include_tax' => false,
                    ],
                    'fixed_price_tax_excluded' => -1.0,
                ],
                'customer' => [
                    [
                        'id_customer' => 1,
                        'fullname_and_email' => 'John Doe - pub@prestashop.com',
                    ],
                ],
            ],
        ];

        yield [
            new SpecificPriceForEditing(
                501,
                'percentage',
                new DecimalNumber('20'),
                true,
                new FixedPrice('100'),
                10,
                new DateTime('2021-11-20 01:00:00'),
                new DateTime('2021-11-21 01:00:00'),
                11,
                null,
                null,
                null,
                null,
                null,
                null
            ),
            [
                'product_id' => 11,
                'groups' => [
                    'currency_id' => null,
                    'country_id' => null,
                    'group_id' => null,
                    'shop_id' => null,
                ],
                'combination_id' => null,
                'from_quantity' => 10,
                'date_range' => [
                    'from' => '2021-11-20 01:00:00',
                    'to' => '2021-11-21 01:00:00',
                ],
                'impact' => [
                    'reduction' => [
                        'type' => 'percentage',
                        'value' => 20.0,
                        'include_tax' => true,
                    ],
                    'fixed_price_tax_excluded' => 100.0,
                ],
            ],
        ];
    }

    /**
     * @param SpecificPriceForEditing $specificPriceForEditing
     *
     * @return CommandBusInterface
     */
    private function createQueryBusMock(SpecificPriceForEditing $specificPriceForEditing): CommandBusInterface
    {
        $mock = $this->createMock(CommandBusInterface::class);
        $mock->method('handle')
            ->willReturn($specificPriceForEditing)
        ;

        return $mock;
    }
}
