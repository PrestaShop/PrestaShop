<?php
/**
 * 2007-2017 PrestaShop
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
 * @copyright 2007-2017 PrestaShop SA
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */
namespace PrestaShopBundle\Controller\Admin;

use Symfony\Component\HttpFoundation\Request;
use PrestaShopBundle\Form\Admin\Product as ProductForms;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Admin controller for the virtual product on the /product/form page.
 */
class VirtualProductController extends FrameworkBundleAdminController
{
    /**
     * Process Ajax Form to create/update virtual product
     *
     * @param int $idProduct
     * @param Request $request
     *
     * @return string
     */
    public function saveAction($idProduct, Request $request)
    {
        $response = new JsonResponse();
        $legacyContext = $this->container->get('prestashop.adapter.legacy.context');
        $adminProductWrapper = $this->container->get('prestashop.adapter.admin.wrapper.product');
        $productAdapter = $this->container->get('prestashop.adapter.data_provider.product');

        //get product
        $product = $productAdapter->getProduct((int)$idProduct, true);

        if (!$product || !$request->isXmlHttpRequest()) {
            return $response;
        }

        $form = $this->createForm(
           'PrestaShopBundle\Form\Admin\Product\ProductVirtual',
            null,
            array('csrf_protection' => false)
        );

        $form->handleRequest($request);
        if ($form->isValid()) {
            $data = $form->getData();
            $res = $adminProductWrapper->updateDownloadProduct($product, $data);
            $res->file_download_link = $res->filename ? $legacyContext->getAdminBaseUrl().$res->getTextLink(true) : '';

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
     * Process Ajax Form to remove attached file
     *
     * @param int $idProduct
     * @param Request $request
     *
     * @return string
     */
    public function removeFileAction($idProduct, Request $request)
    {
        $response = new JsonResponse();
        $adminProductWrapper = $this->container->get('prestashop.adapter.admin.wrapper.product');
        $productAdapter = $this->container->get('prestashop.adapter.data_provider.product');

        //get product
        $product = $productAdapter->getProduct((int)$idProduct);

        if (!$product || !$request->isXmlHttpRequest()) {
            return $response;
        }

        $adminProductWrapper->processDeleteVirtualProductFile($product);

        return $response;
    }

    /**
     * Process Ajax remove action
     *
     * @param int $idProduct
     * @param Request $request
     *
     * @return string
     */
    public function removeAction($idProduct, Request $request)
    {
        $response = new JsonResponse();
        $adminProductWrapper = $this->container->get('prestashop.adapter.admin.wrapper.product');
        $productAdapter = $this->container->get('prestashop.adapter.data_provider.product');

        //get product
        $product = $productAdapter->getProduct((int)$idProduct);

        if (!$product || !$request->isXmlHttpRequest()) {
            return $response;
        }

        $adminProductWrapper->processDeleteVirtualProduct($product);

        return $response;
    }
}
