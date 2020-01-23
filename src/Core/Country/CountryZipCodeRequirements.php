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

namespace PrestaShop\PrestaShop\Core\Country;

/**
 * Holds information about country zip code requirements
 */
class CountryZipCodeRequirements
{
    /**
     * @var bool
     */
    private $isRequired;

    /**
     * @var string|null
     */
    private $pattern;

    /**
     * @var string|null
     */
    private $humanReadablePattern;

    /**
     * @var string|null
     */
    private $countryName;

    /**
     * @param bool $isRequired
     */
    public function __construct(bool $isRequired)
    {
        $this->isRequired = $isRequired;
    }

    /**
     * @return bool
     */
    public function isRequired(): bool
    {
        return $this->isRequired;
    }

    /**
     * @return string|null
     */
    public function getPattern(): ?string
    {
        return $this->pattern;
    }

    /**
     * @param string $pattern
     * @param string $humanReadablePattern
     *
     * @return CountryZipCodeRequirements
     */
    public function setPatterns(string $pattern, string $humanReadablePattern): CountryZipCodeRequirements
    {
        $this->pattern = $pattern;
        $this->humanReadablePattern = $humanReadablePattern;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getHumanReadablePattern(): ?string
    {
        return $this->humanReadablePattern;
    }

    /**
     * @return string|null
     */
    public function getCountryName(): ?string
    {
        return $this->countryName;
    }

    /**
     * @param string $countryName
     *
     * @return CountryZipCodeRequirements
     */
    public function setCountryName(string $countryName): CountryZipCodeRequirements
    {
        $this->countryName = $countryName;

        return $this;
    }
}
