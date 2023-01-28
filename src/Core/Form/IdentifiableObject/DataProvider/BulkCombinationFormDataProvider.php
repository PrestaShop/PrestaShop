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
