<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Carrier\Query;

use PrestaShop\PrestaShop\Core\Domain\Carrier\ValueObject\CarrierId;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;

/**
 * Retrieves carrier ranges data
 */
class GetCarrierRanges
{
    private CarrierId $carrierId;

    /**
     * @param int $carrierId
     */
    public function __construct(
        int $carrierId,
        private readonly ShopConstraint $shopConstraint
    ) {
        $this->carrierId = new CarrierId($carrierId);
    }

    /**
     * @return CarrierId
     */
    public function getCarrierId(): CarrierId
    {
        return $this->carrierId;
    }

    public function getShopConstraint(): ShopConstraint
    {
        return $this->shopConstraint;
    }
}
