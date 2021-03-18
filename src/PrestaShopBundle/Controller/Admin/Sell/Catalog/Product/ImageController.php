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

declare(strict_types=1);

namespace PrestaShopBundle\Controller\Admin\Sell\Catalog\Product;

use Exception;
use PrestaShop\PrestaShop\Core\Domain\Product\Image\Command\AddProductImageCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Image\Exception\CannotAddProductImageException;
use PrestaShop\PrestaShop\Core\Domain\Product\Image\Exception\ProductImageNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Product\Image\Query\GetProductImage;
use PrestaShop\PrestaShop\Core\Domain\Product\Image\Query\GetProductImages;
use PrestaShop\PrestaShop\Core\Domain\Product\Image\QueryResult\ProductImage;
use PrestaShop\PrestaShop\Core\Domain\Product\Image\ValueObject\ImageId;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\Builder\FormBuilderInterface;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\Handler\FormHandlerInterface;
use PrestaShop\PrestaShop\Core\Image\Uploader\Exception\MemoryLimitException;
use PrestaShop\PrestaShop\Core\Image\Uploader\Exception\UploadedImageConstraintException;
use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use PrestaShopBundle\Form\Admin\Sell\Product\Image\AddImageType;
use PrestaShopBundle\Security\Annotation\AdminSecurity;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ImageController extends FrameworkBundleAdminController
{
    /**
     * @AdminSecurity("is_granted(['read'], request.get('_legacy_controller'))", message="You do not have permission to update this.")
     *
     * @param int $productId
     *
     * @return JsonResponse
     */
    public function getImagesAction(int $productId): JsonResponse
    {
        /** @var ProductImage[] $images */
        $images = $this->getQueryBus()->handle(new GetProductImages($productId));

        return new JsonResponse(array_map([$this, 'formatImage'], $images));
    }

    /**
     * @AdminSecurity("is_granted(['update'], request.get('_legacy_controller'))", message="You do not have permission to update this.")
     *
     * @param Request $request
     * @param int $productId
     *
     * @return JsonResponse
     */
    public function addImageAction(Request $request, int $productId): JsonResponse
    {
        $imageForm = $this->createForm(AddImageType::class);
        $imageForm->handleRequest($request);

        if (!$imageForm->isSubmitted() || $imageForm->isValid()) {
            return new JsonResponse([
                'error' => 'Invalid data.',
                'form_errors' => $this->getFormErrorsForJS($imageForm),
            ], Response::HTTP_BAD_REQUEST);
        }

        $formData = $imageForm->getData();
        $uploadedFile = $formData['file'];
        try {
            $command = new AddProductImageCommand($productId, $uploadedFile->getPathname());
            /** @var ImageId $imageId */
            $imageId = $this->getCommandBus()->handle($command);
        } catch (Exception $e) {
            return new JsonResponse([
                'error' => $this->getErrorMessageForException($e, $this->getErrorMessages($e)),
            ], Response::HTTP_BAD_REQUEST);
        }

        return $this->getProductImageJsonResponse($imageId->getValue());
    }

    /**
     * @AdminSecurity("is_granted(['update'], request.get('_legacy_controller'))", message="You do not have permission to update this.")
     *
     * @param Request $request
     * @param int $productImageId
     *
     * @return JsonResponse
     */
    public function updateImageAction(Request $request, int $productImageId): JsonResponse
    {
        $imageForm = $this->getProductImageFormBuilder()->getFormFor($productImageId, [], [
            'method' => Request::METHOD_PATCH,
        ]);
        $imageForm->handleRequest($request);

        try {
            $result = $this->getProductImageFormHandler()->handleFor($productImageId, $imageForm);

            if (!$result->isSubmitted() || !$result->isValid()) {
                return new JsonResponse([
                    'error' => 'Invalid data.',
                    'form_errors' => $this->getFormErrorsForJS($imageForm),
                ], Response::HTTP_BAD_REQUEST);
            }
        } catch (Exception $e) {
            return new JsonResponse([
                'error' => $this->getErrorMessageForException($e, $this->getErrorMessages($e)),
            ], Response::HTTP_BAD_REQUEST);
        }

        return $this->getProductImageJsonResponse($productImageId);
    }

    /**
     * @return FormBuilderInterface
     */
    private function getProductImageFormBuilder(): FormBuilderInterface
    {
        return $this->get('prestashop.core.form.identifiable_object.builder.update_image_form_builder');
    }

    /**
     * @return FormHandlerInterface
     */
    private function getProductImageFormHandler(): FormHandlerInterface
    {
        return $this->get('prestashop.core.form.identifiable_object.product_image_form_handler');
    }

    /**
     * @param int $productImageId
     *
     * @return JsonResponse
     */
    private function getProductImageJsonResponse(int $productImageId): JsonResponse
    {
        $productImage = $this->getQueryBus()->handle(new GetProductImage($productImageId));

        return new JsonResponse($this->formatImage($productImage));
    }

    /**
     * @param ProductImage $image
     *
     * @return array
     */
    private function formatImage(ProductImage $image): array
    {
        return [
            'image_id' => $image->getImageId(),
            'is_cover' => $image->isCover(),
            'position' => $image->getPosition(),
            'path' => _THEME_PROD_DIR_ . $image->getPath(),
            'legends' => $image->getLocalizedLegends(),
        ];
    }

    /**
     * Gets an error by exception class and its code.
     *
     * @param Exception $e
     *
     * @return array
     */
    private function getErrorMessages(Exception $e): array
    {
        $iniConfig = $this->get('prestashop.core.configuration.ini_configuration');

        return [
            ProductImageNotFoundException::class => $this->trans(
                'The object cannot be loaded (or found)',
                'Admin.Notifications.Error'
            ),
            UploadedImageConstraintException::class => [
                UploadedImageConstraintException::EXCEEDED_SIZE => $this->trans(
                'Max file size allowed is "%s" bytes.',
                'Admin.Notifications.Error',
                    [$iniConfig->getUploadMaxSizeInBytes()]
                ),
                UploadedImageConstraintException::UNRECOGNIZED_FORMAT => $this->trans(
                    'Image format not recognized, allowed formats are: .gif, .jpg, .png',
                    'Admin.Notifications.Error'
                ),
            ],
            MemoryLimitException::class => $this->trans(
                'Due to memory limit restrictions, this image cannot be loaded. Please increase your memory_limit value via your server\'s configuration settings.',
                'Admin.Notifications.Error'
            ),
            CannotAddProductImageException::class => $this->trans(
                'An error occurred while attempting to save.',
                'Admin.Notifications.Error'
            ),
        ];
    }
}
