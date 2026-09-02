<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Zone\Command;

use PrestaShop\PrestaShop\Core\Domain\Zone\ValueObject\ZoneId;

/**
 * Deletes zone
 */
class DeleteZoneCommand
{
    /**
     * @var ZoneId
     */
    private $zoneId;

    /**
     * @param int $zoneId
     */
    public function __construct(int $zoneId)
    {
        $this->zoneId = new ZoneId($zoneId);
    }

    /**
     * @return ZoneId
     */
    public function getZoneId(): ZoneId
    {
        return $this->zoneId;
    }
}
