<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

if (!defined('_PS_ADMIN_DIR_')) {
    define('_PS_ADMIN_DIR_', __DIR__);
}
include _PS_ADMIN_DIR_.'/../config/config.inc.php';

if (isset($_GET['secure_key'])) {
    $secureKey = md5(_COOKIE_KEY_.Configuration::get('PS_SHOP_NAME'));
    if ($secureKey === $_GET['secure_key']) {
        $shop_ids = Shop::getCompleteListOfShopsID();
        foreach ($shop_ids as $shop_id) {
            Shop::setContext(Shop::CONTEXT_SHOP, (int)$shop_id);
            Currency::refreshCurrencies();
        }
    }
}
