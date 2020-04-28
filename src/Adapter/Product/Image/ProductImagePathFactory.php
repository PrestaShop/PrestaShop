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

namespace PrestaShop\PrestaShop\Adapter\Product\Image;

use Image;
use PrestaShop\PrestaShop\Core\Domain\Product\Image\Exception\ImageException;
use PrestaShop\PrestaShop\Core\Domain\Product\Image\Exception\ImageNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Product\Image\ImagePathFactoryInterface;
use PrestaShop\PrestaShop\Core\Domain\Product\Image\ValueObject\ImageId;
use PrestaShop\PrestaShop\Core\Image\Uploader\Exception\ImageUploadException;
use PrestaShopException;

final class ProductImagePathFactory implements ImagePathFactoryInterface
{
    /**
     * @var bool
     */
    private $isLegacyImageMode;

    /**
     * @param bool $isLegacyImageMode
     */
    public function __construct(
        bool $isLegacyImageMode
    ) {
        $this->isLegacyImageMode = $isLegacyImageMode;
    }

    /**
     * {@inheritDoc}
     */
    public function getBasePath(ImageId $imageId, bool $withExtension): string
    {
        $image = $this->getImage($imageId);

        if ($this->isLegacyImageMode) {
            $path = $image->id_product . '-' . $image->id;
        } else {
            $path = $image->getImgPath();
        }

        //@todo: it seems that jpg is hardcoded. AdminProductsController:2836
        if ($withExtension) {
            $path .= sprintf('.%s', $image->image_format);
        }

        return _PS_PROD_IMG_DIR_ . $path;
    }

    /**
     * {@inheritDoc}
     */
    public function createDestinationDirectory(ImageId $imageId): void
    {
        $image = $this->getImage($imageId);

        if ($this->isLegacyImageMode || $image->createImgFolder()) {
            return;
        }

        throw new ImageUploadException(sprintf(
            'Error occurred when trying to create directory for product #%s image',
            $image->id_product
        ));
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
        //@todo: is it ok to load the image every time, or better build paths from imageId and productId manually instead?
        $imageIdValue = $imageId->getValue();

        try {
            $image = new Image($imageId->getValue());

            if ((int) $image->id !== $imageIdValue) {
                throw new ImageNotFoundException(sprintf(
                    'Image entity with id #%s does not exist',
                    $imageIdValue
                ));
            }
        } catch (PrestaShopException $e) {
            throw new ImageException(
                sprintf(
                    'Error occurred when trying to load image #%s entity.',
                    $imageIdValue
                ),
                0,
                $e
            );
        }

        return $image;
    }
}
