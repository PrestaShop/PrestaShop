<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Zone\ValueObject;

use PrestaShop\PrestaShop\Core\Domain\Zone\Exception\ZoneException;

/**
 * Defines Zone ID with it's constraints
 */
class ZoneId
{
    /**
     * @var int
     */
    private $zoneId;

    /**
     * @param int $zoneId
     *
     * @throws ZoneException
     */
    public function __construct(int $zoneId)
    {
        $this->assertIntegerIsGreaterThanZero($zoneId);
        $this->zoneId = $zoneId;
    }

    /**
     * @return int
     */
    public function getValue(): int
    {
        return $this->zoneId;
    }

    /**
     * @param int $zoneId
     *
     * @throws ZoneException
     */
    private function assertIntegerIsGreaterThanZero(int $zoneId): void
    {
        if (0 >= $zoneId) {
            throw new ZoneException(sprintf('Zone id %d is invalid. Zone id have to be number bigger than zero.', $zoneId));
        }
    }
}
