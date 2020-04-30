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

use PrestaShop\PrestaShop\Core\Domain\Product\Image\Command\UploadProductImageCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Image\Query\GetProductImages;
use PrestaShop\PrestaShop\Core\Domain\Product\Image\QueryResult\ProductImages;
use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class ImageController extends FrameworkBundleAdminController
{
    /**
     * @todo: security annotations
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
            return $this->json([
                //@todo: trans?
                'message' => 'No files provided for upload'
            ]);
        }

        /** @var UploadedFile $imageFile */
        $imageFile = reset($uploadedFiles);
        $this->getCommandBus()->handle(new UploadProductImageCommand(
            $productId,
            $imageFile->getPathname(),
            $imageFile->getSize(),
            //@todo: maybe better using mime type and some lib to guess the extension?
            $imageFile->getMimeType()
        ));

        return $this->json([
            //@todo: test
            'message' => 'test response'
        ]);
    }

//    /**
//     * @todo: security annotations
//     *
//     * @param int $productId
//     * @param Request $request
//     *
//     * @return JsonResponse
//     */
//    public function bulkUpload(int $productId, Request $request): JsonResponse
//    {
//        $uploadedFiles = $request->files->all();
//
//        if (empty($uploadedFiles)) {
//            return $this->json([
//                //@todo: trans?
//                'message' => 'No files provided for upload'
//            ]);
//        }
//
//        $imageIds = $this->getCommandBus()->handle(new BulkAddProductImageCommand($productId, count($uploadedFiles)));
//
//        foreach ($imageIds as $imageId) {
//            //@todo: how should I map image file with image Id, do i care which img gets which id or just loop through?
//            //@todo: And how do i roll back if some images fails to upload?
//        }
//
//        return $this->json([
//            //@todo: test
//            'message' => 'test response'
//        ]);
//    }

    /**
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
}
