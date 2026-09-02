<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Product\Customization\Command;

use PrestaShop\PrestaShop\Core\Domain\Product\Customization\CustomizationField;
use PrestaShop\PrestaShop\Core\Domain\Product\Customization\CustomizationShopConstraintTrait;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use RuntimeException;

/**
 * Sets product customization fields
 */
class SetProductCustomizationFieldsCommand
{
    use CustomizationShopConstraintTrait;

    /**
     * @var ProductId
     */
    private $productId;

    /**
     * @var CustomizationField[]
     */
    private $customizationFields = [];

    /**
     * @var ShopConstraint
     */
    private $shopConstraint;

    /**
     * @param int $productId
     * @param array{'type': int, "localized_names": array<int, string>, "is_required": bool, "added_by_module": bool, "id"?: int|null}[] $customizationFields
     */
    public function __construct(
        int $productId,
        array $customizationFields,
        ShopConstraint $shopConstraint
    ) {
        $this->productId = new ProductId($productId);
        $this->setCustomizationFields($customizationFields);
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
     * @return CustomizationField[]
     */
    public function getCustomizationFields(): array
    {
        return $this->customizationFields;
    }

    /**
     * @return ShopConstraint
     */
    public function getShopConstraint(): ShopConstraint
    {
        return $this->shopConstraint;
    }

    /**
     * @param array{'type': int, "localized_names": array<int, string>, "is_required": bool, "added_by_module": bool, "id"?: int|null}[] $customizationFields $customizationFields
     */
    private function setCustomizationFields(array $customizationFields): void
    {
        if (empty($customizationFields)) {
            throw new RuntimeException(sprintf(
                'Empty customization fields array provided in %s. To remove customization fields use %s',
                self::class,
                RemoveAllCustomizationFieldsFromProductCommand::class
            ));
        }

        foreach ($customizationFields as $customizationField) {
            $this->customizationFields[] = new CustomizationField(
                (int) $customizationField['type'],
                $customizationField['localized_names'],
                (bool) $customizationField['is_required'],
                (bool) $customizationField['added_by_module'],
                isset($customizationField['id']) ? (int) $customizationField['id'] : null
            );
        }
    }
}
