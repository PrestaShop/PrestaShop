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

use PrestaShop\PrestaShop\Core\Domain\Product\Image\Command\AddProductImageCommand;
use PrestaShop\PrestaShop\Core\Image\Uploader\ImageUploaderInterface;
use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class ImageController extends FrameworkBundleAdminController
{
    public function uploadAction(int $productId, Request $request): JsonResponse
    {
        //@todo: handle multiple files
        $imageFile = $request->files->all()[0];
        $imageId = $this->getCommandBus()->handle(new AddProductImageCommand(
            $productId,
            //@todo: what goes here?
            []
        ));

        $this->getProductImageUploader()->upload($productId, $imageFile, $imageId);
        //@todo: it should be multiple images so do it all in a loop?

        return $this->json([
            //@todo: test
            'message' => 'test response'
        ]);
    }

    /**
     * @return ImageUploaderInterface
     */
    private function getProductImageUploader(): ImageUploaderInterface
    {
        return $this->get('prestashop.adapter.image.uploader.product_image_uploader');
    }
}
