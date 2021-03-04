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

use PrestaShopBundle\Form\Admin\Product\ProductVirtual;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

/**
 * Admin controller for the virtual product on the /product/form page.
 */
class VirtualProductController extends FrameworkBundleAdminController
{
    /**
     * Process Ajax Form to create/update virtual product.
     *
     * @param string|int $idProduct
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function saveAction($idProduct, Request $request)
    {
        $response = new JsonResponse();
        $legacyContext = $this->get('prestashop.adapter.legacy.context');
        $adminProductWrapper = $this->get('prestashop.adapter.admin.wrapper.product');
        $productAdapter = $this->get('prestashop.adapter.data_provider.product');
        $router = $this->get('router');

        //get product
        $product = $productAdapter->getProduct((int) $idProduct);

        if (!$product || !$request->isXmlHttpRequest()) {
            return $response;
        }

        $form = $this->createForm(
            ProductVirtual::class,
            null,
            ['csrf_protection' => false]
        );

        $form->handleRequest($request);
        if ($form->isValid()) {
            $data = $form->getData();
            $res = $adminProductWrapper->updateDownloadProduct($product, $data);
            $res->file_download_link =
                $res->filename ?
                $router->generate(
                    'admin_product_virtual_download_file_action',
                    ['idProduct' => $idProduct]
                ) :
                '';

            $product->is_virtual = 1;
            $product->save();

            $response->setData($res);
        } else {
            $response->setStatusCode(400);
            $response->setData($this->getFormErrorsForJS($form));
        }

        return $response;
    }

    /**
     * Download the content of the virtual product.
     *
     * @param int $idProduct
     *
     * @return BinaryFileResponse
     */
    public function downloadFileAction($idProduct)
    {
        $configuration = $this->get('prestashop.adapter.legacy.configuration');
        $download = $this->getDoctrine()
            ->getRepository('PrestaShopBundle:ProductDownload')
            ->findOneBy([
                'idProduct' => $idProduct,
            ]);

        $response = new BinaryFileResponse(
            $configuration->get('_PS_DOWNLOAD_DIR_') . $download->getFilename()
        );

        $response->setContentDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            $download->getDisplayFilename()
        );

        return $response;
    }

    /**
     * Process Ajax Form to remove attached file.
     *
     * @param string|int $idProduct
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function removeFileAction($idProduct, Request $request)
    {
        $response = new JsonResponse();
        $adminProductWrapper = $this->get('prestashop.adapter.admin.wrapper.product');
        $productAdapter = $this->get('prestashop.adapter.data_provider.product');

        //get product
        $product = $productAdapter->getProduct((int) $idProduct);

        if (!$product || !$request->isXmlHttpRequest()) {
            return $response;
        }

        $adminProductWrapper->processDeleteVirtualProductFile($product);

        return $response;
    }

    /**
     * Process Ajax remove action.
     *
     * @param string|int $idProduct
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function removeAction($idProduct, Request $request)
    {
        $response = new JsonResponse();
        $adminProductWrapper = $this->get('prestashop.adapter.admin.wrapper.product');
        $productAdapter = $this->get('prestashop.adapter.data_provider.product');

        //get product
        $product = $productAdapter->getProduct((int) $idProduct);

        if (!$product || !$request->isXmlHttpRequest()) {
            return $response;
        }

        $adminProductWrapper->processDeleteVirtualProduct($product);

        return $response;
    }
}
