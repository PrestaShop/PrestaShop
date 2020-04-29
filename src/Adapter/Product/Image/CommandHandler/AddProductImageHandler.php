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

use Image;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\ProductNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Product\Image\Command\AddProductImageCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Image\CommandHandler\AddProductImageHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Product\Image\Exception\ImageConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Product\Image\Exception\ImageException;
use PrestaShop\PrestaShop\Core\Domain\Product\Image\ValueObject\ImageId;
use PrestaShopException;
use Product;

final class AddProductImageHandler implements AddProductImageHandlerInterface
{
    /**
     * {@inheritDoc}
     */
    public function handle(AddProductImageCommand $command): ImageId
    {
        $productIdValue = $command->getProductId()->getValue();
        $this->assertProductExists($productIdValue);

        $image = new Image();
        $image->id_product = $productIdValue;
        $image->position = Image::getHighestPosition($productIdValue) + 1;

        if (!Image::getCover($image->id_product)) {
            $image->cover = 1;
        } else {
            $image->cover = 0;
        }

        if (!$image->validateFieldsLang(false, false)) {
            throw new ImageConstraintException(sprintf(
                'Image contains invalid fields'
            ));
        }

        try {
            if (!$image->add()) {
                throw new ImageException('Failed to add new image');
            }
        } catch (PrestaShopException $e) {
            throw new ImageException('Error occurred when trying to add new image', 0, $e);
        }

        return new ImageId((int) $image->id);
    }

    /**
     * @param int $productId
     *
     * @throws ProductNotFoundException
     */
    private function assertProductExists(int $productId): void
    {
        if (!Product::existsInDatabase($productId, 'product')) {
            throw new ProductNotFoundException(sprintf('Product #%s was not found', $productId));
        }
    }
}
