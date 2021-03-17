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
use PrestaShop\PrestaShop\Core\Domain\Product\Image\Query\GetProductImages;
use PrestaShop\PrestaShop\Core\Domain\Product\Image\QueryResult\ProductImage;
use PrestaShop\PrestaShop\Core\Domain\Product\Image\ValueObject\ImageId;
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

        $data = [];
        foreach ($images as $image) {
            $data[] = $this->formatImage($image);
        }

        return new JsonResponse($data);
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

        // @todo: the GetProductImage command is implemented in another PR, but it should be used here to return the
        // full data of the created image (like in the getImages controller)

        return new JsonResponse(['image_id' => $imageId->getValue()]);
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
