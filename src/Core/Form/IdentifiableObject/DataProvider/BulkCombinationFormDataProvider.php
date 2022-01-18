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
    public function getDefaultData()
    {
        //@todo: usually we use this method for creation, but it doesn't mean we cannot use it for this case does it?
        //@todo: need to test if null values actually works for all form types.
        //  (e.g. DeltaQuantityInput might always convert it to 0 which is not good in our case)
        return [
            'stock' => [
                'quantities' => [
                    'quantity' => null,
                    'minimal_quantity' => null,
                ],
                'options' => [
                    'low_stock_threshold' => null,
                    'low_stock_alert' => null,
                ],
                'available_date' => null,
            ],
            'price_impact' => [
                'wholesale_price' => null,
                'price_tax_excluded' => null,
                'price_tax_included' => null,
                'weight' => null,
            ],
            'reference' => null,
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function getData($id)
    {
        //@todo: form is common for all combinations, so data is not needed for single combination right?
        return [];
    }
}
