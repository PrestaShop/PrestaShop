<?php
/**
 * 2007-2019 PrestaShop SA and Contributors
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
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Adapter\MailTemplate;

use PrestaShop\PrestaShop\Adapter\LegacyContext;
use PrestaShop\PrestaShop\Core\ConfigurationInterface;
use PrestaShop\PrestaShop\Core\Employee\ContextEmployeeProviderInterface;
use PrestaShop\PrestaShop\Core\Localization\Locale;
use PrestaShop\PrestaShop\Core\Localization\Locale\Repository as LocaleRepository;
use PrestaShop\PrestaShop\Core\MailTemplate\Layout\LayoutInterface;
use Address;
use AddressFormat;
use Carrier;
use Cart;
use Context;
use Order;
use Product;
use Tools;

/**
 * Class MailPreviewVariablesBuilder is used to build fake (but realistic) template variables to preview email templates.
 */
final class MailPreviewVariablesBuilder
{
    const ORDER_CONFIRMATION = 'order_conf';

    const EMAIL_ALERTS_MODULE = 'ps_emailalerts';
    const NEW_ORDER = 'new_order';
    const RETURN_SLIP = 'return_slip';

    /** @var ConfigurationInterface */
    private $configuration;

    /** @var LegacyContext */
    private $legacyContext;

    /**
     * @var Locale
     */
    private $locale;

    /** @var ContextEmployeeProviderInterface */
    private $employeeProvider;

    /** @var Context */
    private $context;

    /** @var MailPartialTemplateRenderer */
    private $mailPartialTemplateRenderer;

    public function __construct(
        ConfigurationInterface $configuration,
        LegacyContext $legacyContext,
        ContextEmployeeProviderInterface $employeeProvider,
        MailPartialTemplateRenderer $mailPartialTemplateRenderer,
        LocaleRepository $repository
    ) {
        $this->configuration = $configuration;
        $this->legacyContext = $legacyContext;
        $this->context = $this->legacyContext->getContext();
        $this->employeeProvider = $employeeProvider;
        $this->mailPartialTemplateRenderer = $mailPartialTemplateRenderer;
        $this->locale = $repository->getLocale(
            $this->context->language->getLocale()
        );
    }

    /**
     * @param LayoutInterface $mailLayout
     *
     * @return array
     *
     * @throws \SmartyException
     */
    public function buildTemplateVariables(LayoutInterface $mailLayout)
    {
        $imageDir = $this->configuration->get('_PS_IMG_DIR_');
        $baseUrl = $this->context->link->getBaseLink();

        //Logo url
        $logoMail = $this->configuration->get('PS_LOGO_MAIL');
        $logo = $this->configuration->get('PS_LOGO');
        if (!empty($logoMail) && file_exists($imageDir . $logoMail)) {
            $templateVars['{shop_logo}'] = $baseUrl . 'img/' . $logoMail;
        } else {
            if (!empty($logo) && file_exists($imageDir . $logo)) {
                $templateVars['{shop_logo}'] = $baseUrl . 'img/' . $logo;
            } else {
                $templateVars['{shop_logo}'] = '';
            }
        }

        $employeeData = $this->employeeProvider->getData();

        $templateVars['{firstname}'] = $employeeData['firstname'];
        $templateVars['{lastname}'] = $employeeData['lastname'];
        $templateVars['{email}'] = $employeeData['email'];
        $templateVars['{shop_name}'] = $this->context->shop->name;
        $templateVars['{shop_url}'] = $this->context->link->getPageLink('index', true);
        $templateVars['{my_account_url}'] = $this->context->link->getPageLink('my-account', true);
        $templateVars['{guest_tracking_url}'] = $this->context->link->getPageLink('guest-tracking', true);
        $templateVars['{history_url}'] = $this->context->link->getPageLink('history', true);
        $templateVars['{color}'] = $this->configuration->get('PS_MAIL_COLOR');
        $templateVars = array_merge($templateVars, $this->buildOrderVariables($mailLayout));

        return $templateVars;
    }

    /**
     * @return array
     *
     * @throws \PrestaShopException
     * @throws \PrestaShop\PrestaShop\Core\Localization\Exception\LocalizationException
     * @throws \SmartyException
     */
    private function buildOrderVariables(LayoutInterface $mailLayout)
    {
        $orders = Order::getOrdersWithInformations(1);
        $order = new Order($orders[0]['id_order']);

        if (self::ORDER_CONFIRMATION == $mailLayout->getName()) {
            $productTemplateList = $this->getProductList($order);
            $productListTxt = $this->mailPartialTemplateRenderer->render('order_conf_product_list.txt', $this->context->language, $productTemplateList);
            $productListHtml = $this->mailPartialTemplateRenderer->render('order_conf_product_list.tpl', $this->context->language, $productTemplateList);

            $cartRulesList[] = array(
                'voucher_name' => 'Promo code',
                'voucher_reduction' => '-' . $this->locale->formatPrice(5, $this->context->currency->iso_code),
            );
            $cartRulesListTxt = $this->mailPartialTemplateRenderer->render('order_conf_cart_rules.txt', $this->context->language, $cartRulesList);
            $cartRulesListHtml = $this->mailPartialTemplateRenderer->render('order_conf_cart_rules.tpl', $this->context->language, $cartRulesList);

            $productVariables = [
                '{products}' => $productListHtml,
                '{products_txt}' => $productListTxt,
                '{discounts}' => $cartRulesListHtml,
                '{discounts_txt}' => $cartRulesListTxt,
            ];
        } elseif (self::EMAIL_ALERTS_MODULE == $mailLayout->getModuleName() && self::NEW_ORDER == $mailLayout->getName()) {
            $productVariables = [
                '{items}' => $this->getNewOrderItems($order),
            ];
        } elseif (self::EMAIL_ALERTS_MODULE == $mailLayout->getModuleName() && self::RETURN_SLIP == $mailLayout->getName()) {
            $productVariables = [
                '{items}' => $this->getReturnSlipItems($order),
            ];
        } else {
            return [];
        }

        $carrier = new Carrier($order->id_carrier);
        $delivery = new Address($order->id_address_delivery);
        $invoice = new Address($order->id_address_invoice);

        return array_merge($productVariables, [
            '{carrier}' => $carrier->name,
            '{delivery_block_txt}' => $this->getFormatedAddress($delivery, "\n"),
            '{invoice_block_txt}' => $this->getFormatedAddress($invoice, "\n"),
            '{delivery_block_html}' => $this->getFormatedAddress($delivery, '<br />', array(
                'firstname' => '<span style="font-weight:bold;">%s</span>',
                'lastname' => '<span style="font-weight:bold;">%s</span>',
            )),
            '{invoice_block_html}' => $this->getFormatedAddress($invoice, '<br />', array(
                'firstname' => '<span style="font-weight:bold;">%s</span>',
                'lastname' => '<span style="font-weight:bold;">%s</span>',
            )),
            '{date}' => Tools::displayDate($order->date_add, null, 1),
            '{order_name}' => $order->getUniqReference(),
            '{payment}' => Tools::substr($order->payment, 0, 255),
            '{total_products}' => count($order->getProducts()),
            '{total_discounts}' => $this->locale->formatPrice($order->total_discounts, $this->context->currency->iso_code),
            '{total_wrapping}' => $this->locale->formatPrice($order->total_wrapping, $this->context->currency->iso_code),
            '{total_shipping}' => $this->locale->formatPrice($order->total_shipping, $this->context->currency->iso_code),
            '{total_tax_paid}' => $this->locale->formatPrice(($order->total_products_wt - $order->total_products) + ($order->total_shipping_tax_incl - $order->total_shipping_tax_excl), $this->context->currency->iso_code),
            '{total_paid}' => $this->locale->formatPrice($order->total_paid, $this->context->currency->iso_code),
        ]);
    }

    /**
     * @param Order $order
     *
     * @return string
     *
     * @throws \PrestaShopException
     * @throws \PrestaShop\PrestaShop\Core\Localization\Exception\LocalizationException
     */
    private function getNewOrderItems(Order $order)
    {
        $itemsTable = '';

        $products = $order->getProducts();
        $customizedDatas = Product::getAllCustomizedDatas($order->id_cart);
        Product::addCustomizationPrice($products, $customizedDatas);
        foreach ($products as $key => $product) {
            $unitPrice = $product['product_price_wt'];

            $customizationText = '';
            if (isset($customizedDatas[$product['product_id']][$product['product_attribute_id']])) {
                foreach ($customizedDatas[$product['product_id']][$product['product_attribute_id']][$order->id_address_delivery] as $customization) {
                    if (isset($customization['datas'][Product::CUSTOMIZE_TEXTFIELD])) {
                        foreach ($customization['datas'][Product::CUSTOMIZE_TEXTFIELD] as $text) {
                            $customizationText .= $text['name'] . ': ' . $text['value'] . '<br />';
                        }
                    }

                    if (isset($customization['datas'][Product::CUSTOMIZE_FILE])) {
                        $customizationText .= count($customization['datas'][Product::CUSTOMIZE_FILE]) . ' ' . $this->trans('image(s)', array(), 'Modules.Mailalerts.Admin') . '<br />';
                    }

                    $customizationText .= '---<br />';
                }
                if (method_exists('Tools', 'rtrimString')) {
                    $customizationText = Tools::rtrimString($customizationText, '---<br />');
                } else {
                    $customizationText = preg_replace('/---<br \/>$/', '', $customizationText);
                }
            }

            $url = $this->context->link->getProductLink($product['product_id']);
            $itemsTable .=
                '<tr style="background-color:' . ($key % 2 ? '#DDE2E6' : '#EBECEE') . ';">
					<td style="padding:0.6em 0.4em;">' . $product['product_reference'] . '</td>
					<td style="padding:0.6em 0.4em;">
						<strong><a href="' . $url . '">' . $product['product_name'] . '</a>'
                . (isset($product['attributes_small']) ? ' ' . $product['attributes_small'] : '')
                . (!empty($customizationText) ? '<br />' . $customizationText : '')
                . '</strong>
					</td>
					<td style="padding:0.6em 0.4em; text-align:right;">' . $this->locale->formatPrice($unitPrice, $this->context->currency->iso_code) . '</td>
					<td style="padding:0.6em 0.4em; text-align:center;">' . (int) $product['product_quantity'] . '</td>
					<td style="padding:0.6em 0.4em; text-align:right;">'
                . $this->locale->formatPrice(($unitPrice * $product['product_quantity']), $this->context->currency->iso_code)
                . '</td>
				</tr>';
        }
        foreach ($order->getCartRules() as $discount) {
            $itemsTable .=
                '<tr style="background-color:#EBECEE;">
						<td colspan="4" style="padding:0.6em 0.4em; text-align:right;">' . $this->trans('Voucher code:', array(), 'Modules.Mailalerts.Admin') . ' ' . $discount['name'] . '</td>
					<td style="padding:0.6em 0.4em; text-align:right;">-' . $this->locale->formatPrice($discount['value'], $this->context->currency->iso_code) . '</td>
			</tr>';
        }

        return $itemsTable;
    }

    /**
     * @param Order $order
     *
     * @return string
     *
     * @throws \PrestaShopException
     */
    private function getReturnSlipItems(Order $order)
    {
        $itemsTable = '';
        foreach ($order->getCartProducts() as $key => $product) {
            $url = $this->context->link->getProductLink($product['product_id']);
            $itemsTable .=
                '<tr style="background-color:' . ($key % 2 ? '#DDE2E6' : '#EBECEE') . ';">
					<td style="padding:0.6em 0.4em;">' . $product['product_reference'] . '</td>
					<td style="padding:0.6em 0.4em;">
						<strong><a href="' . $url . '">' . $product['product_name'] . '</a>
					</strong>
					</td>
					<td style="padding:0.6em 0.4em; text-align:center;">' . (int) $product['product_quantity'] . '</td>
				</tr>';
        }

        return $itemsTable;
    }

    /**
     * @param Order $order
     *
     * @return array
     *
     * @throws \PrestaShop\PrestaShop\Core\Localization\Exception\LocalizationException
     */
    private function getProductList(Order $order)
    {
        $cart = new Cart($order->id_cart);
        $packageList = $cart->getPackageList();
        $package = current(current($packageList));
        $productList = $package['product_list'];

        $productTemplateList = array();
        foreach ($productList as $product) {
            $price = Product::getPriceStatic((int) $product['id_product'], false, ($product['id_product_attribute'] ? (int) $product['id_product_attribute'] : null), 6, null, false, true, $product['cart_quantity'], false, (int) $order->id_customer, (int) $order->id_cart, (int) $order->{$this->configuration->get('PS_TAX_ADDRESS_TYPE')}, $specific_price, true, true, null, true, $product['id_customization']);
            $priceWithTax = Product::getPriceStatic((int) $product['id_product'], true, ($product['id_product_attribute'] ? (int) $product['id_product_attribute'] : null), 2, null, false, true, $product['cart_quantity'], false, (int) $order->id_customer, (int) $order->id_cart, (int) $order->{$this->configuration->get('PS_TAX_ADDRESS_TYPE')}, $specific_price, true, true, null, true, $product['id_customization']);

            $productPrice = Product::getTaxCalculationMethod() == PS_TAX_EXC ? Tools::ps_round($price, 2) : $priceWithTax;

            $productTemplate = array(
                'id_product' => $product['id_product'],
                'reference' => $product['reference'],
                'name' => $product['name'] . (isset($product['attributes']) ? ' - ' . $product['attributes'] : ''),
                'price' => $this->locale->formatPrice($productPrice * $product['quantity'], $this->context->currency->iso_code),
                'quantity' => $product['quantity'],
                'customization' => array(),
            );

            if (isset($product['price']) && $product['price']) {
                $productTemplate['unit_price'] = $this->locale->formatPrice($productPrice, $this->context->currency->iso_code);
                $productTemplate['unit_price_full'] = $this->locale->formatPrice($productPrice, $this->context->currency->iso_code)
                    . ' ' . $product['unity'];
            } else {
                $productTemplate['unit_price'] = $productTemplate['unit_price_full'] = '';
            }

            $customizedDatas = Product::getAllCustomizedDatas((int) $order->id_cart, null, true, null, (int) $product['id_customization']);
            if (isset($customizedDatas[$product['id_product']][$product['id_product_attribute']])) {
                $productTemplate['customization'] = array();
                foreach ($customizedDatas[$product['id_product']][$product['id_product_attribute']][$order->id_address_delivery] as $customization) {
                    $customizationText = '';
                    if (isset($customization['datas'][Product::CUSTOMIZE_TEXTFIELD])) {
                        foreach ($customization['datas'][Product::CUSTOMIZE_TEXTFIELD] as $text) {
                            $customizationText .= '<strong>' . $text['name'] . '</strong>: ' . $text['value'] . '<br />';
                        }
                    }

                    if (isset($customization['datas'][Product::CUSTOMIZE_FILE])) {
                        $customizationText .= $this->trans('%d image(s)', array(count($customization['datas'][Product::CUSTOMIZE_FILE])), 'Admin.Payment.Notification') . '<br />';
                    }

                    $customizationQuantity = (int) $customization['quantity'];

                    $productTemplate['customization'][] = array(
                        'customization_text' => $customizationText,
                        'customization_quantity' => $customizationQuantity,
                        'quantity' => $this->locale->formatPrice($customizationQuantity * $productPrice, $this->context->currency->iso_code),
                    );
                }
            }
            $productTemplateList[] = $productTemplate;
        }

        return $productTemplateList;
    }

    /**
     * @param Address $address Address $the_address that needs to be txt formated
     * @param string $lineSeparator Line separator
     * @param array $fieldsStyle Associative array to replace styled fields
     *
     * @return string
     */
    private function getFormatedAddress(Address $address, $lineSeparator, $fieldsStyle = array())
    {
        return AddressFormat::generateAddress($address, array('avoid' => array()), $lineSeparator, ' ', $fieldsStyle);
    }
}
