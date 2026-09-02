<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
