<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\State\Command;

use PrestaShop\PrestaShop\Core\Domain\Country\ValueObject\CountryId;
use PrestaShop\PrestaShop\Core\Domain\Zone\ValueObject\ZoneId;

/**
 * Creates state with provided data
 */
class AddStateCommand
{
    /**
     * @var CountryId
     */
    private $countryId;

    /**
     * @var ZoneId
     */
    private $zoneId;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $isoCode;

    /**
     * @var bool
     */
    private $active;

    /**
     * @param int $countryId
     * @param int $zoneId
     * @param string $name
     * @param string $isoCode
     * @param bool $active
     */
    public function __construct(
        int $countryId,
        int $zoneId,
        string $name,
        string $isoCode,
        bool $active
    ) {
        $this->countryId = new CountryId($countryId);
        $this->zoneId = new ZoneId($zoneId);
        $this->name = $name;
        $this->isoCode = $isoCode;
        $this->active = $active;
    }

    /**
     * @return CountryId
     */
    public function getCountryId(): CountryId
    {
        return $this->countryId;
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
     * @return string
     */
    public function getIsoCode(): string
    {
        return $this->isoCode;
    }

    /**
     * @return bool
     */
    public function isActive(): bool
    {
        return $this->active;
    }
}
