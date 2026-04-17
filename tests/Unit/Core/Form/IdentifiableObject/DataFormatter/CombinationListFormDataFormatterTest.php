<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
