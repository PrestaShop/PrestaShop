<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\State\QueryResult;

use PrestaShop\PrestaShop\Core\Domain\Country\ValueObject\CountryId;
use PrestaShop\PrestaShop\Core\Domain\State\ValueObject\StateId;
use PrestaShop\PrestaShop\Core\Domain\Zone\ValueObject\ZoneId;

/**
 * Transfers state data for editing
 */
class EditableState
{
    /**
     * @var StateId
     */
    private $stateId;

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
    private $enabled;

    /**
     * @var array<int, int>
     */
    private $associatedShops;

    /**
     * @param StateId $stateId
     * @param CountryId $countryId
     * @param ZoneId $zoneId
     * @param string $name
     * @param string $isoCode
     * @param bool $enabled
     * @param array<int, int> $associatedShops
     */
    public function __construct(
        StateId $stateId,
        CountryId $countryId,
        ZoneId $zoneId,
        string $name,
        string $isoCode,
        bool $enabled,
        array $associatedShops
    ) {
        $this->stateId = $stateId;
        $this->countryId = $countryId;
        $this->zoneId = $zoneId;
        $this->name = $name;
        $this->isoCode = $isoCode;
        $this->enabled = $enabled;
        $this->associatedShops = $associatedShops;
    }

    /**
     * @return StateId
     */
    public function getStateId(): StateId
    {
        return $this->stateId;
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
    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    /**
     * @return array<int, int>
     */
    public function getAssociatedShops(): array
    {
        return $this->associatedShops;
    }
}
