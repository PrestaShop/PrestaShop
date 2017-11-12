<?php
/**
 * 2007-2017 PrestaShop
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
 * @copyright 2007-2017 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
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
        if (Configuration::isCatalogMode()) {
            Tools::redirect('index.php');
        }

        $cart_rules = $this->getTemplateVarCartRules();

        if (count($cart_rules) <= 0) {
            $this->warning[] = $this->trans('You do not have any vouchers.', array(), 'Shop.Notifications.Warning');
        }

        $this->context->smarty->assign([
            'cart_rules' => $cart_rules,
        ]);

        parent::initContent();
        $this->setTemplate('customer/discount');
    }

    public function getTemplateVarCartRules()
    {
        $cart_rules = [];

        $vouchers = CartRule::getCustomerCartRules(
            $this->context->language->id,
            $this->context->customer->id,
            true,
            false
        );

        foreach ($vouchers as $key => $voucher) {
            $cart_rules[$key] = $voucher;
            $cart_rules[$key]['voucher_date'] = Tools::displayDate($voucher['date_to'], null, false);
            $cart_rules[$key]['voucher_minimal'] = ($voucher['minimum_amount'] > 0) ? Tools::displayPrice($voucher['minimum_amount'], (int)$voucher['minimum_amount_currency']) : $this->trans('None', array(), 'Shop.Theme.Global');
            $cart_rules[$key]['voucher_cumulable'] = $this->getCombinableVoucherTranslation($voucher);
            ;

            $cartRuleValue = $this->accumulateCartRuleValue($voucher);

            if (0 === count($cartRuleValue)) {
                $cart_rules[$key]['value'] = '-';
            } else {
                $cart_rules[$key]['value'] = implode(' + ', $cartRuleValue);
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

    /**
     * @param $voucher
     * @return mixed
     */
    protected function getCombinableVoucherTranslation($voucher)
    {
        if ($voucher['cart_rule_restriction']) {
            $combinableVoucherTranslation = $this->trans('No', array(), 'Shop.Theme.Global');
        } else {
            $combinableVoucherTranslation = $this->trans('Yes', array(), 'Shop.Theme.Global');
        }

        return $combinableVoucherTranslation;
    }

    /**
     * @param $hasTaxIncluded
     * @param $amount
     * @param $currencyId
     * @return string
     */
    protected function formatReductionAmount($hasTaxIncluded, $amount, $currencyId)
    {
        if ($hasTaxIncluded) {
            $taxTranslation = $this->trans('Tax included', array(), 'Shop.Theme.Checkout');
        } else {
            $taxTranslation = $this->trans('Tax excluded', array(), 'Shop.Theme.Checkout');
        };

        return sprintf(
            '%s ' . $taxTranslation,
            Tools::displayPrice($amount, (int) $currencyId)
        );
    }

    /**
     * @param $percentage
     * @return string
     */
    protected function formatReductionInPercentage($percentage)
    {
        return sprintf('%s%%', $percentage);
    }

    /**
     * @param $voucher
     * @return array
     */
    protected function accumulateCartRuleValue($voucher)
    {
        $cartRuleValue = [];

        if ($voucher['reduction_percent'] > 0) {
            $cartRuleValue[] = $this->formatReductionInPercentage($voucher['reduction_percent']);
        }

        if ($voucher['reduction_amount'] > 0) {
            $cartRuleValue[] = $this->formatReductionAmount(
                $voucher['reduction_tax'],
                $voucher['reduction_amount'],
                $voucher['reduction_currency']
            );
        }

        if ($voucher['free_shipping']) {
            $cartRuleValue[] = $this->trans('Free shipping', array(), 'Shop.Theme.Checkout');
        }

        if ($voucher['gift_product'] > 0) {
            $cartRuleValue[] = Product::getProductName(
                $voucher['gift_product'],
                $voucher['gift_product_attribute']
            );
        }

        return $cartRuleValue;
    }
}
