<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Feature\Command;

use PrestaShop\PrestaShop\Core\Domain\Feature\Exception\FeatureConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Feature\ValueObject\FeatureId;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopId;

/**
 * Edit feature with given data.
 */
class EditFeatureCommand
{
    /**
     * @var FeatureId
     */
    private $featureId;

    /**
     * @var string[]|null
     */
    private $localizedNames;

    /**
     * @var ShopId[]|null
     */
    private $associatedShopIds;

    /**
     * @param int $featureId
     */
    public function __construct(int $featureId)
    {
        $this->featureId = new FeatureId($featureId);
    }

    /**
     * @return FeatureId
     */
    public function getFeatureId(): FeatureId
    {
        return $this->featureId;
    }

    /**
     * @return string[]|null
     */
    public function getLocalizedNames(): ?array
    {
        return $this->localizedNames;
    }

    /**
     * @param string[] $localizedNames
     *
     * @return EditFeatureCommand
     */
    public function setLocalizedNames(array $localizedNames): self
    {
        if (empty($localizedNames)) {
            throw new FeatureConstraintException(
                'Feature name cannot be empty',
                FeatureConstraintException::INVALID_NAME
            );
        }

        $this->localizedNames = $localizedNames;

        return $this;
    }

    /**
     * @return ShopId[]|null
     */
    public function getAssociatedShopIds(): ?array
    {
        return $this->associatedShopIds;
    }

    /**
     * @param int[] $associatedShopIds
     *
     * @return EditFeatureCommand
     */
    public function setAssociatedShopIds(array $associatedShopIds): self
    {
        if (empty($associatedShopIds)) {
            throw new FeatureConstraintException('Shop association cannot be empty', FeatureConstraintException::INVALID_SHOP_ASSOCIATION);
        }

        $this->associatedShopIds = array_map(static function (int $shopId): ShopId {
            return new ShopId($shopId);
        }, $associatedShopIds);

        return $this;
    }
}
