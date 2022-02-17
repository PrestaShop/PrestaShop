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
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
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
