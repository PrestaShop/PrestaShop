<?php
/**
 * 2007-2018 PrestaShop.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
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
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Adapter\Currency;

use Currency;
use ObjectModel;
use Shop;

/**
 * Class CurrencyManager is responsible for dealing with currency data using legacy classes.
 */
class CurrencyManager
{
    /**
     * Updates currency data after default currency has changed.
     */
    public function updateDefaultCurrency()
    {
        /* Set conversion rate of default currency to 1 */
        ObjectModel::updateMultishopTable('Currency', ['conversion_rate' => 1], 'a.id_currency');

        $tmpContext = Shop::getContext();
        if (Shop::CONTEXT_GROUP == $tmpContext) {
            $tmpShop = Shop::getContextShopGroupID();
        } else {
            $tmpShop = (int) Shop::getContextShopID();
        }

        foreach (Shop::getContextListShopID() as $shopId) {
            Shop::setContext(Shop::CONTEXT_SHOP, (int) $shopId);
            Currency::refreshCurrencies();
        }

        Shop::setContext($tmpContext, $tmpShop);
    }
}
