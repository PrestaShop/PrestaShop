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

    public function __construct(Context $context = null)
    {
        if ($context) {
            $this->idShop = $context->shop->id;
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
     * @return $this
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
     * @return $this
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
     * @return $this
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
}
