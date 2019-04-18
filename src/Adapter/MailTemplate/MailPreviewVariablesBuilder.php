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
 * needs please refer to http://www.prestashop.com for more information.
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
    /** @var ConfigurationInterface */
    private $configuration;

    /** @var LegacyContext */
    private $legacyContext;

    /** @var ContextEmployeeProviderInterface */
    private $employeeProvider;

    /** @var Context */
    private $context;

    public function __construct(
        ConfigurationInterface $configuration,
        LegacyContext $legacyContext,
        ContextEmployeeProviderInterface $employeeProvider
    ) {
        $this->configuration = $configuration;
        $this->legacyContext = $legacyContext;
        $this->context = $this->legacyContext->getContext();
        $this->employeeProvider = $employeeProvider;
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

        if ('order_conf' == $mailLayout->getName()) {
            $product_var_tpl_list = $this->getProductList($order);
            $product_list_txt = $this->getEmailTemplateContent('order_conf_product_list.txt', $product_var_tpl_list);
            $product_list_html = $this->getEmailTemplateContent('order_conf_product_list.tpl', $product_var_tpl_list);

            $cart_rules_list[] = array(
                'voucher_name' => 'Promo code',
                'voucher_reduction' => '-' . Tools::displayPrice(5, $this->context->currency, false),
            );
            $cart_rules_list_txt = $this->getEmailTemplateContent('order_conf_cart_rules.txt', $cart_rules_list);
            $cart_rules_list_html = $this->getEmailTemplateContent('order_conf_cart_rules.tpl', $cart_rules_list);

            $productVariables = [
                '{products}' => $product_list_html,
                '{products_txt}' => $product_list_txt,
                '{discounts}' => $cart_rules_list_html,
                '{discounts_txt}' => $cart_rules_list_txt,
            ];
        } else if ('ps_emailalerts' == $mailLayout->getModuleName() && 'new_order' == $mailLayout->getName()) {
            $productVariables = [
                '{items}' => $this->getNewOrderItems($order),
            ];
        } else if ('ps_emailalerts' == $mailLayout->getModuleName() && 'return_slip' == $mailLayout->getName()) {
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
            '{total_discounts}' => Tools::displayPrice($order->total_discounts, $this->context->currency, false),
            '{total_wrapping}' => Tools::displayPrice($order->total_wrapping, $this->context->currency, false),
            '{total_shipping}' => Tools::displayPrice($order->total_shipping, $this->context->currency, false),
            '{total_tax_paid}' => Tools::displayPrice(($order->total_products_wt - $order->total_products) + ($order->total_shipping_tax_incl - $order->total_shipping_tax_excl), $this->context->currency, false),
            '{total_paid}' => Tools::displayPrice($order->total_paid, $this->context->currency, false),
        ]);
    }

    /**
     * @param Order $order
     *
     * @return string
     * @throws \PrestaShopException
     * @throws \PrestaShop\PrestaShop\Core\Localization\Exception\LocalizationException
     */
    private function getNewOrderItems(Order $order)
    {
        $items_table = '';

        $products = $order->getProducts();
        $customized_datas = Product::getAllCustomizedDatas($order->id_cart);
        Product::addCustomizationPrice($products, $customized_datas);
        foreach ($products as $key => $product) {
            $unit_price = $product['product_price_wt'];

            $customization_text = '';
            if (isset($customized_datas[$product['product_id']][$product['product_attribute_id']])) {
                foreach ($customized_datas[$product['product_id']][$product['product_attribute_id']][$order->id_address_delivery] as $customization) {
                    if (isset($customization['datas'][Product::CUSTOMIZE_TEXTFIELD])) {
                        foreach ($customization['datas'][Product::CUSTOMIZE_TEXTFIELD] as $text) {
                            $customization_text .= $text['name'].': '.$text['value'].'<br />';
                        }
                    }

                    if (isset($customization['datas'][Product::CUSTOMIZE_FILE])) {
                        $customization_text .= count($customization['datas'][Product::CUSTOMIZE_FILE]).' '.$this->trans('image(s)', array(), 'Modules.Mailalerts.Admin').'<br />';
                    }

                    $customization_text .= '---<br />';
                }
                if (method_exists('Tools', 'rtrimString')) {
                    $customization_text = Tools::rtrimString($customization_text, '---<br />');
                } else {
                    $customization_text = preg_replace('/---<br \/>$/', '', $customization_text);
                }
            }

            $url = $this->context->link->getProductLink($product['product_id']);
            $items_table .=
                '<tr style="background-color:'.($key % 2 ? '#DDE2E6' : '#EBECEE').';">
					<td style="padding:0.6em 0.4em;">'.$product['product_reference'].'</td>
					<td style="padding:0.6em 0.4em;">
						<strong><a href="'.$url.'">'.$product['product_name'].'</a>'
                .(isset($product['attributes_small']) ? ' '.$product['attributes_small'] : '')
                .(!empty($customization_text) ? '<br />'.$customization_text : '')
                .'</strong>
					</td>
					<td style="padding:0.6em 0.4em; text-align:right;">'.Tools::displayPrice($unit_price, $this->context->currency, false).'</td>
					<td style="padding:0.6em 0.4em; text-align:center;">'.(int) $product['product_quantity'].'</td>
					<td style="padding:0.6em 0.4em; text-align:right;">'
                .Tools::displayPrice(($unit_price * $product['product_quantity']), $this->context->currency, false)
                .'</td>
				</tr>';
        }
        foreach ($order->getCartRules() as $discount) {
            $items_table .=
                '<tr style="background-color:#EBECEE;">
						<td colspan="4" style="padding:0.6em 0.4em; text-align:right;">'.$this->trans('Voucher code:', array(), 'Modules.Mailalerts.Admin').' '.$discount['name'].'</td>
					<td style="padding:0.6em 0.4em; text-align:right;">-'.Tools::displayPrice($discount['value'], $this->context->currency, false).'</td>
			</tr>';
        }

        return $items_table;
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
        $items_table = '';
        foreach ($order->getCartProducts() as $key => $product) {
            $url = $this->context->link->getProductLink($product['product_id']);
            $items_table .=
                '<tr style="background-color:'.($key % 2 ? '#DDE2E6' : '#EBECEE').';">
					<td style="padding:0.6em 0.4em;">'.$product['product_reference'].'</td>
					<td style="padding:0.6em 0.4em;">
						<strong><a href="'.$url.'">'.$product['product_name'].'</a>
					</strong>
					</td>
					<td style="padding:0.6em 0.4em; text-align:center;">'.(int) $product['product_quantity'].'</td>
				</tr>';
        }

        return $items_table;
    }

    /**
     * @param Order $order
     *
     * @return array
     * @throws \PrestaShop\PrestaShop\Core\Localization\Exception\LocalizationException
     */
    private function getProductList(Order $order)
    {
        $cart = new Cart($order->id_cart);
        $packageList = $cart->getPackageList();
        $package = current(current($packageList));
        $productList = $package['product_list'];

        $product_var_tpl_list = array();
        foreach ($productList as $product) {
            $price = Product::getPriceStatic((int) $product['id_product'], false, ($product['id_product_attribute'] ? (int) $product['id_product_attribute'] : null), 6, null, false, true, $product['cart_quantity'], false, (int) $order->id_customer, (int) $order->id_cart, (int) $order->{$this->configuration->get('PS_TAX_ADDRESS_TYPE')}, $specific_price, true, true, null, true, $product['id_customization']);
            $price_wt = Product::getPriceStatic((int) $product['id_product'], true, ($product['id_product_attribute'] ? (int) $product['id_product_attribute'] : null), 2, null, false, true, $product['cart_quantity'], false, (int) $order->id_customer, (int) $order->id_cart, (int) $order->{$this->configuration->get('PS_TAX_ADDRESS_TYPE')}, $specific_price, true, true, null, true, $product['id_customization']);

            $product_price = Product::getTaxCalculationMethod() == PS_TAX_EXC ? Tools::ps_round($price, 2) : $price_wt;

            $product_var_tpl = array(
                'id_product' => $product['id_product'],
                'reference' => $product['reference'],
                'name' => $product['name'] . (isset($product['attributes']) ? ' - ' . $product['attributes'] : ''),
                'price' => Tools::displayPrice($product_price * $product['quantity'], $this->context->currency, false),
                'quantity' => $product['quantity'],
                'customization' => array(),
            );

            if (isset($product['price']) && $product['price']) {
                $product_var_tpl['unit_price'] = Tools::displayPrice($product_price, $this->context->currency, false);
                $product_var_tpl['unit_price_full'] = Tools::displayPrice($product_price, $this->context->currency, false)
                    . ' ' . $product['unity'];
            } else {
                $product_var_tpl['unit_price'] = $product_var_tpl['unit_price_full'] = '';
            }

            $customized_datas = Product::getAllCustomizedDatas((int) $order->id_cart, null, true, null, (int) $product['id_customization']);
            if (isset($customized_datas[$product['id_product']][$product['id_product_attribute']])) {
                $product_var_tpl['customization'] = array();
                foreach ($customized_datas[$product['id_product']][$product['id_product_attribute']][$order->id_address_delivery] as $customization) {
                    $customization_text = '';
                    if (isset($customization['datas'][Product::CUSTOMIZE_TEXTFIELD])) {
                        foreach ($customization['datas'][Product::CUSTOMIZE_TEXTFIELD] as $text) {
                            $customization_text .= '<strong>' . $text['name'] . '</strong>: ' . $text['value'] . '<br />';
                        }
                    }

                    if (isset($customization['datas'][Product::CUSTOMIZE_FILE])) {
                        $customization_text .= $this->trans('%d image(s)', array(count($customization['datas'][Product::CUSTOMIZE_FILE])), 'Admin.Payment.Notification') . '<br />';
                    }

                    $customization_quantity = (int) $customization['quantity'];

                    $product_var_tpl['customization'][] = array(
                        'customization_text' => $customization_text,
                        'customization_quantity' => $customization_quantity,
                        'quantity' => Tools::displayPrice($customization_quantity * $product_price, $this->context->currency, false),
                    );
                }
            }
            $product_var_tpl_list[] = $product_var_tpl;
        }

        return $product_var_tpl_list;
    }

    /**
     * Fetch the content of $templateName inside the folder
     * current_theme/mails/current_iso_lang/ if found, otherwise in
     * mails/current_iso_lang.
     *
     * @param string $templateName template name with extension
     * @param array $var sent to smarty as 'list'
     *
     * @return string
     *
     * @throws \SmartyException
     */
    private function getEmailTemplateContent($templateName, $var)
    {
        $pathToFindEmail = array(
            _PS_THEME_DIR_ . 'mails' . DIRECTORY_SEPARATOR . $this->context->language->iso_code . DIRECTORY_SEPARATOR . $templateName,
            _PS_THEME_DIR_ . 'mails' . DIRECTORY_SEPARATOR . 'en' . DIRECTORY_SEPARATOR . $templateName,
            _PS_MAIL_DIR_ . $this->context->language->iso_code . DIRECTORY_SEPARATOR . $templateName,
            _PS_MAIL_DIR_ . 'en' . DIRECTORY_SEPARATOR . $templateName,
        );

        foreach ($pathToFindEmail as $path) {
            if (file_exists($path)) {
                $smarty = $this->context->smarty;
                $smarty->assign('list', $var);

                return $smarty->fetch($path);
            }
        }

        return '';
    }

    /**
     * @param object Address $the_address that needs to be txt formated
     *
     * @return string the txt formated address block
     */
    private function getFormatedAddress(Address $the_address, $line_sep, $fields_style = array())
    {
        return AddressFormat::generateAddress($the_address, array('avoid' => array()), $line_sep, ' ', $fields_style);
    }
}
