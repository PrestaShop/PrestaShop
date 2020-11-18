<?php
/**
 * 2007-2019 PrestaShop and Contributors
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

namespace PrestaShopBundle\Controller\Admin;

use PrestaShopBundle\Security\Annotation\AdminSecurity;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Admin controller for product attachments (in /product/form page).
 */
class AttachementProductController extends FrameworkBundleAdminController
{
    /**
     * Manage form add product attachment.
     *
     * @AdminSecurity("is_granted(['create', 'update'], 'ADMINPRODUCTS_')")
     *
     * @param int $idProduct
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function addAction($idProduct, Request $request)
    {
        $response = new JsonResponse();
        $legacyContext = $this->get('prestashop.adapter.legacy.context');
        $adminProductWrapper = $this->get('prestashop.adapter.admin.wrapper.product');
        $productAdapter = $this->get('prestashop.adapter.data_provider.product');

        //get product
        $product = $productAdapter->getProduct((int) $idProduct);

        if (!$product || !$request->isXmlHttpRequest()) {
            return $response;
        }

        $form = $this->createForm(
            'PrestaShopBundle\Form\Admin\Product\ProductAttachement',
            null,
            array('csrf_protection' => false)
        );

        $form->handleRequest($request);

        if ($form->isValid()) {
            $data = $form->getData();
            $res = $adminProductWrapper->processAddAttachment($product, $data, $legacyContext->getLanguages());
            if ($res) {
                $res->real_name = $data['name'];
                $response->setData($res);
            }
        } else {
            $response->setStatusCode(400);
            $response->setData($this->getFormErrorsForJS($form));
        }

        return $response;
    }
}
