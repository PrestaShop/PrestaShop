<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject;

use PrestaShop\PrestaShop\Core\Domain\Shop\Exception\ShopException;

/**
 * Shop identity
 */
class ShopId implements ShopIdInterface
{
    /**
     * @var int
     */
    private $shopId;

    /**
     * @param int $shopId
     *
     * @throws ShopException
     */
    public function __construct(int $shopId)
    {
        $this->assertIsGreaterThanZero($shopId);

        $this->shopId = $shopId;
    }

    /**
     * @return int
     */
    public function getValue(): int
    {
        return $this->shopId;
    }

    /**
     * @param int $shopId
     *
     * @throws ShopException
     */
    private function assertIsGreaterThanZero(int $shopId): void
    {
        if (0 >= $shopId) {
            throw new ShopException(
                sprintf(
                    'Shop id %s is invalid. Shop id must be number that is greater than zero.',
                    var_export($shopId, true)
                )
            );
        }
    }
}
