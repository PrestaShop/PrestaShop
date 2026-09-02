<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\State\Command;

use PrestaShop\PrestaShop\Core\Domain\Country\Exception\CountryConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Country\ValueObject\CountryId;
use PrestaShop\PrestaShop\Core\Domain\State\Exception\StateConstraintException;
use PrestaShop\PrestaShop\Core\Domain\State\ValueObject\StateId;
use PrestaShop\PrestaShop\Core\Domain\Zone\Exception\ZoneException;
use PrestaShop\PrestaShop\Core\Domain\Zone\ValueObject\ZoneId;

/**
 * Edits state with provided data
 */
class EditStateCommand
{
    /**
     * @var StateId
     */
    private $stateId;

    /**
     * @var CountryId|null
     */
    private $countryId;

    /**
     * @var ZoneId|null
     */
    private $zoneId;

    /**
     * @var string|null
     */
    private $name;

    /**
     * @var string|null
     */
    private $isoCode;

    /**
     * @var bool|null
     */
    private $active;

    /**
     * @param int $stateId
     *
     * @throws StateConstraintException
     */
    public function __construct(int $stateId)
    {
        $this->stateId = new StateId($stateId);
    }

    /**
     * @return StateId
     */
    public function getStateId(): StateId
    {
        return $this->stateId;
    }

    /**
     * @return CountryId|null
     */
    public function getCountryId(): ?CountryId
    {
        return $this->countryId;
    }

    /**
     * @param int $countryId
     *
     * @return EditStateCommand
     *
     * @throws CountryConstraintException
     */
    public function setCountryId(int $countryId): EditStateCommand
    {
        $this->countryId = new CountryId($countryId);

        return $this;
    }

    /**
     * @return ZoneId|null
     */
    public function getZoneId(): ?ZoneId
    {
        return $this->zoneId;
    }

    /**
     * @param int $zoneId
     *
     * @return EditStateCommand
     *
     * @throws ZoneException
     */
    public function setZoneId(int $zoneId): EditStateCommand
    {
        $this->zoneId = new ZoneId($zoneId);

        return $this;
    }

    /**
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string $name
     *
     * @return EditStateCommand
     */
    public function setName(string $name): EditStateCommand
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getIsoCode(): ?string
    {
        return $this->isoCode;
    }

    /**
     * @param string $isoCode
     *
     * @return EditStateCommand
     */
    public function setIsoCode(string $isoCode): EditStateCommand
    {
        $this->isoCode = $isoCode;

        return $this;
    }

    /**
     * @return bool|null
     */
    public function getActive(): ?bool
    {
        return $this->active;
    }

    /**
     * @param bool $active
     *
     * @return EditStateCommand
     */
    public function setActive(bool $active): EditStateCommand
    {
        $this->active = $active;

        return $this;
    }
}
