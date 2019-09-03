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

namespace PrestaShopBundle\Controller\Admin\Improve\Shipping;

use Exception;
use PrestaShop\PrestaShop\Core\Image\Uploader\TmpImageUploaderInterface;
use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use PrestaShopBundle\Form\Admin\Improve\Shipping\Carrier\CarrierType;
use PrestaShopBundle\Security\Annotation\AdminSecurity;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class CarrierController extends FrameworkBundleAdminController
{
    /**
     * @AdminSecurity("is_granted(['read'], request.get('_legacy_controller'))")
     */
    public function indexAction()
    {
        //@todo: implemet index action
    }

    /**
     * Show shipping carrier creation page
     *
     * @AdminSecurity("is_granted(['create'], request.get('_legacy_controller'))")
     *
     * @return Response
     */
    public function createAction(Request $request): Response
    {
        $carrierForm = $this->createForm(CarrierType::class);
        $carrierForm->handleRequest($request);

        if ($carrierForm->isSubmitted() && $carrierForm->isValid()) {
            dump($carrierForm->getData());
            die;
        }

        return $this->render('@PrestaShop/Admin/Improve/Shipping/Carrier/add.html.twig', [
            'carrierForm' => $carrierForm->createView(),
            'contextLangId' => $this->container->get('prestashop.adapter.legacy.context')->getContext()->language->id,
            'defaultLangId' => $this->get('prestashop.adapter.legacy.configuration')->getInt('PS_LANG_DEFAULT'),
            'uploadImageUrl' => $this->generateUrl('admin_carriers_upload_image'),
            'removeImageUrl' => $this->generateUrl('admin_carriers_remove_image'),
            'defaultLogo' => '/img/admin/carrier-default.jpg',
            'validateStepsUrl' => $this->generateUrl('admin_carriers_validate_steps'),
        ]);
    }

    public function uploadImageAction(Request $request): JsonResponse
    {
        $carrierForm = $this->createForm(CarrierType::class);
        $carrierForm->handleRequest($request);

        $errors = [
            'message' => $this->trans(
                'An error occurred during the image upload process.',
                'Admin.Notifications.Error'
            ),
        ];

        if ($carrierForm->isSubmitted() && $carrierForm['step_general']['logo']->isValid()) {
            try {
                $uploadedImgPath = $this->uploadTmpImage($carrierForm->getData()['step_general']['logo']);

                $previousImgName = $carrierForm->getData()['step_general']['logo_tmp_name'];
                // remove previous image from tmp
                if ($previousImgName) {
                    $this->removeImage($previousImgName, true);
                }

                return $this->json([
                    'img_path' => $uploadedImgPath,
                ]);
            } catch (Exception $e) {
                return $this->returnErrorJsonResponse($errors, Response::HTTP_INTERNAL_SERVER_ERROR);
            }
        }

        return $this->returnErrorJsonResponse($errors, Response::HTTP_BAD_REQUEST);
    }

    public function removeImageAction(Request $request): JsonResponse
    {
        $content = json_decode($request->getContent(), true);

        if (isset($content['image_name'], $content['is_tmp_image'])) {
            if ($this->removeImage($content['image_name'], $content['is_tmp_image'])) {
                return $this->json([
                    'message' => $this->trans('Successful deletion.', 'Admin.Notifications.Success'),
                    'file_label' => $this->trans('%count% file(s)', 'Admin.Global', ['%count%' => 0]),
                ]);
            }
        }

        return $this->returnErrorJsonResponse([
            'message' => $this->trans(
                'An error occurred during the image removal process.',
                'Admin.Notifications.Error'),
            ],
            Response::HTTP_INTERNAL_SERVER_ERROR
        );
    }

    public function validateStepsAction(Request $request)
    {
        $carrierForm = $this->createForm(CarrierType::class);
        $carrierForm->handleRequest($request);

        $formStepMap = [
            1 => $carrierForm['step_general'],
            3 => $carrierForm['step_shipping'],
            4 => $carrierForm['step_properties_and_access'],
            5 => $carrierForm['step_summary'],
        ];

        if (isset($carrierForm['step_multi_shop'])) {
            $formStepMap[2] = $carrierForm['step_multi_shop'];
        }

        $errors = [];

        foreach ($formStepMap as $stepNr => $stepForm) {
            $stepToShow = $request->request->getInt('step');

            if ($stepNr < $stepToShow) {
                foreach ($this->getFormErrorsForJS($formStepMap[$stepNr]) as $errorField => $messages) {
                    $errors[$errorField] = isset($messages[0]) ? $messages[0] : '';
                }
            }
        }

        return $this->json([
            'errors' => $errors,
        ]);
    }

    // @todo: move to RemoveCarrierLogoCommand
    private function removeImage(string $imgName, bool $isTmpImage): bool
    {
        $imgPath = $isTmpImage === true ?
            _PS_TMP_IMG_DIR_ . $imgName :
            _PS_SHIP_IMG_DIR_ . $imgName
        ;

        return file_exists($imgPath) && unlink($imgPath);
    }

    private function uploadTmpImage(UploadedFile $image)
    {
        /** @var TmpImageUploaderInterface $tmpImageUploader */
        $tmpImageUploader = $this->container->get('prestashop.adapter.image.uploader.tmp_image_uploader');

        return $tmpImageUploader->upload($image);
    }
}
