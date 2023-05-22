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

namespace PrestaShopBundle\Controller\Admin;

use PrestaShop\PrestaShop\Adapter\Product\AdminProductWrapper;
use PrestaShop\PrestaShop\Adapter\Tools;
use PrestaShopBundle\Model\Product\AdminModelAdapter as ProductAdminModelAdapter;
use PrestaShopBundle\Security\Annotation\AdminSecurity;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\HttpFoundation\Response;

/**
 * @deprecated since 8.1 and will be removed in next major.
 *
 * Admin controller for suppliers page.
 */
class SupplierController extends FrameworkBundleAdminController
{
    /**
     * refreshProductSupplierCombinationFormAction.
     *
     * @AdminSecurity("is_granted('create', request.get('_legacy_controller')) || is_granted('update', request.get('_legacy_controller'))")
     *
     * @param int $idProduct
     * @param int|string $supplierIds The suppliers ids separate by "-"
     *
     * @return string|Response
     */
    public function refreshProductSupplierCombinationFormAction($idProduct, $supplierIds)
    {
        $adminProductWrapper = $this->get(AdminProductWrapper::class);
        $productAdapter = $this->get('prestashop.adapter.data_provider.product');
        $response = new Response();

        //get product
        $product = $productAdapter->getProduct((int) $idProduct);

        $suppliers = explode('-', $supplierIds);
        if ($supplierIds == 0) {
            return $response;
        }

        if (!is_object($product) || empty($product->id)) {
            $response->setStatusCode(400);

            return $response;
        }

        //Pre-save of supplier product, needed for well form generation
        $_POST['supplier_loaded'] = 1;
        foreach ($suppliers as $idSupplier) {
            $_POST['check_supplier_' . $idSupplier] = 1;
        }
        $adminProductController = $adminProductWrapper->getInstance();
        $adminProductController->processSuppliers($idProduct);

        $modelMapper = new ProductAdminModelAdapter(
            $this->get('prestashop.adapter.legacy.context'),
            $this->get(AdminProductWrapper::class),
            $this->get(Tools::class),
            $this->get('prestashop.adapter.data_provider.product'),
            $this->get('prestashop.adapter.data_provider.supplier'),
            $this->get('prestashop.adapter.data_provider.warehouse'),
            $this->get('prestashop.adapter.data_provider.feature'),
            $this->get('prestashop.adapter.data_provider.pack'),
            $this->get('prestashop.adapter.shop.context'),
            $this->get('prestashop.adapter.data_provider.tax'),
            $this->get('prestashop.adapter.legacy.configuration'),
            $this->get('router')
        );
        $allFormData = $modelMapper->getFormData($product);

        $form = $this->createFormBuilder($allFormData);
        $simpleSubForm = $form->create('step6', FormType::class);

        foreach ($suppliers as $idSupplier) {
            if ($idSupplier == 0 || !is_numeric($idSupplier)) {
                continue;
            }

            $simpleSubForm->add('supplier_combination_' . $idSupplier, 'Symfony\Component\Form\Extension\Core\Type\CollectionType', [
                'entry_type' => 'PrestaShopBundle\Form\Admin\Product\ProductSupplierCombination',
                'entry_options' => [
                    'id_supplier' => $idSupplier,
                ],
                'prototype' => true,
                'allow_add' => true,
                'required' => false,
                'label' => $this->get('prestashop.adapter.data_provider.supplier')->getNameById($idSupplier),
            ]);
        }

        $form->add($simpleSubForm);

        return $this->render('@Product/ProductPage/Forms/form_supplier_combination.html.twig', [
            'suppliers' => $suppliers,
            'form' => $form->getForm()['step6']->createView(),
        ]);
    }
}
