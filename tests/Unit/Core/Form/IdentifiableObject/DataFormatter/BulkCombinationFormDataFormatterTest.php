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

namespace Tests\Unit\Core\Form\IdentifiableObject\DataFormatter;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\DataFormatter\BulkCombinationFormDataFormatter;

class BulkCombinationFormDataFormatterTest extends TestCase
{
    /**
     * @dataProvider getDataToFormat
     *
     * @param array $bulkFormData
     * @param array $expectedFormattedData
     */
    public function testFormat(array $bulkFormData, array $expectedFormattedData): void
    {
        $formatter = new BulkCombinationFormDataFormatter();
        $formData = $formatter->format($bulkFormData);
        $this->assertEquals($formData, $expectedFormattedData);
    }

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
                    'low_stock_threshold' => 5,
                    'low_stock_alert' => true,
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
                        'low_stock_alert' => true,
                    ],
                    'available_date' => '2022-01-15',
                ],
            ],
        ];

        yield 'partial stock data wit no options' => [
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
                    'low_stock_alert' => true,
                ],
            ],
            [
                'stock' => [
                    'options' => [
                        'stock_location' => 'far',
                        'low_stock_alert' => true,
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

        yield 'images data' => [
            [
                'images' => [
                    'images' => [1, 3, 5],
                ],
            ],
            [
                'images' => [1, 3, 5],
            ],
        ];

        yield 'all images unselected' => [
            [
                'images' => [
                    'images' => [],
                ],
            ],
            [
                'images' => [],
            ],
        ];

        yield 'empty images data' => [
            [
                'images' => [],
            ],
            [],
        ];

        yield 'empty images data, but with disabling_switch on' => [
            [
                'images' => [
                    'disabling_switch_images' => true,
                ],
            ],
            [
                'images' => [],
            ],
        ];
    }
}
