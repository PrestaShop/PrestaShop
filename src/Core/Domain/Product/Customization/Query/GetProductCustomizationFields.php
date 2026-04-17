<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Product\Customization\Query;

use PrestaShop\PrestaShop\Core\Domain\Product\Customization\CustomizationShopConstraintTrait;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;

/**
 * Gets product customization fields
 */
class GetProductCustomizationFields
{
    use CustomizationShopConstraintTrait;

    /**
     * @var ProductId
     */
    private $productId;

    /**
     * @var ShopConstraint
     */
    private $shopConstraint;

    /**
     * @param int $productId
     * @param ShopConstraint $shopConstraint
     */
    public function __construct(
        int $productId,
        ShopConstraint $shopConstraint
    ) {
        $this->productId = new ProductId($productId);
        $this->checkShopConstraint($shopConstraint);
        $this->shopConstraint = $shopConstraint;
    }

    /**
     * @return ProductId
     */
    public function getProductId(): ProductId
    {
        return $this->productId;
    }

    /**
     * @return ShopConstraint
     */
    public function getShopConstraint(): ShopConstraint
    {
        return $this->shopConstraint;
    }
}
