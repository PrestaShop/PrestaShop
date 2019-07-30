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

namespace PrestaShop\PrestaShop\Core\Domain\Product\Command;

use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;

/**
 * Edits related products.
 */
class EditRelatedProductCommand
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
     * @param array $relatedProductIds
     */
    public function __construct(int $productId, array $relatedProductIds)
    {
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

    private function setRelatedProductIds(array $relatedProductIds): void
    {
        foreach ($relatedProductIds as $productId) {
            $this->relatedProductIds[] = new ProductId($productId);
        }
    }
}
