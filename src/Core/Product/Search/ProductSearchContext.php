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
     * @var boolean if the sharing stock is enable
     */
    private $stockSharingBetweenShopGroupEnabled = false;

    public function __construct(Context $context = null)
    {
        if ($context) {
            $shopGroup = $context->shop->getGroup();

            $this->idShop = $context->shop->id;
            $this->idShopGroup = $shopGroup->id;
            $this->stockSharingBetweenShopGroupEnabled = boolval($shopGroup->share_stock);
            $this->idLang = $context->language->id;
            $this->idCurrency = $context->currency->id;
            $this->idCustomer = $context->customer->id;
        }
    }

    /**
     * @param int $idShop
     *
     * @return $this
     */
    public function setIdShop(int $idShop): ProductSearchContext
    {
        $this->idShop = $idShop;

        return $this;
    }

    /**
     * @return int the Product Search Shop id
     */
    public function getIdShop(): int
    {
        return $this->idShop;
    }

    /**
     * @param int $idLang
     *
     * @return $this
     */
    public function setIdLang(int $idLang): ProductSearchContext
    {
        $this->idLang = $idLang;

        return $this;
    }

    /**
     * @return int the Product Search Language id
     */
    public function getIdLang(): int
    {
        return $this->idLang;
    }

    /**
     * @param int $idCurrency
     *
     * @return $this
     */
    public function setIdCurrency(int $idCurrency): ProductSearchContext
    {
        $this->idCurrency = $idCurrency;

        return $this;
    }

    /**
     * @return int the Product Search Currency id
     */
    public function getIdCurrency(): int
    {
        return $this->idCurrency;
    }

    /**
     * @param int $idCustomer
     *
     * @return $this
     */
    public function setIdCustomer(int $idCustomer): ProductSearchContext
    {
        $this->idCustomer = $idCustomer;

        return $this;
    }

    /**
     * @return int the Product Search Customer id
     */
    public function getIdCustomer(): int
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
     * @return $this
     */
    public function setIdShopGroup(int $idShopGroup): ProductSearchContext
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
     * @return $this
     */
    public function setStockSharingBetweenShopGroupEnabled(bool $stockSharingBetweenShopGroupEnabled): ProductSearchContext
    {
        $this->stockSharingBetweenShopGroupEnabled = $stockSharingBetweenShopGroupEnabled;

        return $this;
    }
}
