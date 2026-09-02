<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Zone\QueryResult;

use PrestaShop\PrestaShop\Core\Domain\Zone\ValueObject\ZoneId;

/**
 * Transfers zone data for editing
 */
class EditableZone
{
    /**
     * @var ZoneId
     */
    private $zoneId;

    /**
     * @var string
     */
    private $name;

    /**
     * @var bool
     */
    private $enabled;

    /**
     * @var array
     */
    private $associatedShops;

    /**
     * @param ZoneId $zoneId
     * @param string $name
     * @param bool $enabled
     * @param array $associatedShops
     */
    public function __construct(ZoneId $zoneId, string $name, bool $enabled, array $associatedShops)
    {
        $this->zoneId = $zoneId;
        $this->name = $name;
        $this->enabled = $enabled;
        $this->associatedShops = $associatedShops;
    }

    /**
     * @return ZoneId
     */
    public function getZoneId(): ZoneId
    {
        return $this->zoneId;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return bool
     */
    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    /**
     * @return array
     */
    public function getAssociatedShops(): array
    {
        return $this->associatedShops;
    }
}
