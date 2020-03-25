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

namespace PrestaShop\PrestaShop\Core\Domain\State\QueryResult;

use PrestaShop\PrestaShop\Core\Domain\State\ValueObject\StateId;

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
     * @var int
     */
    private $countryId;

    /**
     * @var int
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
     * @param StateId $stateId
     * @param int $countryId
     * @param int $zoneId
     * @param string $name
     * @param string $isoCode
     * @param bool $active
     */
    public function __construct(
        StateId $stateId,
        int $countryId,
        int $zoneId,
        string $name,
        string $isoCode,
        bool $active
    ) {
        $this->stateId = $stateId;
        $this->countryId = $countryId;
        $this->zoneId = $zoneId;
        $this->name = $name;
        $this->isoCode = $isoCode;
        $this->active = $active;
    }

    /**
     * @return StateId
     */
    public function getStateId(): StateId
    {
        return $this->stateId;
    }

    /**
     * @return int
     */
    public function getCountryId(): int
    {
        return $this->countryId;
    }

    /**
     * @return int
     */
    public function getZoneId(): int
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
