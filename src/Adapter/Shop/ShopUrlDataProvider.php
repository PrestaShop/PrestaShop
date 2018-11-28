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

namespace PrestaShop\PrestaShop\Adapter\Shop;

use PrestaShopException;
use Validate;
use ShopUrl;

/**
 * Class ShopUrlDataProvider is responsible for providing data from shop_url table.
 */
class ShopUrlDataProvider
{
    /**
     * @var int
     */
    private $contextShopId;

    /**
     * ShopUrlDataProvider constructor.
     *
     * @param int $contextShopId
     */
    public function __construct($contextShopId)
    {
        $this->contextShopId = $contextShopId;
    }

    /**
     * Gets main shop url data.
     *
     * @return ShopUrl
     *
     * @throws PrestaShopException
     */
    public function getMainShopUrl()
    {
        /** @var ShopUrl $result */
        $result = ShopUrl::getShopUrls($this->contextShopId)->where('main', '=', 1)->getFirst();

        if (!Validate::isLoadedObject($result)) {
            return new ShopUrl();
        }

        return $result;
    }

    /**
     * Checks whenever the main shop url exists for current shop context.
     *
     * @return bool
     *
     * @throws PrestaShopException
     */
    public function doesMainShopUrlExist()
    {
        $shopUrl = ShopUrl::getShopUrls($this->contextShopId)->where('main', '=', 1)->getFirst();

        return Validate::isLoadedObject($shopUrl);
    }
}
