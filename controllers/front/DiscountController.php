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
class DiscountControllerCore extends FrontController
{
    /** @var bool */
    public $auth = true;
    /** @var string */
    public $php_self = 'discount';
    /** @var string */
    public $authRedirection = 'discount';
    /** @var bool */
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

        $this->context->smarty->assign([
            'cart_rules' => $this->getTemplateVarCartRules(),
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

            if ($voucher['quantity'] === 0 || $voucher['quantity_for_user'] === 0) {
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

        $breadcrumb['links'][] = [
            'title' => $this->trans('Your vouchers', [], 'Shop.Theme.Customeraccount'),
            'url' => $this->context->link->getPageLink('discount'),
        ];

        return $breadcrumb;
    }

    /**
     * @param array $voucher
     *
     * @return mixed
     */
    protected function getCombinableVoucherTranslation(array $voucher)
    {
        if ($voucher['cart_rule_restriction']) {
            $combinableVoucherTranslation = $this->trans('No', [], 'Shop.Theme.Global');
        } else {
            $combinableVoucherTranslation = $this->trans('Yes', [], 'Shop.Theme.Global');
        }

        return $combinableVoucherTranslation;
    }

    /**
     * Formats a value of a voucher with fixed reduction
     *
     * @param bool $hasTaxIncluded
     * @param float $amount
     * @param int $currencyId
     *
     * @return string
     */
    protected function formatReductionAmount(bool $hasTaxIncluded, float $amount, int $currencyId)
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
     * Formats a value of a voucher with percentage reduction
     *
     * @param float $percentage
     *
     * @return string
     */
    protected function formatReductionInPercentage(float $percentage)
    {
        return sprintf('%s%%', $percentage);
    }

    /**
     * Formats all reductions and benefits of a voucher. (One voucher can provide a discount and gift at the same time.)
     *
     * @param array $voucher
     *
     * @return array
     */
    protected function accumulateCartRuleValue(array $voucher)
    {
        $cartRuleValue = [];

        if ($voucher['reduction_percent'] > 0) {
            $cartRuleValue[] = $this->formatReductionInPercentage((float) $voucher['reduction_percent']);
        }

        if ($voucher['reduction_amount'] > 0) {
            $cartRuleValue[] = $this->formatReductionAmount(
                (bool) $voucher['reduction_tax'],
                (float) $voucher['reduction_amount'],
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
     * Prepares a single row of voucher table to show it to customer.
     *
     * @param array $voucher
     *
     * @return array
     */
    protected function buildCartRuleFromVoucher(array $voucher): array
    {
        $voucher['voucher_date'] = Tools::displayDate($voucher['date_to'], false);

        if ((int) $voucher['minimum_amount'] === 0) {
            $voucher['voucher_minimal'] = $this->trans('None', [], 'Shop.Theme.Global');
        } else {
            $voucher['voucher_minimal'] = $this->context->getCurrentLocale()->formatPrice(
                $voucher['minimum_amount'],
                Currency::getIsoCodeById((int) $voucher['minimum_amount_currency'])
            );
        }

        $voucher['voucher_cumulable'] = $this->getCombinableVoucherTranslation($voucher);

        // Get all benefits of this voucher into array (discount, gift, free shipping etc.)
        $cartRuleValues = $this->accumulateCartRuleValue($voucher);

        // And combine them into a string
        // If for some magical reason the voucher has no benefit (should not be achievable in BO), we display a dash
        if (0 === count($cartRuleValues)) {
            $voucher['value'] = '-';
        } else {
            $voucher['value'] = implode(' + ', $cartRuleValues);
        }

        return $voucher;
    }
}
