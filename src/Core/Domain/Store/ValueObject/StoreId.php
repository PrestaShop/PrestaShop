<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Store\ValueObject;

use PrestaShop\PrestaShop\Core\Domain\Store\Exception\StoreConstraintException;

/**
 * Contains store ID with it's constraints
 */
class StoreId
{
    /**
     * @var int
     */
    private $storeId;

    /**
     * @param int $storeId
     */
    public function __construct(int $storeId)
    {
        if (0 >= $storeId) {
            throw new StoreConstraintException(sprintf('Invalid store id "%d"', $storeId), StoreConstraintException::INVALID_ID);
        }
        $this->storeId = $storeId;
    }

    /**
     * @return int
     */
    public function getValue(): int
    {
        return $this->storeId;
    }
}
