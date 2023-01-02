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
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\DataFormatter\CombinationListFormDataFormatter;

class CombinationListFormDataFormatterTest extends TestCase
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
        $formatter = new CombinationListFormDataFormatter(self::MODIFY_ALL_SHOPS_PREFIX);
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
                'reference' => 'reference',
            ],
            [
                'references' => [
                    'reference' => 'reference',
                ],
            ],
        ];

        yield 'prices data' => [
            [
                'impact_on_price_te' => 42,
                'impact_on_price_ti' => 51,
            ],
            [
                'price_impact' => [
                    'price_tax_excluded' => 42,
                    'price_tax_included' => 51,
                ],
            ],
        ];

        yield 'prices data with modify all shops prefix' => [
            [
                'impact_on_price_te' => 42,
                self::MODIFY_ALL_SHOPS_PREFIX . 'impact_on_price_te' => true,
                'impact_on_price_ti' => 51,
            ],
            [
                'price_impact' => [
                    'price_tax_excluded' => 42,
                    self::MODIFY_ALL_SHOPS_PREFIX . 'price_tax_excluded' => true,
                    'price_tax_included' => 51,
                ],
            ],
        ];

        yield 'quantity data' => [
            [
                'delta_quantity' => [
                    'delta' => -45,
                ],
            ],
            [
                'stock' => [
                    'quantities' => [
                        'delta_quantity' => [
                            'delta' => -45,
                        ],
                    ],
                ],
            ],
        ];

        yield 'quantity data with modify all shops prefix' => [
            [
                'delta_quantity' => [
                    'delta' => -45,
                    self::MODIFY_ALL_SHOPS_PREFIX . 'delta' => false,
                ],
            ],
            [
                'stock' => [
                    'quantities' => [
                        'delta_quantity' => [
                            'delta' => -45,
                            self::MODIFY_ALL_SHOPS_PREFIX . 'delta' => false,
                        ],
                    ],
                ],
            ],
        ];

        yield 'is_default data' => [
            [
                'is_default' => true,
            ],
            [
                'header' => [
                    'is_default' => true,
                ],
            ],
        ];

        yield 'is_default data with modify all shops prefix' => [
            [
                'is_default' => true,
                self::MODIFY_ALL_SHOPS_PREFIX . 'is_default' => false,
            ],
            [
                'header' => [
                    'is_default' => true,
                    self::MODIFY_ALL_SHOPS_PREFIX . 'is_default' => false,
                ],
            ],
        ];
    }
}
