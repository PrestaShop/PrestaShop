<?php
/**
 * 2007-2020 PrestaShop SA and Contributors
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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2020 PrestaShop SA and Contributors
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
     * Assign template vars related to page content.
     *
     * @see FrontController::initContent()
     */
    public function initContent()
    {
        if (Configuration::isCatalogMode()) {
            Tools::redirect('index.php');
        }

        $cart_rules = $this->getTemplateVarCartRules();

        if (count($cart_rules) <= 0) {
            $this->warning[] = $this->trans('You do not have any vouchers.', [], 'Shop.Notifications.Warning');
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
        $customerId = $this->context->customer->id;
        $languageId = $this->context->language->id;

        $vouchers = CartRule::getCustomerCartRules(
            $languageId,
            $customerId,
            true,
            false
        );

        foreach ($vouchers as $key => $voucher) {
            $voucherCustomerId = (int) $voucher['id_customer'];
            $voucherIsRestrictedToASingleCustomer = ($voucherCustomerId !== 0);

            if ($voucherIsRestrictedToASingleCustomer && $customerId !== $voucherCustomerId) {
                continue;
            }

            $cart_rule = $this->buildCartRuleFromVoucher($voucher);
            $cart_rules[$key] = $cart_rule;
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
     *
     * @return mixed
     */
    protected function getCombinableVoucherTranslation($voucher)
    {
        if ($voucher['cart_rule_restriction']) {
            $combinableVoucherTranslation = $this->trans('No', [], 'Shop.Theme.Global');
        } else {
            $combinableVoucherTranslation = $this->trans('Yes', [], 'Shop.Theme.Global');
        }

        return $combinableVoucherTranslation;
    }

    /**
     * @param $hasTaxIncluded
     * @param $amount
     * @param $currencyId
     *
     * @return string
     */
    protected function formatReductionAmount($hasTaxIncluded, $amount, $currencyId)
    {
        if ($hasTaxIncluded) {
            $taxTranslation = $this->trans('Tax included', [], 'Shop.Theme.Checkout');
        } else {
            $taxTranslation = $this->trans('Tax excluded', [], 'Shop.Theme.Checkout');
        }

        return sprintf(
            '%s ' . $taxTranslation,
            $this->context->getCurrentLocale()->formatPrice($amount, Currency::getIsoCodeById((int) $currencyId))
        );
    }

    /**
     * @param $percentage
     *
     * @return string
     */
    protected function formatReductionInPercentage($percentage)
    {
        return sprintf('%s%%', $percentage);
    }

    /**
     * @param array $voucher
     *
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
            $cartRuleValue[] = $this->trans('Free shipping', [], 'Shop.Theme.Checkout');
        }

        if ($voucher['gift_product'] > 0) {
            $cartRuleValue[] = Product::getProductName(
                $voucher['gift_product'],
                $voucher['gift_product_attribute']
            );
        }

        return $cartRuleValue;
    }

    /**
     * @param array $voucher
     *
     * @return array
     */
    protected function buildCartRuleFromVoucher(array $voucher): array
    {
        $voucher['voucher_date'] = Tools::displayDate($voucher['date_to'], null, false);

        if ((int) $voucher['minimum_amount'] === 0) {
            $voucher['voucher_minimal'] = $this->trans('None', [], 'Shop.Theme.Global');
        } else {
            $voucher['voucher_minimal'] = $this->context->getCurrentLocale()->formatPrice(
                $voucher['minimum_amount'],
                Currency::getIsoCodeById((int) $voucher['minimum_amount_currency'])
            );
        }

        $voucher['voucher_cumulable'] = $this->getCombinableVoucherTranslation($voucher);

        $cartRuleValues = $this->accumulateCartRuleValue($voucher);

        if (0 === count($cartRuleValues)) {
            $voucher['value'] = '-';
        } else {
            $voucher['value'] = implode(' + ', $cartRuleValues);
        }

        return $voucher;
    }
}
