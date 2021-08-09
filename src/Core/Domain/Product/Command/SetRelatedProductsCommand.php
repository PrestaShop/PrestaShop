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

namespace PrestaShop\PrestaShop\Core\Domain\Product\Command;

use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;
use RuntimeException;

/**
 * Sets related products for product
 */
class SetRelatedProductsCommand
{
    /**
     * @var ProductId
     */
    private $productId;

    /**
     * @var ProductId[]
     */
    private $relatedProductIds;

    /**
     * @param int $productId
     * @param int[] $relatedProductIds
     */
    public function __construct(
        int $productId,
        array $relatedProductIds
    ) {
        $this->productId = new ProductId($productId);
        $this->setRelatedProductIds($relatedProductIds);
    }

    /**
     * @return ProductId
     */
    public function getProductId(): ProductId
    {
        return $this->productId;
    }

    /**
     * @return ProductId[]
     */
    public function getRelatedProductIds(): array
    {
        return $this->relatedProductIds;
    }

    /**
     * @param int[] $ids
     */
    private function setRelatedProductIds(array $ids): void
    {
        if (empty($ids)) {
            throw new RuntimeException(sprintf(
                'Empty array of related products provided in %s. To remove all related products use %s.',
                self::class,
                RemoveAllRelatedProductsCommand::class
            ));
        }

        foreach ($ids as $id) {
            $this->relatedProductIds[] = new ProductId($id);
        }
    }
}
