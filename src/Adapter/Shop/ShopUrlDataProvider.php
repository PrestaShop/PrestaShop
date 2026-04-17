<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Shop;

use PrestaShopException;
use ShopUrl;
use Validate;

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
