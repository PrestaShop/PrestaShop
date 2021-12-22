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

/**
 * Class responsible of managing the right version of Shop
 * for every internal/external services.
 */
class Version
{
    /**
     * Full version name.
     *
     * @var string
     */
    private $version;

    /**
     * Major version name.
     *
     * @var string
     */
    private $majorVersionString;

    /**
     * Major version.
     *
     * @var int
     */
    private $majorVersion;

    /**
     * Minor version.
     *
     * @var int
     */
    private $minorVersion;

    /**
     * Release version.
     *
     * @var int
     */
    private $releaseVersion;

    /**
     * Initialize version data.
     *
     * @param string $version Version
     */
    public function __construct($version)
    {
        $this->version = $version;
        $versions = explode('.', $version);

        $this->majorVersionString = $versions[0];
        $this->majorVersion = (int) ($versions[0]);
        $this->minorVersion = (int) $versions[1];
        $this->releaseVersion = (int) $versions[2];
    }

    /**
     * Returns the current version.
     *
     * @return string For example "1.7.4.0"
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * Returns the current major version as a string.
     *
     * @return string For example "1.7"
     */
    public function getMajorVersionString()
    {
        return $this->majorVersionString;
    }

    /**
     * Returns the current major version as an integer.
     *
     * @return int For example 17
     */
    public function getMajorVersion()
    {
        return $this->majorVersion;
    }

    /**
     * Returns the current minor version.
     *
     * @return int
     */
    public function getMinorVersion()
    {
        return $this->minorVersion;
    }

    /**
     * Returns the current release version.
     *
     * @return int
     */
    public function getReleaseVersion()
    {
        return $this->releaseVersion;
    }

    /**
     * Returns if the current version is greater than the provided version.
     *
     * @param string $version Must be a valid version string, for example "1.7.4.0"
     *
     * @return bool
     *
     * @throws InvalidArgumentException If the provided version is invalid
     */
    public function isGreaterThan($version)
    {
        return $this->versionCompare($version, '>');
    }

    /**
     * Returns if the current version is greater than or equal to the provided version.
     *
     * @param string $version Must be a valid version string, for example "1.7.4.0"
     *
     * @return bool
     *
     * @throws InvalidArgumentException If the provided version is invalid
     */
    public function isGreaterThanOrEqualTo($version)
    {
        return $this->versionCompare($version, '>=');
    }

    /**
     * Returns if the current version is less than the provided version.
     *
     * @param string $version Must be a valid version string, for example "1.7.4.0"
     *
     * @return bool
     *
     * @throws InvalidArgumentException If the provided version is invalid
     */
    public function isLessThan($version)
    {
        return $this->versionCompare($version, '<');
    }

    /**
     * Returns if the current version is less than or equal to the provided version.
     *
     * @param string $version Must be a valid version string, for example "1.7.4.0"
     *
     * @return bool
     *
     * @throws InvalidArgumentException If the provided version is invalid
     */
    public function isLessThanOrEqualTo($version)
    {
        return $this->versionCompare($version, '<=');
    }

    /**
     * Returns if the current version is equal to the provided version.
     *
     * @param string $version Must be a valid version string, for example "1.7.4.0"
     *
     * @return bool
     *
     * @throws InvalidArgumentException If the provided version is invalid
     */
    public function isEqualTo($version)
    {
        return $this->versionCompare($version, '=');
    }

    /**
     * Returns if the current version is not equal to the provided version.
     *
     * @param string $version Must be a valid version string, for example "1.7.4.0"
     *
     * @return bool
     *
     * @throws InvalidArgumentException If the provided version is invalid
     */
    public function isNotEqualTo($version)
    {
        return $this->versionCompare($version, '!=');
    }

    /**
     * Compares the current version with the provided version depending on the provided operator.
     * It sanitized both version to have a.
     *
     * @param string $version  Must be a valid version string, for example "1.7.4.0"
     * @param string $operator Operator for version_compare(),
     *                  allowed values are: <, lt, <=, le, >, gt, >=, ge, ==, =, eq, !=, <>, ne
     *
     * @return bool result of the comparison
     *
     * @throws InvalidArgumentException if the provided version is invalid
     */
    private function versionCompare($version, $operator)
    {
        $this->assertVersion($version);

        $first = (string) (int) (trim(str_replace('.', '', $this->version)));
        $second = (string) (int) (trim(str_replace('.', '', $version)));
        $firstLen = strlen($first);
        $secondLen = strlen($second);
        if ($firstLen > $secondLen) {
            $second = str_pad($second, $firstLen, '0');
        } elseif ($firstLen < $secondLen) {
            $first = str_pad($first, $secondLen, '0');
        }

        return version_compare($first, $second, $operator);
    }

    /**
     * Checks if a given version is a valid version string.
     *
     * @param string $version
     *
     * @throws InvalidArgumentException If the provided version is invalid
     */
    private function assertVersion($version)
    {
        if (!preg_match('~^\d+(\.\d+){0,}$~', $version)) {
            throw new InvalidArgumentException("Invalid version used: $version");
        }
    }
}
