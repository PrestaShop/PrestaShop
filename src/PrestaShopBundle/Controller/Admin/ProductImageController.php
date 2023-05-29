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

use ImageManager;
use PrestaShop\PrestaShop\Adapter\Product\AdminProductWrapper;
use PrestaShopBundle\Security\Annotation\AdminSecurity;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @deprecated since 8.1 and will be removed in next major.
 *
 * Admin controller for product images.
 */
class ProductImageController extends FrameworkBundleAdminController
{
    /**
     * Manage upload for product image.
     *
     * @AdminSecurity("is_granted('create', request.get('_legacy_controller')) || is_granted('update', request.get('_legacy_controller'))")
     *
     * @param int $idProduct
     * @param Request $request
     *
     * @return string
     */
    public function uploadImageAction($idProduct, Request $request)
    {
        $response = new JsonResponse();
        $adminProductWrapper = $this->get(AdminProductWrapper::class);
        $return_data = [];

        if ($idProduct == 0 || !$request->isXmlHttpRequest()) {
            return $response;
        }

        $form = $this->createFormBuilder(null, ['csrf_protection' => false])
            ->add('file', 'Symfony\Component\Form\Extension\Core\Type\FileType', [
                'error_bubbling' => true,
                'constraints' => [
                    new Assert\NotNull(['message' => $this->trans('Please select a file', 'Admin.Catalog.Feature')]),
                    new Assert\Image(['maxSize' => $this->getConfiguration()->get('PS_ATTACHMENT_MAXIMUM_SIZE') . 'M']),
                    new Assert\File([
                        'mimeTypes' => [
                            'image/gif',
                            'image/jpeg',
                            'image/png',
                            'image/webp',
                        ],
                        'mimeTypesMessage' => $this->trans(
                            'Image format not recognized, allowed formats are: %s',
                            'Admin.Notifications.Error',
                            [
                                implode(', ', ImageManager::EXTENSIONS_SUPPORTED),
                            ]
                        ),
                    ]),
                ],
            ])
            ->getForm();

        $form->handleRequest($request);

        if ($request->isMethod('POST')) {
            if ($form->isValid()) {
                $return_data = $adminProductWrapper->getInstance()->ajaxProcessaddProductImage($idProduct, 'form', false)[0];
                $return_data = array_merge($return_data, [
                    'url_update' => $this->generateUrl('admin_product_image_form', ['idImage' => $return_data['id']]),
                    'url_delete' => $this->generateUrl('admin_product_image_delete', ['idImage' => $return_data['id']]),
                ]);
            } else {
                $error_msg = [];
                foreach ($form->getErrors() as $error) {
                    $error_msg[] = $error->getMessage();
                }
                $return_data = ['message' => implode(' ', $error_msg)];
                $response->setStatusCode(400);
            }
        }

        return $response->setData($return_data);
    }

    /**
     * Update images positions.
     *
     * @AdminSecurity("is_granted('create', request.get('_legacy_controller')) || is_granted('update', request.get('_legacy_controller'))")
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function updateImagePositionAction(Request $request)
    {
        $response = new JsonResponse();
        $adminProductWrapper = $this->get(AdminProductWrapper::class);
        $json = $request->request->get('json');

        if (!empty($json) && $request->isXmlHttpRequest()) {
            $adminProductWrapper->ajaxProcessUpdateImagePosition(json_decode($json, true));
        }

        return $response;
    }

    /**
     * Manage form image.
     *
     * @AdminSecurity("is_granted('create', request.get('_legacy_controller')) || is_granted('update', request.get('_legacy_controller'))")
     * @Template("@PrestaShop/Admin/ProductImage/form.html.twig")
     *
     * @param string|int $idImage
     * @param Request $request
     *
     * @return Response
     */
    public function formAction($idImage, Request $request): Response
    {
        $locales = $this->get('prestashop.adapter.legacy.context')->getLanguages();
        $adminProductWrapper = $this->get(AdminProductWrapper::class);
        $productAdapter = $this->get('prestashop.adapter.data_provider.product');

        if ($idImage == 0 || !$request->isXmlHttpRequest()) {
            return new Response();
        }

        $image = $productAdapter->getImage((int) $idImage);

        $form = $this->get('form.factory')->createNamedBuilder('form_image', FormType::class, $image, ['csrf_protection' => false])
            ->add('legend', 'PrestaShopBundle\Form\Admin\Type\TranslateType', [
                'type' => 'Symfony\Component\Form\Extension\Core\Type\TextareaType',
                'options' => [],
                'locales' => $locales,
                'hideTabs' => true,
                'label' => $this->trans('Caption', 'Admin.Catalog.Feature'),
                'required' => false,
            ])
            ->add('cover', 'Symfony\Component\Form\Extension\Core\Type\CheckboxType', [
                'label' => $this->trans('Cover image', 'Admin.Catalog.Feature'),
                'required' => false,
            ])
            ->getForm();

        $form->handleRequest($request);

        if ($request->isMethod('POST')) {
            $jsonResponse = new JsonResponse();

            if ($form->isValid()) {
                $jsonResponse->setData($adminProductWrapper->ajaxProcessUpdateImage($idImage, $form->getData()));
            } else {
                $error_msg = [];
                foreach ($form->getErrors() as $error) {
                    $error_msg[] = $error->getMessage();
                }

                $jsonResponse->setData(['message' => implode(' ', $error_msg)]);
                $jsonResponse->setStatusCode(400);
            }

            return $jsonResponse;
        }

        return $this->render('@PrestaShop/Admin/ProductImage/form.html.twig', [
            'image' => $image,
            'form' => $form->createView(),
        ]);
    }

    /**
     * Delete an image from its ID.
     *
     * @AdminSecurity("is_granted('create', request.get('_legacy_controller')) || is_granted('update', request.get('_legacy_controller'))")
     *
     * @param int $idImage
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function deleteAction($idImage, Request $request)
    {
        $response = new JsonResponse();
        $adminProductWrapper = $this->get(AdminProductWrapper::class);

        if (!$request->isXmlHttpRequest()) {
            return $response;
        }

        $adminProductWrapper->getInstance()->ajaxProcessDeleteProductImage($idImage);

        return $response;
    }
}
