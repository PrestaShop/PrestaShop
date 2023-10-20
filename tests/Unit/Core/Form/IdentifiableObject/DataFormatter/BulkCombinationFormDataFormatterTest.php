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

namespace Tests\Unit\Core\Form\IdentifiableObject\DataFormatter;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\DataFormatter\BulkCombinationFormDataFormatter;
use PrestaShopBundle\Form\Admin\Extension\DisablingSwitchExtension;

class BulkCombinationFormDataFormatterTest extends TestCase
{
    private const MODIFY_ALL_SHOPS_PREFIX = 'modify_all_shops_';

    /**
     * @dataProvider getDataToFormat
     *
     * @param array $bulkFormData
     * @param array $expectedFormattedData
     */
    public function testFormat(array $bulkFormData, array $expectedFormattedData): void
    {
        $formatter = new BulkCombinationFormDataFormatter(self::MODIFY_ALL_SHOPS_PREFIX);
        $formData = $formatter->format($bulkFormData);
        $this->assertEquals($formData, $expectedFormattedData);
    }

    /**
     * @return iterable
     */
    public function getDataToFormat(): iterable
    {
        yield 'empty data' => [
            [],
            // Formatted data is empty
            [],
        ];

        yield 'no data detected' => [
            [
                'unknown' => 'any value',
            ],
            // Formatted data is empty
            [],
        ];

        yield 'references data' => [
            [
                'references' => [
                    'reference' => 'reference',
                    'mpn' => 'mpn',
                    'upc' => 'upc',
                    'ean_13' => 'ean_13',
                    'isbn' => 'isbn',
                ],
            ],
            // Formatted data has same format for references
            [
                'references' => [
                    'reference' => 'reference',
                    'mpn' => 'mpn',
                    'upc' => 'upc',
                    'ean_13' => 'ean_13',
                    'isbn' => 'isbn',
                ],
            ],
        ];

        yield 'partial references data' => [
            [
                'references' => [
                    'reference' => 'reference',
                    'ean_13' => 'ean_13',
                    'isbn' => 'isbn',
                ],
            ],
            // Formatted data has same format for references
            [
                'references' => [
                    'reference' => 'reference',
                    'ean_13' => 'ean_13',
                    'isbn' => 'isbn',
                ],
            ],
        ];

        yield 'stock data' => [
            [
                'stock' => [
                    'delta_quantity' => [
                        'delta' => 15,
                    ],
                    'minimal_quantity' => 2,
                    'stock_location' => 'far',
                    'low_stock_threshold' => [
                        'threshold_value' => 5,
                        'low_stock_alert' => true,
                    ],
                    'available_date' => '2022-01-15',
                ],
            ],
            [
                'stock' => [
                    'quantities' => [
                        'delta_quantity' => [
                            'delta' => 15,
                        ],
                        'minimal_quantity' => 2,
                    ],
                    'options' => [
                        'stock_location' => 'far',
                        'low_stock_threshold' => 5,
                        sprintf('%slow_stock_threshold', DisablingSwitchExtension::FIELD_PREFIX) => true,
                    ],
                    'available_date' => '2022-01-15',
                ],
            ],
        ];

        yield 'stock data with modify all shops prefix' => [
            [
                'stock' => [
                    'delta_quantity' => [
                        'delta' => 15,
                        self::MODIFY_ALL_SHOPS_PREFIX . 'delta' => true,
                    ],
                    'minimal_quantity' => 2,
                    'stock_location' => 'far',
                    'low_stock_threshold' => [
                        'threshold_value' => 5,
                        self::MODIFY_ALL_SHOPS_PREFIX . 'threshold_value' => true,
                        'low_stock_alert' => true,
                        self::MODIFY_ALL_SHOPS_PREFIX . 'low_stock_alert' => true,
                    ],
                    'available_date' => '2022-01-15',
                ],
            ],
            [
                'stock' => [
                    'quantities' => [
                        'delta_quantity' => [
                            'delta' => 15,
                            self::MODIFY_ALL_SHOPS_PREFIX . 'delta' => true,
                        ],
                        'minimal_quantity' => 2,
                    ],
                    'options' => [
                        'stock_location' => 'far',
                        'low_stock_threshold' => 5,
                        self::MODIFY_ALL_SHOPS_PREFIX . 'low_stock_threshold' => true,
                        sprintf('%slow_stock_threshold', DisablingSwitchExtension::FIELD_PREFIX) => true,
                        sprintf(
                            '%s%slow_stock_threshold',
                            self::MODIFY_ALL_SHOPS_PREFIX,
                            DisablingSwitchExtension::FIELD_PREFIX
                        ) => true,
                    ],
                    'available_date' => '2022-01-15',
                ],
            ],
        ];

        yield 'stock data with fixed quantity' => [
            [
                'stock' => [
                    'fixed_quantity' => 7,
                    'stock_location' => 'close',
                    'low_stock_threshold' => [
                        'threshold_value' => 2,
                        'low_stock_alert' => false,
                    ],
                    'available_date' => '2022-02-15',
                ],
            ],
            [
                'stock' => [
                    'quantities' => [
                        'fixed_quantity' => 7,
                    ],
                    'options' => [
                        'stock_location' => 'close',
                        'low_stock_threshold' => 2,
                        sprintf('%slow_stock_threshold', DisablingSwitchExtension::FIELD_PREFIX) => false,
                    ],
                    'available_date' => '2022-02-15',
                ],
            ],
        ];

        yield 'stock data with fixed quantity and modify all shops prefix' => [
            [
                'stock' => [
                    'fixed_quantity' => 7,
                    self::MODIFY_ALL_SHOPS_PREFIX . 'fixed_quantity' => false,
                    'stock_location' => 'close',
                    'low_stock_threshold' => [
                        'threshold_value' => 2,
                        'low_stock_alert' => false,
                    ],
                    sprintf('%slow_stock_threshold', DisablingSwitchExtension::FIELD_PREFIX) => false,
                    'available_date' => '2022-02-15',
                ],
            ],
            [
                'stock' => [
                    'quantities' => [
                        'fixed_quantity' => 7,
                        self::MODIFY_ALL_SHOPS_PREFIX . 'fixed_quantity' => false,
                    ],
                    'options' => [
                        'stock_location' => 'close',
                        'low_stock_threshold' => 2,
                        sprintf('%slow_stock_threshold', DisablingSwitchExtension::FIELD_PREFIX) => false,
                    ],
                    'available_date' => '2022-02-15',
                ],
            ],
        ];

        yield 'partial stock data with no options' => [
            [
                'stock' => [
                    'delta_quantity' => [
                    ],
                    'minimal_quantity' => 2,
                    'available_date' => '2022-01-15',
                ],
            ],
            [
                'stock' => [
                    'quantities' => [
                        'minimal_quantity' => 2,
                    ],
                    'available_date' => '2022-01-15',
                ],
            ],
        ];

        yield 'partial stock data ith no quantity' => [
            [
                'stock' => [
                    'delta_quantity' => [
                    ],
                    'stock_location' => 'far',
                    'low_stock_threshold' => [
                        'low_stock_alert' => false,
                    ],
                ],
            ],
            [
                'stock' => [
                    'options' => [
                        'stock_location' => 'far',
                        sprintf('%slow_stock_threshold', DisablingSwitchExtension::FIELD_PREFIX) => false,
                    ],
                ],
            ],
        ];

        yield 'price data' => [
            [
                'price' => [
                    'wholesale_price' => 12,
                    'price_tax_excluded' => 10,
                    'price_tax_included' => 18,
                    'unit_price' => 87,
                    'weight' => 45,
                ],
            ],
            [
                'price_impact' => [
                    'wholesale_price' => 12,
                    'price_tax_excluded' => 10,
                    'price_tax_included' => 18,
                    'unit_price' => 87,
                    'weight' => 45,
                ],
            ],
        ];

        yield 'price data with modify all shops prefix' => [
            [
                'price' => [
                    'wholesale_price' => 12,
                    self::MODIFY_ALL_SHOPS_PREFIX . 'wholesale_price' => true,
                    'price_tax_excluded' => 10,
                    self::MODIFY_ALL_SHOPS_PREFIX . 'price_tax_excluded' => true,
                    'price_tax_included' => 18,
                    'unit_price' => 87,
                    self::MODIFY_ALL_SHOPS_PREFIX . 'unit_price' => false,
                    'weight' => 45,
                    self::MODIFY_ALL_SHOPS_PREFIX . 'weight' => false,
                ],
            ],
            [
                'price_impact' => [
                    'wholesale_price' => 12,
                    self::MODIFY_ALL_SHOPS_PREFIX . 'wholesale_price' => true,
                    'price_tax_excluded' => 10,
                    self::MODIFY_ALL_SHOPS_PREFIX . 'price_tax_excluded' => true,
                    'price_tax_included' => 18,
                    'unit_price' => 87,
                    self::MODIFY_ALL_SHOPS_PREFIX . 'unit_price' => false,
                    'weight' => 45,
                    self::MODIFY_ALL_SHOPS_PREFIX . 'weight' => false,
                ],
            ],
        ];

        yield 'partial price data' => [
            [
                'price' => [
                    'wholesale_price' => 12,
                    'price_tax_included' => 18,
                    'unit_price' => 87,
                ],
            ],
            [
                'price_impact' => [
                    'wholesale_price' => 12,
                    'price_tax_included' => 18,
                    'unit_price' => 87,
                ],
            ],
        ];

        yield 'images selected, but disabling switch is not provided' => [
            [
                'images' => [
                    'images' => [1, 3, 5],
                ],
            ],
            [],
        ];

        yield 'images selected and disabling switch is true' => [
            [
                'images' => [
                    'images' => [1, 3, 5],
                    'disabling_switch_images' => true,
                ],
            ],
            [
                'images' => [1, 3, 5],
            ],
        ];

        yield 'images selected, but disabling switch is false' => [
            [
                'images' => [
                    'images' => [1, 3, 5],
                    'disabling_switch_images' => false,
                ],
            ],
            [],
        ];

        yield 'all images unselected, disabling switch not provided' => [
            [
                'images' => [
                    'images' => [],
                ],
            ],
            [],
        ];

        yield 'all images unselected and disabling switch is true' => [
            [
                'images' => [
                    'disabling_switch_images' => true,
                    'images' => [],
                ],
            ],
            [
                'images' => [],
            ],
        ];

        yield 'all images unselected, but disabling switch is false' => [
            [
                'images' => [
                    'disabling_switch_images' => false,
                    'images' => [],
                ],
            ],
            [],
        ];

        yield 'empty images data, disabling switch not provided' => [
            [
                'images' => [],
            ],
            [],
        ];

        yield 'empty images data and disabling switch is true' => [
            [
                'images' => [
                    'disabling_switch_images' => true,
                ],
            ],
            [
                'images' => [],
            ],
        ];

        yield 'empty images data, but disabling switch is false' => [
            [
                'images' => [
                    'disabling_switch_images' => false,
                ],
            ],
            [],
        ];
    }
}
