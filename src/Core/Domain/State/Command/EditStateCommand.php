<?php
/**
 * 2007-2019 PrestaShop and Contributors
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Core\Domain\State\Command;

use PrestaShop\PrestaShop\Core\Domain\State\ValueObject\StateId;

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
     * @var int|null
     */
    private $countryId;

    /**
     * @var int|null
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
     * @param StateId $stateId
     */
    public function __construct(StateId $stateId)
    {
        $this->stateId = $stateId;
    }

    /**
     * @return StateId
     */
    public function getStateId(): StateId
    {
        return $this->stateId;
    }

    /**
     * @return int|null
     */
    public function getCountryId(): ?int
    {
        return $this->countryId;
    }

    /**
     * @param int|null $countryId
     *
     * @return EditStateCommand
     */
    public function setCountryId(?int $countryId): EditStateCommand
    {
        $this->countryId = $countryId;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getZoneId(): ?int
    {
        return $this->zoneId;
    }

    /**
     * @param int|null $zoneId
     *
     * @return EditStateCommand
     */
    public function setZoneId(?int $zoneId): EditStateCommand
    {
        $this->zoneId = $zoneId;

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
     * @param string|null $name
     *
     * @return EditStateCommand
     */
    public function setName(?string $name): EditStateCommand
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
     * @param string|null $isoCode
     *
     * @return EditStateCommand
     */
    public function setIsoCode(?string $isoCode): EditStateCommand
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
     * @param bool|null $active
     *
     * @return EditStateCommand
     */
    public function setActive(?bool $active): EditStateCommand
    {
        $this->active = $active;

        return $this;
    }
}
