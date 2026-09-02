<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Attribute\Validate;

use PrestaShop\PrestaShop\Adapter\AbstractObjectModelValidator;
use PrestaShop\PrestaShop\Adapter\Shop\Repository\ShopRepository;
use PrestaShop\PrestaShop\Core\Domain\AttributeGroup\Attribute\Exception\AttributeConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopId;
use PrestaShop\PrestaShop\Core\Exception\CoreException;
use ProductAttribute;

/**
 * Validates attribute properties using legacy object model
 */
class AttributeValidator extends AbstractObjectModelValidator
{
    public function __construct(
        private ShopRepository $shopRepository
    ) {
    }

    /**
     * @param ProductAttribute $attribute
     *
     * @throws CoreException
     */
    public function validate(ProductAttribute $attribute): void
    {
        $this->validateObjectModelLocalizedProperty($attribute, 'name', AttributeConstraintException::class, AttributeConstraintException::INVALID_NAME);
        $this->validateObjectModelProperty($attribute, 'color', AttributeConstraintException::class, AttributeConstraintException::INVALID_COLOR);
        $this->validateObjectModelProperty($attribute, 'id_attribute_group', AttributeConstraintException::class, AttributeConstraintException::INVALID_ATTRIBUTE_GROUP_ID);
        $this->validateShopsExists($attribute->id_shop_list);
    }

    private function validateShopsExists(array $shopIds): void
    {
        foreach ($shopIds as $shopId) {
            $this->shopRepository->assertShopExists(new ShopId((int) $shopId));
        }
    }
}
