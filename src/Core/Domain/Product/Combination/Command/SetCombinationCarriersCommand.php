<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Product\Combination\Command;

use PrestaShop\PrestaShop\Core\Domain\Carrier\ValueObject\CarrierReferenceId;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\ValueObject\CombinationId;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;

/**
 * Sets the carriers of a combination, an empty list means the product carriers apply
 */
final class SetCombinationCarriersCommand
{
    private readonly CombinationId $combinationId;

    /**
     * @var CarrierReferenceId[]
     */
    private readonly array $carrierReferenceIds;

    /**
     * @param int[] $carrierReferenceIds
     */
    public function __construct(
        int $combinationId,
        array $carrierReferenceIds,
        private readonly ShopConstraint $shopConstraint
    ) {
        $this->combinationId = new CombinationId($combinationId);
        $this->carrierReferenceIds = array_map(
            static fn ($carrierReferenceId): CarrierReferenceId => new CarrierReferenceId((int) $carrierReferenceId),
            array_values(array_unique($carrierReferenceIds))
        );
    }

    public function getCombinationId(): CombinationId
    {
        return $this->combinationId;
    }

    /**
     * @return CarrierReferenceId[]
     */
    public function getCarrierReferenceIds(): array
    {
        return $this->carrierReferenceIds;
    }

    public function getShopConstraint(): ShopConstraint
    {
        return $this->shopConstraint;
    }
}
