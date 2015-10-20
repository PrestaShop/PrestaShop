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

class OrderReturnControllerCore extends FrontController
{
    public $auth = true;
    public $php_self = 'order-return';
    public $authRedirection = 'order-follow';
    public $ssl = true;

    /**
     * Initialize order return controller
     * @see FrontController::init()
     */
    public function init()
    {
        parent::init();

        $id_order_return = (int)Tools::getValue('id_order_return');

        if (!isset($id_order_return) || !Validate::isUnsignedId($id_order_return)) {
            $this->redirect_after = '404';
            $this->redirect();
        } else {
            $order_return = new OrderReturn((int)$id_order_return);
            if (Validate::isLoadedObject($order_return) && $order_return->id_customer == $this->context->cookie->id_customer) {
                $order = new Order((int)($order_return->id_order));
                if (Validate::isLoadedObject($order)) {
                    $state = new OrderReturnState((int)$order_return->state);

                    if ($order_return->state == 1) {
                        $this->warning[] = $this->l('You must wait for confirmation before returning any merchandise.');
                    }

                    $this->context->smarty->assign(array(
                        'orderRet' => $this->getTemplateVarOrderReturn($order_return),
                        'order' => $order,
                        'state_name' => $state->name[(int)$this->context->language->id],
                        'return_allowed' => false,
                        'products' => $this->getTemplateVarProducts((int)$order_return->id, $order),
                        'returnedCustomizations' => OrderReturn::getReturnedCustomizedProducts((int)$order_return->id_order),
                        'customizedDatas' => Product::getAllCustomizedDatas((int)$order->id_cart)
                    ));
                } else {
                    $this->redirect_after = '404';
                    $this->redirect();
                }
            } else {
                $this->redirect_after = '404';
                $this->redirect();
            }
        }
    }

    /**
     * Assign template vars related to page content
     * @see FrontController::initContent()
     */
    public function initContent()
    {
        parent::initContent();

        $this->context->smarty->assign(array(
            'errors' => $this->errors,
            'nbdaysreturn' => (int)Configuration::get('PS_ORDER_RETURN_NB_DAYS')
        ));
        $this->setTemplate('customer/order-return.tpl');
    }

    public function getTemplateVarOrderReturn($order_return)
    {
        $order_return = $this->objectSerializer->toArray($order_return);

        $order_return['return_number'] = sprintf('%06d', $order_return['id']);
        $order_return['return_date'] = Tools::displayDate($order_return['date_add'], null, false);
        $order_return['return_pdf_url'] = $this->context->link->getPageLink('pdf-order-return', true, null, 'id_order_return='.(int)$order_return['id']);

        return $order_return;
    }

    public function getTemplateVarProducts($order_return_id, $order)
    {
        $products = [];
        $return_products = OrderReturn::getOrdersReturnProducts((int)$order_return_id, $order);

        foreach ($return_products as $id_return_product => $return_product) {
            if (!isset($return_product['deleted'])) {
                $products[$id_return_product] = $return_product;
                $products[$id_return_product]['customizations'] = ($return_product['customizedDatas']) ? $this->getTemplateVarCustomization($return_product) : [];
            }
        }

        return $products;
    }

    public function getTemplateVarCustomization(array $product)
    {
        $product_customizations = [];
        $imageRetriever = new Adapter_ImageRetriever($this->context->link);

        foreach ($product['customizedDatas'] as $byAddress) {
            foreach ($byAddress as $customization) {
                $presentedCustomization = [
                    'quantity'              => $customization['quantity'],
                    'fields'                => [],
                    'id_customization'      => null
                ];

                foreach ($customization['datas'] as $byType) {
                    $field = [];
                    foreach ($byType as $data) {
                        switch ($data['type']) {
                            case Product::CUSTOMIZE_FILE:
                                $field['type'] = 'image';
                                $field['image'] = $imageRetriever->getCustomizationImage(
                                    $data['value']
                                );
                                break;
                            case Product::CUSTOMIZE_TEXTFIELD:
                                $field['type'] = 'text';
                                $field['text'] = $data['value'];
                                break;
                            default:
                                $field['type'] = null;
                        }
                        $field['label'] = $data['name'];
                        $presentedCustomization['id_customization'] = $data['id_customization'];
                    }
                    $presentedCustomization['fields'][] = $field;
                }

                $product_customizations[] = $presentedCustomization;
            }
        }

        return $product_customizations;
    }
}
