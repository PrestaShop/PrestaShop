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
use PrestaShop\PrestaShop\Adapter\Image\ImageRetriever;
use PrestaShop\PrestaShop\Adapter\Presenter\Order\OrderReturnLazyArray;
use PrestaShop\PrestaShop\Adapter\Presenter\Order\OrderReturnPresenter;

class OrderReturnControllerCore extends FrontController
{
    /** @var bool */
    public $auth = true;
    /** @var string */
    public $php_self = 'order-return';
    /** @var string */
    public $authRedirection = 'order-follow';
    /** @var bool */
    public $ssl = true;

    /**
     * Initialize order return controller.
     *
     * @see FrontController::init()
     */
    public function init()
    {
        parent::init();

        $id_order_return = (int) Tools::getValue('id_order_return');

        if (!Validate::isUnsignedId($id_order_return)) {
            $this->redirect_after = '404';
            $this->redirect();
        } else {
            $order_return = new OrderReturn((int) $id_order_return);
            if (Validate::isLoadedObject($order_return) && $order_return->id_customer == $this->context->cookie->id_customer) {
                $order = new Order((int) ($order_return->id_order));
                if (Validate::isLoadedObject($order)) {
                    if ($order_return->state == 1) {
                        $this->warning[] = $this->trans('You must wait for confirmation before returning any merchandise.', [], 'Shop.Notifications.Warning');
                    }

                    // StarterTheme: Use presenters!
                    $this->context->smarty->assign([
                        'return' => $this->getTemplateVarOrderReturn($order_return),
                        'products' => $this->getTemplateVarProducts((int) $order_return->id, $order),
                    ]);
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
     * Assign template vars related to page content.
     *
     * @see FrontController::initContent()
     */
    public function initContent()
    {
        if (Configuration::isCatalogMode()) {
            Tools::redirect('index.php');
        }

        parent::initContent();
        $this->setTemplate('customer/order-return');
    }

    public function getTemplateVarOrderReturn(OrderReturn $orderReturn)
    {
        $orderReturns = OrderReturn::getOrdersReturn($orderReturn->id_customer, $orderReturn->id_order, false, null, $orderReturn->id);

        if (empty($orderReturns)) {
            return [];
        }

        $orderReturnPresenter = new OrderReturnPresenter(
            Configuration::get('PS_RETURN_PREFIX', $this->context->language->id),
            $this->context->link
        );

        return $orderReturnPresenter->present(array_shift($orderReturns));
    }

    public function getTemplateVarProducts(int $order_return_id, Order $order)
    {
        $products = [];
        $return_products = OrderReturn::getOrdersReturnProducts((int) $order_return_id, $order);

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
        $imageRetriever = new ImageRetriever($this->context->link);

        foreach ($product['customizedDatas'] as $byAddress) {
            foreach ($byAddress as $customization) {
                $presentedCustomization = [
                    'quantity' => $customization['quantity'],
                    'fields' => [],
                    'id_customization' => null,
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
                        $field['id_module'] = $data['id_module'];
                        $presentedCustomization['id_customization'] = $data['id_customization'];
                    }
                    $presentedCustomization['fields'][] = $field;
                }

                $product_customizations[] = $presentedCustomization;
            }
        }

        return $product_customizations;
    }

    public function getBreadcrumbLinks()
    {
        $breadcrumb = parent::getBreadcrumbLinks();

        $breadcrumb['links'][] = $this->addMyAccountToBreadcrumb();

        if (($id_order_return = (int) Tools::getValue('id_order_return')) && Validate::isUnsignedId($id_order_return)) {
            $breadcrumb['links'][] = [
                'title' => $this->trans('Merchandise returns', [], 'Shop.Theme.Global'),
                'url' => $this->context->link->getPageLink('order-follow'),
            ];

            $prefix = Configuration::get('PS_RETURN_PREFIX', $this->context->language->id);
            $orderReturn = new OrderReturn($id_order_return);
            $orderReturn->id_order_return = $id_order_return;
            $orderReturnLazyArray = new OrderReturnLazyArray($prefix, $this->context->link, (array) $orderReturn);
            $orderReturnNumber = $orderReturnLazyArray->getReturnNumber();

            $breadcrumb['links'][] = [
                'title' => $orderReturnNumber,
                'url' => '#',
            ];
        }

        return $breadcrumb;
    }
}
