<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Product\Query;

use PrestaShop\PrestaShop\Core\Domain\Language\ValueObject\LanguageId;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;

/**
 * Get Product data necessary for editing
 */
class GetProductForEditing
{
    /**
     * @var ProductId
     */
    private $productId;

    /**
     * @var ShopConstraint
     */
    private $shopConstraint;

    /**
     * @var LanguageId some data from this class is only used for reading and is not expected to be edited, so it
     *                 is provided only in the language of the user interface, which is defined by this parameter
     */
    private $displayLanguageId;

    /**
     * @param int $productId
     * @param ShopConstraint $shopConstraint
     * @param int $displayLanguageId
     */
    public function __construct(
        int $productId,
        ShopConstraint $shopConstraint,
        int $displayLanguageId
    ) {
        $this->productId = new ProductId($productId);
        $this->shopConstraint = $shopConstraint;
        $this->displayLanguageId = new LanguageId($displayLanguageId);
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

    /**
     * @return LanguageId
     */
    public function getDisplayLanguageId(): LanguageId
    {
        return $this->displayLanguageId;
    }
}
