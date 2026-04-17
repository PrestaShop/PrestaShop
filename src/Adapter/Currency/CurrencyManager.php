<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
        if ($tmpContext == Shop::CONTEXT_GROUP) {
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
