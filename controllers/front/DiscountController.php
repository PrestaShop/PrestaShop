<?php
/**
 * 2007-2015 PrestaShop
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
 * @copyright 2007-2015 PrestaShop SA
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

class DiscountControllerCore extends FrontController
{
    public $auth = true;
    public $php_self = 'discount';
    public $authRedirection = 'discount';
    public $ssl = true;

    /**
     * Assign template vars related to page content
     * @see FrontController::initContent()
     */
    public function initContent()
    {
        parent::initContent();

        $cart_rules = $this->getTemplateVarCartRules();

        if (count($cart_rules) <= 0) {
            $this->warning[] = $this->l('You do not have any vouchers.');
        }

        $this->context->smarty->assign([
            'cart_rules' => $cart_rules,
        ]);

        $this->setTemplate('customer/discount.tpl');
    }

    public function getTemplateVarCartRules()
    {
        $cart_rules = [];
        $vouchers = CartRule::getCustomerCartRules($this->context->language->id, $this->context->customer->id, true, false);
        foreach ($vouchers as $key => $voucher) {
            $cart_rules[$key] = $voucher;
            $cart_rules[$key]['voucher_date'] = Tools::displayDate($voucher['date_to'], null, false);
            $cart_rules[$key]['voucher_minimal'] = ($voucher['minimum_amount'] > 0) ? Tools::displayPrice($voucher['minimum_amount'], (int)$voucher['minimum_amount_currency']) : $this->l('None');
            $cart_rules[$key]['voucher_cumulable'] = ($voucher['cumulable']) ? $this->l('Yes') : $this->l('No');
            if ($voucher['id_discount_type'] == 1) {
                $cart_rules[$key]['value'] = sprintf('%s%%', $voucher['value']);
            } elseif ($voucher['id_discount_type'] == 2) {
                $cart_rules[$key]['value'] = sprintf('%s '.($voucher['reduction_tax'] ? $this->l('Tax included') : $this->l('Tax excluded')), Tools::displayPrice($voucher['value'], (int)$voucher['reduction_currency']));
            } elseif ($voucher['id_discount_type'] == 3) {
                $cart_rules[$key]['value'] = $this->l('Free shipping');
            } else {
                $cart_rules[$key]['value'] = '-';
            }
        }

        return $cart_rules;
    }

    public function getBreadcrumbLinks()
    {
        $breadcrumb = parent::getBreadcrumbLinks();

        $breadcrumb['links'][] = $this->addMyAccountToBreadcrumb();

        return $breadcrumb;
    }
}
