<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Form\IdentifiableObject\DataProvider;

class BulkCombinationFormDataProvider implements FormDataProviderInterface
{
    /**
     * We return values matching the empty state of each field, this way they will be disabled by default.
     *
     * @return array
     */
    public function getDefaultData()
    {
        return [
            'stock' => [
                'delta_quantity' => [
                    'quantity' => 0,
                    'delta' => 0,
                ],
                'fixed_quantity' => 0,
                'minimal_quantity' => 0,
                'stock_location' => '',
                'low_stock_threshold' => [
                    'threshold_value' => 0,
                    'low_stock_alert' => false,
                ],
                'available_date' => '',
                'available_now_label' => [],
                'available_later_label' => [],
            ],
            'price' => [
                'wholesale_price' => 0,
                'price_tax_excluded' => 0,
                'price_tax_included' => 0,
                'unit_price' => 0,
                'weight' => 0,
            ],
            'references' => [
                'reference' => '',
                'mpn' => '',
                'upc' => '',
                'ean_13' => '',
                'isbn' => '',
            ],
            'images' => [
                'images' => [],
            ],
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function getData($id)
    {
        return [];
    }
}
