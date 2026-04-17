<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Carrier\Command;

use PrestaShop\PrestaShop\Core\Domain\Carrier\ValueObject\CarrierId;
use PrestaShop\PrestaShop\Core\Domain\Carrier\ValueObject\CarrierRangesCollection;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;

/**
 * Command aim to edit carrier range
 */
class SetCarrierRangesCommand
{
    private CarrierId $carrierId;
    private CarrierRangesCollection $ranges;

    public function __construct(
        int $carrierId,
        /* @var array{
         *     id_zone: int,
         *     range_from: float,
         *     range_to: float,
         *     range_price: string,
         * }[] $ranges,
         */
        array $ranges,
        private readonly ShopConstraint $shopConstraint
    ) {
        $this->carrierId = new CarrierId($carrierId);
        $this->ranges = new CarrierRangesCollection($ranges);
    }

    public function getCarrierId(): CarrierId
    {
        return $this->carrierId;
    }

    public function getRanges(): CarrierRangesCollection
    {
        return $this->ranges;
    }

    public function getShopConstraint(): ShopConstraint
    {
        return $this->shopConstraint;
    }
}
