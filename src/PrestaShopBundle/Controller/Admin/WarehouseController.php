<?php
/**
 * 2007-2018 PrestaShop.
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
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShopBundle\Controller\Admin;

use Symfony\Component\HttpFoundation\Response;
use PrestaShopBundle\Model\Product\AdminModelAdapter as ProductAdminModelAdapter;

/**
 * Admin controller for warehouse on the /product/form page.
 */
class WarehouseController extends FrameworkBundleAdminController
{
    /**
     * Refresh the WarehouseCombination data for the given product ID.
     *
     * @param int $idProduct
     *
     * @return string|Response
     */
    public function refreshProductWarehouseCombinationFormAction($idProduct)
    {
        $productAdapter = $this->get('prestashop.adapter.data_provider.product');
        $warehouseAdapter = $this->get('prestashop.adapter.data_provider.warehouse');
        $response = new Response();

        //get product and all warehouses
        $product = $productAdapter->getProduct((int) $idProduct);
        if (!is_object($product) || empty($product->id)) {
            $response->setStatusCode(400);

            return $response;
        }
        $warehouses = $warehouseAdapter->getWarehouses();

        $modelMapper = new ProductAdminModelAdapter(
            $product,
            $this->get('prestashop.adapter.legacy.context'),
            $this->get('prestashop.adapter.admin.wrapper.product'),
            $this->get('prestashop.adapter.tools'),
            $this->get('prestashop.adapter.data_provider.product'),
            $this->get('prestashop.adapter.data_provider.supplier'),
            $this->get('prestashop.adapter.data_provider.warehouse'),
            $this->get('prestashop.adapter.data_provider.feature'),
            $this->get('prestashop.adapter.data_provider.pack'),
            $this->get('prestashop.adapter.shop.context'),
            $this->get('prestashop.adapter.data_provider.tax')
        );
        $allFormData = $modelMapper->getFormData();

        $form = $this->createFormBuilder($allFormData);
        $simpleSubForm = $form->create('step4', 'form');

        foreach ($warehouses as $warehouse) {
            $simpleSubForm->add('warehouse_combination_' . $warehouse['id_warehouse'], 'Symfony\Component\Form\Extension\Core\Type\CollectionType', array(
                'entry_type' => 'PrestaShopBundle\Form\Admin\Product\ProductWarehouseCombination',
                'entry_options' => array(
                    'id_warehouse' => $warehouse['id_warehouse'],
                ),
                'allow_add' => true,
                'required' => false,
                'label' => $warehouse['name'],
            ));
        }

        $form->add($simpleSubForm);

        return $this->render('@Product/ProductPage/Forms/form_warehouse_combination.html.twig', array(
            'warehouses' => $warehouses,
            'form' => $form->getForm()['step4']->createView(),
        ));
    }
}
