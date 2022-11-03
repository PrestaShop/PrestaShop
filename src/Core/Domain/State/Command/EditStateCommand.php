<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
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
