<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Form\IdentifiableObject\CommandBuilder\Product;

use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;

interface ProductCommandsBuilderInterface
{
    /**
     * @param ProductId $productId
     * @param array $formData
     * @param ShopConstraint $singleShopConstraint
     *
     * @return array Returns empty array if the required data for the command is absent
     */
    public function buildCommands(ProductId $productId, array $formData, ShopConstraint $singleShopConstraint): array;
}
