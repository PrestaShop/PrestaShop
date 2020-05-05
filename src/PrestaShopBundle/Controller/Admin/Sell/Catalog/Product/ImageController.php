<?php
/**
 * 2007-2020 PrestaShop SA and Contributors
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
 * @copyright 2007-2020 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

declare(strict_types=1);

namespace PrestaShopBundle\Controller\Admin\Sell\Catalog\Product;

use ErrorException;
use Exception;
use PrestaShop\PrestaShop\Core\Configuration\UploadSizeConfigurationInterface;
use PrestaShop\PrestaShop\Core\Domain\Product\Image\Command\UploadProductImageCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Image\Exception\CannotUnlinkImageException;
use PrestaShop\PrestaShop\Core\Domain\Product\Image\Exception\ImageConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Product\Image\ImageSettings;
use PrestaShop\PrestaShop\Core\Domain\Product\Image\Query\GetProductImages;
use PrestaShop\PrestaShop\Core\Domain\Product\Image\QueryResult\ProductImages;
use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use PrestaShopBundle\Security\Annotation\AdminSecurity;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ImageController extends FrameworkBundleAdminController
{
    /**
     * @AdminSecurity("is_granted(['create', 'update'], request.get('_legacy_controller'))")
     *
     * @param int $productId
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function uploadAction(int $productId, Request $request): JsonResponse
    {
        $uploadedFiles = $request->files->all();

        if (empty($uploadedFiles)) {
            return $this->json(
                ['message' => $this->trans('No file was uploaded.', 'Admin.Advparameters.Notification')],
                Response::HTTP_BAD_REQUEST
            );
        }

        /** @var UploadedFile $uploadedFile */
        foreach ($uploadedFiles as $uploadedFile) {
            $mimeType = $uploadedFile->getMimeType();

            try {
                $this->checkUploadedFileSize($uploadedFile);
            } catch (ImageConstraintException $e) {
                //@todo: decide convention for ajax error responses
                return $this->json(
                    ['message' => $this->getErrorMessageForException($e, $this->getErrorMessages($e))],
                    Response::HTTP_BAD_REQUEST
                );
            }

            try {
                $tmpImageFile = $uploadedFile->move(_PS_TMP_IMG_DIR_, uniqid());
                $this->getCommandBus()->handle(new UploadProductImageCommand(
                    $productId,
                    $tmpImageFile->getPathname(),
                    $mimeType
                ));
            } catch (FileException|Exception $e) {
                return $this->json(
                    ['message' => $this->getErrorMessageForException($e, $this->getErrorMessages($e))],
                    Response::HTTP_INTERNAL_SERVER_ERROR
                );
            }

            try {
                unlink($tmpImageFile->getPathname());
            } catch (ErrorException $e) {
                return $this->json(
                    [
                        //@todo: should be just a warning, because upload have already succeeded
                        'message' => $this->trans(
                            'Failed to delete file "%filePath%"',
                            'Admin.Notifications.Error',
                            ['%filePath%' => $tmpImageFile->getPathname()]
                        )
                    ],
                    Response::HTTP_PARTIAL_CONTENT
                );
            }
        }

        return $this->json([
            //@todo: test
            'message' => 'success test response'
        ]);
    }

    /**
     * @AdminSecurity("is_granted('read', request.get('_legacy_controller'))")
     *
     * @param int $productId
     *
     * @return JsonResponse
     */
    public function getImagesAction(int $productId): JsonResponse
    {
        $images = $this->getQueryBus()->handle(new GetProductImages($productId));
        //@todo: check edgecases/errors etc.

        $formattedImages = [];
        /** @var ProductImages $images */
        foreach ($images->getProductImages() as $image) {
            $formattedImages[] = [
                'imageId' => $image->getId(),
                'productId' => $image->getProductId(),
                'position' => $image->getPosition(),
                'basePath' => $image->getBasePath(),
                //@todo: do i need lang here or leave it to js?
                'legend' => $image->getLocalizedLegends()[$this->getContextLangId()]
            ];
        }
        return $this->json([
            'images' => $formattedImages,
        ]);
    }

    /**
     * @param UploadedFile $uploadedFile
     *
     * @throws ImageConstraintException
     */
    private function checkUploadedFileSize(UploadedFile $uploadedFile): void
    {
        $maxUploadSize = $this->getUploadSizeConfig()->getMaxUploadSizeInBytes();
        $fileSize = $uploadedFile->getSize();

        if ($maxUploadSize > 0 && $fileSize > $maxUploadSize) {
            throw new ImageConstraintException(
                sprintf('Max file size allowed is "%s" bytes. Uploaded file size is "%s".', $maxUploadSize, $fileSize),
                ImageConstraintException::INVALID_FILE_SIZE
            );
        }
    }

    /**
     * @return UploadSizeConfigurationInterface
     */
    private function getUploadSizeConfig(): UploadSizeConfigurationInterface
    {
        return $this->get('prestashop.core.configuration.upload_size_configuration');
    }

    /**
     * @param Exception $e
     *
     * @return array
     */
    private function getErrorMessages(Exception $e): array
    {
        $errorMessagesMap = [
            ImageConstraintException::class => [
                ImageConstraintException::INVALID_FILE_FORMAT => $this->trans(
                    'Image format not recognized, allowed format(s) is(are): .%s',
                    'Admin.Notifications.Error',
                    [implode(',', ImageSettings::getAllowedFormats())]
                ),
                ImageConstraintException::INVALID_FILE_SIZE => $this->trans(
                    'Max file size allowed is "%s" bytes.',
                    'Admin.Notifications.Error',
                    [$this->getUploadSizeConfig()->getMaxUploadSizeInBytes()]
                ),
            ]
        ];

        if ($e instanceof CannotUnlinkImageException) {
            $errorMessagesMap[CannotUnlinkImageException::class] = $this->trans(
                'Failed to delete file "%filePath%"',
                'Admin.Notifications.Error',
                ['%filePath%' => $e->getFilePath()]
            );
        }

        return $errorMessagesMap;
    }
}
