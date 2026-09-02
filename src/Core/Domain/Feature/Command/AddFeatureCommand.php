<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Feature\Command;

use PrestaShop\PrestaShop\Core\Domain\Feature\Exception\FeatureConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopId;

/**
 * Adds new feature
 */
class AddFeatureCommand
{
    /**
     * @var string[]
     */
    private $localizedNames;

    /**
     * @var ShopId[]
     */
    private $associatedShopIds;

    /**
     * @param string[] $localizedNames
     * @param int[] $shopAssociation
     */
    public function __construct(array $localizedNames, array $shopAssociation)
    {
        $this->assertNamesAreNotEmpty($localizedNames);
        $this->setShopAssociation($shopAssociation);
        $this->localizedNames = $localizedNames;
    }

    /**
     * @return string[]
     */
    public function getLocalizedNames(): array
    {
        return $this->localizedNames;
    }

    /**
     * @return ShopId[]
     */
    public function getAssociatedShopIds(): array
    {
        return $this->associatedShopIds;
    }

    /**
     * @param string[] $names
     *
     * @throws FeatureConstraintException
     */
    private function assertNamesAreNotEmpty(array $names): void
    {
        if (empty($names)) {
            throw new FeatureConstraintException(
                'Feature name cannot be empty',
                FeatureConstraintException::INVALID_NAME
            );
        }
    }

    /**
     * @param int[] $associatedShopIds
     */
    private function setShopAssociation(array $associatedShopIds): void
    {
        if (empty($associatedShopIds)) {
            throw new FeatureConstraintException('Shop association cannot be empty', FeatureConstraintException::INVALID_SHOP_ASSOCIATION);
        }

        $this->associatedShopIds = array_map(static function (int $shopId): ShopId {
            return new ShopId($shopId);
        }, $associatedShopIds);
    }
}
