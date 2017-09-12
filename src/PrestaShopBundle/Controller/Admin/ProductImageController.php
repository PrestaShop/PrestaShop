<?php
/**
 * 2007-2017 PrestaShop
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
 * @copyright 2007-2017 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */
namespace PrestaShopBundle\Controller\Admin;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Validator\Constraints as Assert;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * Admin controller for product images
 */
class ProductImageController extends FrameworkBundleAdminController
{
    /**
     * Manage upload for product image
     *
     * @param int $idProduct
     * @param Request $request
     *
     * @return string
     */
    public function uploadImageAction($idProduct, Request $request)
    {
        $response = new JsonResponse();
        $adminProductWrapper = $this->container->get('prestashop.adapter.admin.wrapper.product');
        $translator = $this->container->get('translator');
        $return_data = [];

        if ($idProduct == 0 || !$request->isXmlHttpRequest()) {
            return $response;
        }

        $form = $this->createFormBuilder(null, array('csrf_protection' => false))
            ->add('file', 'Symfony\Component\Form\Extension\Core\Type\FileType', array(
                'error_bubbling' => true,
                'constraints' => [
                    new Assert\NotNull(array('message' => $translator->trans('Please select a file', [], 'Admin.Catalog.Feature'))),
                    new Assert\Image(array('maxSize' => $this->configuration->get('PS_ATTACHMENT_MAXIMUM_SIZE').'M')),
                ]
            ))
            ->getForm();

        $form->handleRequest($request);

        if ($request->isMethod('POST')) {
            if ($form->isValid()) {
                $return_data = $adminProductWrapper->getInstance()->ajaxProcessaddProductImage($idProduct, 'form', false)[0];
                $return_data = array_merge($return_data, array(
                    'url_update' => $this->generateUrl('admin_product_image_form', array('idImage' => $return_data['id'])),
                    'url_delete' => $this->generateUrl('admin_product_image_delete', array('idImage' => $return_data['id'])),
                ));
            } else {
                $error_msg = array();
                foreach ($form->getErrors() as $error) {
                    $error_msg[] = $error->getMessage();
                }
                $return_data = array('message' => implode(" ", $error_msg));
                $response->setStatusCode(400);
            }
        }

        return $response->setData($return_data);
    }

    /**
     * Update images positions
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function updateImagePositionAction(Request $request)
    {
        $response = new JsonResponse();
        $adminProductWrapper = $this->container->get('prestashop.adapter.admin.wrapper.product');
        $json = $request->request->get('json');

        if (!empty($json) && $request->isXmlHttpRequest()) {
            $adminProductWrapper->ajaxProcessUpdateImagePosition(json_decode($json, true));
        }

        return $response;
    }

    /**
     * Manage form image
     *
     * @Template("@PrestaShop/Admin/ProductImage/form.html.twig")
     * @param $idImage
     * @param Request $request
     * @return array|JsonResponse|Response
     */
    public function formAction($idImage, Request $request)
    {
        $locales = $this->container->get('prestashop.adapter.legacy.context')->getLanguages();
        $adminProductWrapper = $this->container->get('prestashop.adapter.admin.wrapper.product');
        $productAdapter = $this->container->get('prestashop.adapter.data_provider.product');
        $translator = $this->container->get('translator');

        if ($idImage == 0 || !$request->isXmlHttpRequest()) {
            return new Response();
        }

        $image = $productAdapter->getImage((int)$idImage);

        $form = $this->container->get('form.factory')->createNamedBuilder('form_image', 'form', $image, array('csrf_protection' => false))
            ->add('legend', 'PrestaShopBundle\Form\Admin\Type\TranslateType', array(
                'type' => 'Symfony\Component\Form\Extension\Core\Type\TextareaType',
                'options' => array(),
                'locales' => $locales,
                'hideTabs' => true,
                'label' => $translator->trans('Caption', array(), 'Admin.Catalog.Feature'),
                'required' => false,
            ))
            ->add('cover', 'Symfony\Component\Form\Extension\Core\Type\CheckboxType', array(
                'label'    => $translator->trans('Cover image', array(), 'Admin.Catalog.Feature'),
                'required' => false,
            ))
            ->getForm();

        $form->handleRequest($request);

        if ($request->isMethod('POST')) {
            $jsonResponse = new JsonResponse();

            if ($form->isValid()) {
                $jsonResponse->setData($adminProductWrapper->ajaxProcessUpdateImage($idImage, $form->getData()));
            } else {
                $error_msg = array();
                foreach ($form->getErrors() as $error) {
                    $error_msg[] = $error->getMessage();
                }

                $jsonResponse->setData(array('message' => implode(" ", $error_msg)));
                $jsonResponse->setStatusCode(400);
            }

            return $jsonResponse;
        }

        return array(
            'image' => $image,
            'form' => $form->createView(),
        );
    }

    /**
     * Delete an image from its ID
     *
     * @param int $idImage
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function deleteAction($idImage, Request $request)
    {
        $response = new JsonResponse();
        $adminProductWrapper = $this->container->get('prestashop.adapter.admin.wrapper.product');

        if (!$request->isXmlHttpRequest()) {
            return $response;
        }

        $adminProductWrapper->getInstance()->ajaxProcessDeleteProductImage($idImage);

        return $response;
    }
}
