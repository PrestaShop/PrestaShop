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
use PrestaShop\PrestaShop\Core\Domain\Product\Image\Command\EditProductImageCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Image\CommandHandler\EditProductImageHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Product\Image\Exception\CannotUnlinkImageException;
use PrestaShop\PrestaShop\Core\Domain\Product\Image\Exception\ImageException;
use PrestaShop\PrestaShop\Core\Domain\Product\Image\Exception\ImageNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Product\Image\Exception\ImageUpdateException;
use PrestaShop\PrestaShop\Core\Domain\Product\Image\ValueObject\ImageId;
use PrestaShopException;

final class EditProductImageHandler implements EditProductImageHandlerInterface
{
    /**
     * {@inheritDoc}
     */
    public function handle(EditProductImageCommand $command): void
    {
        $image = $this->getImage($command->getImageId());

        //Intentional check for truthy value only,
        //because we don't want to remove existing cover unless selecting this as new one.
        if ($command->isCover()) {
            $this->switchCover($image);
        }

        if (null !== $command->getLocalizedLegends()) {
            $image->legend = $command->getLocalizedLegends();
        }
    }

    /**
     * @param Image $image
     *
     * @throws CannotUnlinkImageException
     * @throws ImageException
     * @throws ImageUpdateException
     */
    private function switchCover(Image $image): void
    {
        $productId = (int) $image->id_product;

        try {
            if (!Image::deleteCover($productId)) {
                throw new ImageUpdateException(sprintf(
                    'Failed updating cover image for product #%s.',
                    $productId
                ));
            }
        } catch (ErrorException $e) {
            throw new CannotUnlinkImageException(
                sprintf('Failed to unlink cover image from system for product #%s', $productId),
                0,
                $e
            );
        } catch (PrestaShopException $e) {
            throw new ImageException(
                sprintf(
                    'Error occurred when trying to update cover for product with id "%s"',
                    $productId
                ),
                0,
                $e
            );
        }

        $image->cover = true;
    }

    /**
     * @param ImageId $imageId
     *
     * @return Image
     *
     * @throws ImageException
     * @throws ImageNotFoundException
     */
    private function getImage(ImageId $imageId): Image
    {
        try {
            $image = new Image($imageId);

            if ((int) $image->id !== $imageId) {
                throw new ImageNotFoundException(sprintf(
                    'Image with id "%s" was not found.',
                    $imageId
                ));
            }
        } catch (PrestaShopException $e) {
            throw new ImageException(
                sprintf(
                    'Error occurred when trying to load image entity #%s',
                    $imageId
                ),
                0,
                $e
            );
        }

        return $image;
    }
}
