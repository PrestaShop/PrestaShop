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

namespace PrestaShop\PrestaShop\Adapter\Product\Image\CommandHandler;

use ErrorException;
use Image;
use PrestaShop\PrestaShop\Adapter\Product\Image\AbstractImageHandler;
use PrestaShop\PrestaShop\Core\Domain\Product\Image\Command\DeleteProductImageCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Image\CommandHandler\DeleteProductImageHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Product\Image\Exception\CannotDeleteImageException;
use PrestaShop\PrestaShop\Core\Domain\Product\Image\Exception\CannotUnlinkImageException;
use PrestaShop\PrestaShop\Core\Domain\Product\Image\Exception\ImageException;
use PrestaShop\PrestaShop\Core\Domain\Product\Image\Exception\ImageNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Product\Image\Exception\ImageUpdateException;
use PrestaShop\PrestaShop\Core\Domain\Product\Image\ValueObject\ImageId;
use PrestaShopException;

final class DeleteProductImageHandler extends AbstractImageHandler implements DeleteProductImageHandlerInterface
{
    /**
     * @var int
     */
    private $contextShopId;

    /**
     * @param int $contextShopId
     */
    public function __construct(int $contextShopId)
    {
        $this->contextShopId = $contextShopId;
    }

    /**
     * {@inheritDoc}
     */
    public function handle(DeleteProductImageCommand $command): void
    {
        $image = $this->getImage($command->getImageId());
        $this->deleteImageFromDb($image);

        if ($image->cover) {
            $this->useFirstPositionImageAsCover((int) $image->id_product);
        }

        $this->unlinkImageFiles($image);
    }

    /**
     * @param Image $image
     *
     * @throws CannotDeleteImageException
     * @throws ImageException
     */
    private function deleteImageFromDb(Image $image): void
    {
        try {
            if (!$image->delete()) {
                throw new CannotDeleteImageException(sprintf('Failed deleting image #%s', $image->id));
            }
        } catch (PrestaShopException $e) {
            throw new ImageException(
                sprintf('Error occurred when trying to delete image #%s', $image->id),
                0,
                $e
            );
        }
    }

    /**
     * @param int $productId
     *
     * @throws ImageException
     * @throws ImageUpdateException
     * @throws ImageNotFoundException
     */
    private function useFirstPositionImageAsCover(int $productId): void
    {
        $fallbackCoverImageId = Image::getFirstByPosition($productId, $this->contextShopId);
        $fallbackCoverImage = $this->getImage(new ImageId($fallbackCoverImageId));

        $fallbackCoverImage->cover = true;

        try {
            if (!$fallbackCoverImage->update()) {
                throw new ImageUpdateException(
                    sprintf('Failed to apply cover to first by position image with id "%s"', $fallbackCoverImageId)
                );
            }
        } catch (PrestaShopException $e) {
            throw new ImageException(
                sprintf(
                    'Error occurred when trying to update image #%s cover',
                    $fallbackCoverImageId
                ),
                0,
                $e
            );
        }
    }

    /**
     * @param Image $image
     *
     * @throws CannotUnlinkImageException
     */
    private function unlinkImageFiles(Image $image): void
    {
        //@todo: shouldn't it also delete images by types? AdminproductsController::ajaxProcessDeleteProductImage
        $imageFilePaths = [
            _PS_TMP_IMG_DIR_ . 'product_' . $image->id_product . '.jpg',
            _PS_TMP_IMG_DIR_ . 'product_mini_' . $image->id_product . '_' . $this->contextShopId . '.jpg',
        ];

        foreach ($imageFilePaths as $imageFile) {
            if (file_exists($imageFile)) {
                try {
                    unlink($imageFile);
                } catch (ErrorException $e) {
                    throw new CannotUnlinkImageException(
                        sprintf('Failed to unlink image "%s" from system', $imageFile),
                        0,
                        $e
                    );
                }
            }
        }
    }
}
