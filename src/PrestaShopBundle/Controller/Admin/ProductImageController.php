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
namespace PrestaShopBundle\Controller\Admin;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Validator\Constraints as Assert;
use PrestaShopBundle\Form\Admin\Type\TranslateType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * Admin controller for product image
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
        $translator = $this->container->get('prestashop.adapter.translator');
        $return_data = [];

        if ($idProduct == 0 || !$request->isXmlHttpRequest()) {
            return $response;
        }

        $form = $this->createFormBuilder(null, array('csrf_protection' => false))
            ->add('file', 'file', array(
                'error_bubbling' => true,
                'constraints' => [
                    new Assert\NotNull(array('message' => $translator->trans('Please select a file', [], 'AdminProducts'))),
                    new Assert\Image(array('maxSize' => '8M')),
                ]
            ))
            ->getForm();

        $form->handleRequest($request);

        if ($request->isMethod('POST')) {
            if ($form->isValid()) {
                $return_data = $adminProductWrapper->getInstance()->ajaxProcessaddProductImage($idProduct, 'form', false)[0];
            } else {
                $error_msg = array();
                foreach ($form->getErrors() as $key => $error) {
                    $error_msg[] = $error->getMessage();
                }
                $return_data = array('message' => implode(" ", $error_msg));
                $response->setStatusCode(400);
            }
        }

        return $response->setContent(json_encode($return_data));
    }

    /**
     * Update images positions
     *
     * @param Request $request
     *
     * @return Reponse
     */
    public function updateImagePositionAction(Request $request)
    {
        $response = new JsonResponse();
        $adminProductWrapper = $this->container->get('prestashop.adapter.admin.wrapper.product');
        $json = $request->request->get('json');

        if (empty($json) || !$request->isXmlHttpRequest()) {
            return $response;
        }

        $adminProductWrapper->ajaxProcessUpdateImagePosition(json_decode($json, 1));

        return $response;
    }

    /**
     * Manage form image
     *
     * @Template
     * @param int $idImage
     * @param Request $request
     *
     * @return array
     */
    public function formAction($idImage, Request $request)
    {
        $locales = $this->container->get('prestashop.adapter.legacy.context')->getLanguages();
        $adminProductWrapper = $this->container->get('prestashop.adapter.admin.wrapper.product');
        $productAdapter = $this->container->get('prestashop.adapter.data_provider.product');
        $translator = $this->container->get('prestashop.adapter.translator');

        if ($idImage == 0 || !$request->isXmlHttpRequest()) {
            return new Response();
        }

        $image = $productAdapter->getImage((int)$idImage);

        $form = $this->container->get('form.factory')->createNamedBuilder('form_image', 'form', $image, array('csrf_protection' => false))
            ->add('legend', new TranslateType('text', array(), $locales, false), array(
                'label' => $translator->trans('Legend', [], 'AdminProducts'),
                'required' => false,
            ))
            ->add('cover', 'checkbox', array(
                'label'    => $translator->trans('Choose as cover image', [], 'AdminProducts'),
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
                foreach ($form->getErrors() as $key => $error) {
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
     * delete image
     *
     * @param int $idImage
     * @param Request $request
     *
     * @return Reponse
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
