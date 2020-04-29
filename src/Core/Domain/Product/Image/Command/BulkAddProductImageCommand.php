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

namespace PrestaShop\PrestaShop\Core\Domain\Product\Image\Command;

use PrestaShop\PrestaShop\Core\Domain\Product\Image\Exception\ImageException;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;

class BulkAddProductImageCommand
{
    /**
     * @var ProductId
     */
    private $productId;

    /**
     * @var int
     */
    private $imageCount;

    /**
     * @param int $productId
     * @param int $imageCount
     */
    public function __construct(int $productId, int $imageCount)
    {
        $this->productId = new ProductId($productId);
        $this->assertImageCount($imageCount);
        $this->imageCount = $imageCount;
    }

    /**
     * @return ProductId
     */
    public function getProductId(): ProductId
    {
        return $this->productId;
    }

    /**
     * @return int
     */
    public function getImageCount(): int
    {
        return $this->imageCount;
    }

    /**
     * @param int $count
     *
     * @throws ImageException
     */
    private function assertImageCount(int $count): void
    {
        if (1 > $count) {
            throw new ImageException(sprintf(
                'Invalid image count %s given for bulk upload',
                $count
            ));
        }
    }
}
