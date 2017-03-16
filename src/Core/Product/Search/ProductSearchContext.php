<?php
/**
 * 2007-2017 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
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
 * @copyright 2007-2017 PrestaShop SA
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */


namespace PrestaShop\PrestaShop\Core\Product\Search;

use Context;

class ProductSearchContext
{
    private $id_shop;
    private $id_lang;
    private $id_currency;
    private $id_customer;

    public function __construct(Context $context = null)
    {
        if ($context) {
            $this->id_shop = $context->shop->id;
            $this->id_lang = $context->language->id;
            $this->id_currency = $context->currency->id;
            $this->id_customer = $context->customer->id;
        }
    }

    public function setIdShop($id_shop)
    {
        $this->id_shop = $id_shop;
        return $this;
    }

    public function getIdShop()
    {
        return $this->id_shop;
    }

    public function setIdLang($id_lang)
    {
        $this->id_lang = $id_lang;
        return $this;
    }

    public function getIdLang()
    {
        return $this->id_lang;
    }

    public function setIdCurrency($id_currency)
    {
        $this->id_currency = $id_currency;
        return $this;
    }

    public function getIdCurrency()
    {
        return $this->id_currency;
    }

    public function setIdCustomer($id_customer)
    {
        $this->id_customer = $id_customer;
        return $this;
    }

    public function getIdCustomer()
    {
        return $this->id_customer;
    }
}
