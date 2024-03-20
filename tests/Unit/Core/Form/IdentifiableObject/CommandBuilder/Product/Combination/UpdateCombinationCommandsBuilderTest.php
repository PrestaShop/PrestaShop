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

namespace Tests\Unit\Core\Form\IdentifiableObject\CommandBuilder\Product\Combination;

use DateTime;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\Command\UpdateCombinationCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Stock\ValueObject\LowStockThreshold;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\CommandBuilder\Product\Combination\UpdateCombinationCommandsBuilder;
use PrestaShop\PrestaShop\Core\Util\DateTime\NullDateTime;
use PrestaShopBundle\Form\Extension\DisablingSwitchExtension;

class UpdateCombinationCommandsBuilderTest extends AbstractCombinationCommandBuilderTest
{
    /**
     * @dataProvider getExpectedCommands
     * @dataProvider getExpectedCommandsMultiShop
     *
     * @param array $formData
     * @param array $expectedCommands
     */
    public function testBuildCommands(array $formData, array $expectedCommands)
    {
        $builder = new UpdateCombinationCommandsBuilder(self::MODIFY_ALL_SHOPS_PREFIX);
        $builtCommands = $builder->buildCommands($this->getCombinationId(), $formData, $this->getSingleShopConstraint());
        $this->assertEquals($expectedCommands, $builtCommands);
    }

    public function getExpectedCommands(): iterable
    {
        yield [
            [
                'no data' => ['useless value'],
            ],
            [],
        ];

        yield [
            [
                'price_impact' => [
                    'not_handled' => 0,
                ],
                'references' => [
                    'not_handled' => 0,
                ],
            ],
            [],
        ];

        // References
        $command = $this->getSingleShopCommand();
        $command->setReference('toto');
        yield [
            [
                'references' => [
                    'reference' => 'toto',
                ],
            ],
            [$command],
        ];

        $command = $this->getSingleShopCommand();
        $command->setUpc('123456');
        $command->setMpn('mpn');
        yield [
            [
                'references' => [
                    'upc' => '123456',
                    'mpn' => 'mpn',
                ],
            ],
            [$command],
        ];

        $command = $this->getSingleShopCommand();
        $command->setIsbn('0-8044-2957-X');
        $command->setGtin('12345678910');
        yield [
            [
                'references' => [
                    'isbn' => '0-8044-2957-X',
                    'ean_13' => '12345678910',
                ],
            ],
            [$command],
        ];

        // Price impact
        $command = $this->getSingleShopCommand();
        $command->setImpactOnWeight('12');
        yield [
            [
                'price_impact' => [
                    'not_handled' => 0,
                    'weight' => 12.0,
                ],
            ],
            [$command],
        ];

        $command = $this->getSingleShopCommand();
        $command->setImpactOnUnitPrice('51.00');
        $command->setWholesalePrice('12.00');
        yield [
            [
                'price_impact' => [
                    'unit_price_tax_excluded' => 51.00,
                    'wholesale_price' => '12.00',
                ],
            ],
            [$command],
        ];

        $command = $this->getSingleShopCommand();
        $command->setEcoTax('42.00');
        $command->setImpactOnPrice('49.00');
        yield [
            [
                'price_impact' => [
                    'ecotax_tax_excluded' => 42.00,
                    'price_tax_excluded' => '49.00',
                ],
            ],
            [$command],
        ];

        // Stock information
        $command = $this->getSingleShopCommand();
        $command->setMinimalQuantity(1);
        yield [
            [
                'stock' => [
                    'quantities' => [
                        'minimal_quantity' => 1,
                    ],
                ],
            ],
            [$command],
        ];

        $command = $this->getSingleShopCommand();
        $command->setLowStockThreshold(5);
        yield [
            [
                'stock' => [
                    'options' => [
                        'low_stock_threshold' => '5',
                    ],
                ],
            ],
            [$command],
        ];

        $command = $this->getSingleShopCommand();
        $command->setLowStockThreshold(LowStockThreshold::DISABLED_VALUE);
        yield [
            [
                'stock' => [
                    'options' => [
                        'disabling_switch_low_stock_threshold' => '0',
                    ],
                ],
            ],
            [$command],
        ];

        $command = $this->getSingleShopCommand();
        $command->setAvailableDate(new DateTime('2022-10-10'));
        yield [
            [
                'stock' => [
                    'available_date' => '2022-10-10',
                ],
            ],
            [$command],
        ];

        $command = $this->getSingleShopCommand();
        $command->setAvailableDate(new NullDateTime());
        yield [
            [
                'stock' => [
                    'available_date' => '',
                ],
            ],
            [$command],
        ];

        $command = $this->getSingleShopCommand();
        $command->setIsDefault(true);
        yield [
            [
                'header' => [
                    'is_default' => true,
                ],
            ],
            [$command],
        ];

        $command = $this->getSingleShopCommand();
        $command->setIsDefault(false);
        yield [
            [
                'header' => [
                    'is_default' => '0',
                ],
            ],
            [$command],
        ];

        $command = $this->getSingleShopCommand();
        $command->setIsDefault(false);
        yield [
            [
                'header' => [
                    'is_default' => null,
                ],
            ],
            [$command],
        ];

        $command = $this->getSingleShopCommand();
        $command->setLowStockThreshold(LowStockThreshold::DISABLED_VALUE);
        yield 'low stock threshold is overriden by disabling switch when it is falsy' => [
            [
                'stock' => [
                    'options' => [
                        sprintf('%slow_stock_threshold', DisablingSwitchExtension::FIELD_PREFIX) => false,
                        'low_stock_threshold' => 7,
                    ],
                ],
            ],
            [$command],
        ];

        $command = $this->getSingleShopCommand();
        $command->setLowStockThreshold(8);
        yield 'low stock threshold is correctly set when disabling switch is truthy' => [
            [
                'stock' => [
                    'options' => [
                        sprintf('%slow_stock_threshold', DisablingSwitchExtension::FIELD_PREFIX) => true,
                        'low_stock_threshold' => 8,
                    ],
                ],
            ],
            [$command],
        ];
    }

    public function getExpectedCommandsMultiShop(): iterable
    {
        // check that references are always updated only as a single shop,
        // even if for some reason there would be all shops prefix in form,
        // because these fields are not multiShop shops
        $singleShopCommand = $this
            ->getSingleShopCommand()
            ->setReference('toto')
            ->setUpc('123456')
            ->setMpn('mpn')
            ->setIsbn('0-8044-2957-X')
            ->setGtin('12345678910')
        ;
        yield [
            [
                'references' => [
                    'reference' => 'toto',
                    self::MODIFY_ALL_SHOPS_PREFIX . 'reference' => true,
                    'upc' => '123456',
                    self::MODIFY_ALL_SHOPS_PREFIX . 'upc' => true,
                    'mpn' => 'mpn',
                    self::MODIFY_ALL_SHOPS_PREFIX . 'mpn' => true,
                    'isbn' => '0-8044-2957-X',
                    self::MODIFY_ALL_SHOPS_PREFIX . 'isbn' => true,
                    'ean_13' => '12345678910',
                    self::MODIFY_ALL_SHOPS_PREFIX . 'ean_13' => true,
                ],
            ],
            [$singleShopCommand],
        ];

        $allShopsCommand = $this
            ->getAllShopsCommand()
            ->setImpactOnWeight('12')
            ->setImpactOnUnitPrice('51.00')
            ->setWholesalePrice('12.00')
            ->setEcoTax('42.00')
            ->setImpactOnPrice('49.00')
        ;

        yield [
            [
                'price_impact' => [
                    'not_handled' => 0,
                    'weight' => 12.0,
                    self::MODIFY_ALL_SHOPS_PREFIX . 'weight' => true,
                    'unit_price_tax_excluded' => 51.00,
                    self::MODIFY_ALL_SHOPS_PREFIX . 'unit_price_tax_excluded' => true,
                    'wholesale_price' => '12.00',
                    self::MODIFY_ALL_SHOPS_PREFIX . 'wholesale_price' => true,
                    'ecotax_tax_excluded' => 42.00,
                    self::MODIFY_ALL_SHOPS_PREFIX . 'ecotax_tax_excluded' => true,
                    'price_tax_excluded' => '49.00',
                    self::MODIFY_ALL_SHOPS_PREFIX . 'price_tax_excluded' => true,
                ],
            ],
            [$allShopsCommand],
        ];

        $singleShopCommand = $this
            ->getSingleShopCommand()
            ->setImpactOnWeight('12')
            ->setImpactOnUnitPrice('51.00')
            ->setWholesalePrice('12.00')

        ;
        $allShopsCommand = $this
            ->getAllShopsCommand()
            ->setEcoTax('42.00')
            ->setImpactOnPrice('49.00')
        ;

        yield [
            [
                'price_impact' => [
                    'not_handled' => 0,
                    'weight' => 12.0,
                    self::MODIFY_ALL_SHOPS_PREFIX . 'weight' => false,
                    'unit_price_tax_excluded' => 51.00,
                    self::MODIFY_ALL_SHOPS_PREFIX . 'unit_price_tax_excluded' => false,
                    'wholesale_price' => '12.00',
                    self::MODIFY_ALL_SHOPS_PREFIX . 'wholesale_price' => false,
                    'ecotax_tax_excluded' => 42.00,
                    self::MODIFY_ALL_SHOPS_PREFIX . 'ecotax_tax_excluded' => true,
                    'price_tax_excluded' => '49.00',
                    self::MODIFY_ALL_SHOPS_PREFIX . 'price_tax_excluded' => true,
                ],
            ],
            [$singleShopCommand, $allShopsCommand],
        ];

        $localizedAvailableNow = [
            1 => 'available',
            2 => 'disponible',
        ];
        $localizedAvailableLater = [
            1 => 'available1',
            2 => 'disponible2',
        ];

        $singleShopCommand = $this
            ->getSingleShopCommand()
            ->setLocalizedAvailableNowLabels($localizedAvailableNow)
            ->setLocalizedAvailableLaterLabels($localizedAvailableLater)
        ;
        $allShopsCommand = $this
            ->getAllShopsCommand()
            ->setMinimalQuantity(1)
            ->setLowStockThreshold(LowStockThreshold::DISABLED_VALUE)
            ->setAvailableDate(new DateTime('2022-10-10'))
        ;
        yield [
            [
                'stock' => [
                    'quantities' => [
                        'minimal_quantity' => 1,
                        self::MODIFY_ALL_SHOPS_PREFIX . 'minimal_quantity' => true,
                    ],
                    'options' => [
                        'low_stock_threshold' => '5',
                        self::MODIFY_ALL_SHOPS_PREFIX . 'low_stock_threshold' => true,
                        'disabling_switch_low_stock_threshold' => '0',
                        self::MODIFY_ALL_SHOPS_PREFIX . 'disabling_switch_low_stock_threshold' => true,
                    ],
                    'available_date' => '2022-10-10',
                    self::MODIFY_ALL_SHOPS_PREFIX . 'available_date' => true,
                    'available_now_label' => $localizedAvailableNow,
                    // even if all shops field is present for some reason,
                    // it still should generate single shop command for this field,
                    // because it is not multiShop field
                    self::MODIFY_ALL_SHOPS_PREFIX . 'available_now_label' => true,
                    'available_later_label' => $localizedAvailableLater,
                ],
            ],
            [$singleShopCommand, $allShopsCommand],
        ];

        $singleShopCommand = $this
            ->getSingleShopCommand()
            ->setMinimalQuantity(1)
            ->setLowStockThreshold(5)
        ;
        $allShopsCommand = $this
            ->getAllShopsCommand()
            ->setLowStockThreshold(LowStockThreshold::DISABLED_VALUE)
            ->setAvailableDate(new NullDateTime())
        ;
        yield [
            [
                'stock' => [
                    'quantities' => [
                        'minimal_quantity' => 1,
                        self::MODIFY_ALL_SHOPS_PREFIX . 'minimal_quantity' => false,
                    ],
                    'options' => [
                        'low_stock_threshold' => '5',
                        self::MODIFY_ALL_SHOPS_PREFIX . 'low_stock_threshold' => false,
                        'disabling_switch_low_stock_threshold' => '0',
                        self::MODIFY_ALL_SHOPS_PREFIX . 'disabling_switch_low_stock_threshold' => true,
                    ],
                    'available_date' => '',
                    self::MODIFY_ALL_SHOPS_PREFIX . 'available_date' => true,
                ],
            ],
            [$singleShopCommand, $allShopsCommand],
        ];

        $allShopsCommand = $this
            ->getAllShopsCommand()
            ->setIsDefault(true)
        ;
        yield [
            [
                'header' => [
                    'is_default' => true,
                    self::MODIFY_ALL_SHOPS_PREFIX . 'is_default' => true,
                ],
            ],
            [$allShopsCommand],
        ];

        $allShopsCommand = $this
            ->getAllShopsCommand()
            ->setIsDefault(false)
        ;
        yield [
            [
                'header' => [
                    'is_default' => false,
                    self::MODIFY_ALL_SHOPS_PREFIX . 'is_default' => true,
                ],
            ],
            [$allShopsCommand],
        ];

        $singleShopCommand = $this
            ->getSingleShopCommand()
            ->setIsDefault(true)
        ;
        yield [
            [
                'header' => [
                    'is_default' => true,
                    self::MODIFY_ALL_SHOPS_PREFIX . 'is_default' => false,
                ],
            ],
            [$singleShopCommand],
        ];

        $singleShopCommand = $this
            ->getSingleShopCommand()
            ->setIsDefault(false)
        ;
        yield [
            [
                'header' => [
                    'is_default' => false,
                    self::MODIFY_ALL_SHOPS_PREFIX . 'is_default' => false,
                ],
            ],
            [$singleShopCommand],
        ];
    }

    private function getSingleShopCommand(): UpdateCombinationCommand
    {
        return new UpdateCombinationCommand($this->getCombinationId()->getValue(), $this->getSingleShopConstraint());
    }

    private function getAllShopsCommand(): UpdateCombinationCommand
    {
        return new UpdateCombinationCommand($this->getCombinationId()->getValue(), ShopConstraint::allShops());
    }
}
