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

namespace PrestaShop\PrestaShop\Core\Domain\Product\Image\Command;

;
use PrestaShop\PrestaShop\Core\Domain\Product\Image\Exception\ProductImageConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Product\Image\ValueObject\ExistingProductImage;
use PrestaShop\PrestaShop\Core\Domain\Product\Image\ValueObject\ProductImage;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;

/**
 * Updates product images.
 */
class UpdateProductImagesCommand
{
    /**
     * @var ProductId
     */
    private $productId;

    /**
     * @var ExistingProductImage[]|ProductImage[]
     */
    private $images;

    /**
     * @param int $productId
     * @param array $images
     *
     * @throws ProductImageConstraintException
     */
    public function __construct(int $productId, array $images)
    {
        $this->setImages($images);
        $this->productId = new ProductId($productId);
    }

    /**
     * @return ProductId
     */
    public function getProductId(): ProductId
    {
        return $this->productId;
    }

    /**
     * @return ExistingProductImage[]|ProductImage[]
     */
    public function getImages(): array
    {
        return $this->images;
    }

    /**
     * @param array $images
     *
     * @throws ProductImageConstraintException
     */
    private function setImages(array $images): void
    {
        foreach ($images as $image) {
            $commonParameters = [$image['position'], $image['is_cover'], $image['captions']];

            if (isset($image['id'])) {
                $this->images[] = new ExistingProductImage($image['id'], ...$commonParameters);

                continue;
            }

            $this->images[] = new ProductImage(...$commonParameters);
        }
    }
}
