<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Product\Search;

use Context;

class ProductSearchContext
{
    /**
     * @var int the Shop id
     */
    private $idShop;

    /**
     * @var int the Language id
     */
    private $idLang;

    /**
     * @var int the Currency id
     */
    private $idCurrency;

    /**
     * @var int the Customer id
     */
    private $idCustomer;

    /**
     * @var int the Shop Group id
     */
    private $idShopGroup;

    /**
     * @var bool if the sharing stock is enable
     */
    private $stockSharingBetweenShopGroupEnabled = false;

    public function __construct(?Context $context = null)
    {
        if ($context) {
            $shopGroup = $context->shop->getGroup();

            $this->idShop = $context->shop->id;
            $this->idShopGroup = $shopGroup->id;
            $this->stockSharingBetweenShopGroupEnabled = (bool) $shopGroup->share_stock;
            $this->idLang = $context->language->id;
            $this->idCurrency = $context->currency->id;
            $this->idCustomer = $context->customer->id;
        }
    }

    /**
     * @param int $idShop
     *
     * @return self
     */
    public function setIdShop($idShop)
    {
        $this->idShop = $idShop;

        return $this;
    }

    /**
     * @return int the Product Search Shop id
     */
    public function getIdShop()
    {
        return $this->idShop;
    }

    /**
     * @param int $idLang
     *
     * @return self
     */
    public function setIdLang($idLang)
    {
        $this->idLang = $idLang;

        return $this;
    }

    /**
     * @return int the Product Search Language id
     */
    public function getIdLang()
    {
        return $this->idLang;
    }

    /**
     * @param int $idCurrency
     *
     * @return self
     */
    public function setIdCurrency($idCurrency)
    {
        $this->idCurrency = $idCurrency;

        return $this;
    }

    /**
     * @return int the Product Search Currency id
     */
    public function getIdCurrency()
    {
        return $this->idCurrency;
    }

    /**
     * @param int $idCustomer
     *
     * @return self
     */
    public function setIdCustomer($idCustomer)
    {
        $this->idCustomer = $idCustomer;

        return $this;
    }

    /**
     * @return int the Product Search Customer id
     */
    public function getIdCustomer()
    {
        return $this->idCustomer;
    }

    /**
     * @return int the Shop Group Iid
     */
    public function getIdShopGroup(): int
    {
        return $this->idShopGroup;
    }

    /**
     * @param int $idShopGroup
     *
     * @return self
     */
    public function setIdShopGroup(int $idShopGroup): self
    {
        $this->idShopGroup = $idShopGroup;

        return $this;
    }

    /**
     * @return bool if sharing stock is enable
     */
    public function isStockSharingBetweenShopGroupEnabled(): bool
    {
        return $this->stockSharingBetweenShopGroupEnabled;
    }

    /**
     * @param bool $stockSharingBetweenShopGroupEnabled
     *
     * @return self
     */
    public function setStockSharingBetweenShopGroupEnabled(bool $stockSharingBetweenShopGroupEnabled): self
    {
        $this->stockSharingBetweenShopGroupEnabled = $stockSharingBetweenShopGroupEnabled;

        return $this;
    }
}
