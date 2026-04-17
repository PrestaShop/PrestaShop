<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject;

use PrestaShop\PrestaShop\Core\Domain\Shop\Exception\ShopException;

class ShopGroupId
{
    /**
     * @var int
     */
    private $shopGroupId;

    /**
     * @param int $shopGroupId
     *
     * @throws ShopException
     */
    public function __construct(int $shopGroupId)
    {
        $this->assertIsGreaterThanZero($shopGroupId);

        $this->shopGroupId = $shopGroupId;
    }

    /**
     * @return int
     */
    public function getValue(): int
    {
        return $this->shopGroupId;
    }

    /**
     * @param int $shopGroupId
     *
     * @throws ShopException
     */
    private function assertIsGreaterThanZero(int $shopGroupId): void
    {
        if (0 >= $shopGroupId) {
            throw new ShopException(
                sprintf(
                    'Shop group id %s is invalid. Shop group id must be a number that is greater than zero.',
                    var_export($shopGroupId, true)
                )
            );
        }
    }
}
