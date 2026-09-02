<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\AttributeGroup\Validate;

use AttributeGroup;
use PrestaShop\PrestaShop\Adapter\AbstractObjectModelValidator;
use PrestaShop\PrestaShop\Adapter\Shop\Repository\ShopRepository;
use PrestaShop\PrestaShop\Core\Domain\AttributeGroup\Exception\AttributeGroupConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopId;
use PrestaShop\PrestaShop\Core\Exception\CoreException;

/**
 * Validates Attribute Group properties using legacy object model
 */
class AttributeGroupValidator extends AbstractObjectModelValidator
{
    public function __construct(
        private ShopRepository $shopRepository
    ) {
    }

    /**
     * @param AttributeGroup $attributeGroup
     *
     * @throws CoreException
     */
    public function validate(AttributeGroup $attributeGroup): void
    {
        $this->validateObjectModelLocalizedProperty($attributeGroup, 'name', AttributeGroupConstraintException::class, AttributeGroupConstraintException::INVALID_NAME);
        $this->validateObjectModelLocalizedProperty($attributeGroup, 'public_name', AttributeGroupConstraintException::class, AttributeGroupConstraintException::INVALID_PUBLIC_NAME);
        $this->validateObjectModelProperty($attributeGroup, 'group_type', AttributeGroupConstraintException::class, AttributeGroupConstraintException::INVALID_TYPE);
        $this->validateShopsExists($attributeGroup->id_shop_list);
    }

    private function validateShopsExists(array $shopIds): void
    {
        foreach ($shopIds as $shopId) {
            $this->shopRepository->assertShopExists(new ShopId((int) $shopId));
        }
    }
}
