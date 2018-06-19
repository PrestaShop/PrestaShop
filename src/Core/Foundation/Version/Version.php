<?php
/**
 * 2007-2018 PrestaShop
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
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */
namespace PrestaShop\PrestaShop\Core\Foundation\Version;

use PrestaShop\PrestaShop\Core\Foundation\Version\Exception\InvalidVersionException;

/**
 * Class responsible of managing the right version of Shop
 * for every internal/external services.
 */
class Version
{
    const STRING = 0;
    const INTEGER = 1;

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


    public function __construct($version, $majorVersionString, $majorVersion, $minorVersion, $releaseVersion)
    {
        $this->version = $version;
        $this->majorVersionString = $majorVersionString;
        $this->majorVersion = $majorVersion;
        $this->minorVersion = $minorVersion;
        $this->releaseVersion = $releaseVersion;
    }

    /**
     * Returns the current PrestaShop version which is hardcoded in \AppKernel::VERSION.
     *
     * @return string For example "1.7.4.0"
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * Returns the current PrestaShop major version as a string.
     *
     * @return string For example "1.7"
     */
    public function getStringMajorVersion()
    {
        return $this->majorVersionString;
    }

    /**
     * Returns the current PrestaShop major version as an integer.
     *
     * @return int For example 17
     */
    public function getMajorVersion()
    {
        return $this->majorVersion;
    }

    /**
     * Returns the current PrestaShop minor version.
     *
     * @return int
     */
    public function getMinorVersion()
    {
        return $this->minorVersion;
    }

    /**
     * Returns the current PrestaShop release version
     *
     * @return int
     */
    public function getReleaseVersion()
    {
        return $this->releaseVersion;
    }

    /**
     * Returns if the current PrestaShop version is greater than the provided version.
     *
     * @param $version Must be a valid PrestaShop version string, for example "1.7.4.0"
     *
     * @return bool
     *
     * @throws InvalidVersionException If the provided version is invalid
     */
    public function isGreaterThan($version)
    {
        return $this->versionCompare($version, '>');
    }

    /**
     * Returns if the current PrestaShop version is greater than or equal to the provided version.
     *
     * @param $version Must be a valid PrestaShop version string, for example "1.7.4.0"
     *
     * @return bool
     *
     * @throws InvalidVersionException If the provided version is invalid
     */
    public function isGreaterThanOrEqualTo($version)
    {
        return $this->versionCompare($version, '>=');
    }

    /**
     * Returns if the current PrestaShop version is less than the provided version.
     *
     * @param $version Must be a valid PrestaShop version string, for example "1.7.4.0"
     *
     * @return bool
     *
     * @throws InvalidVersionException If the provided version is invalid
     */
    public function isLessThan($version)
    {
        return $this->versionCompare($version, '<');
    }

    /**
     * Returns if the current PrestaShop version is less than or equal to the provided version.
     *
     * @param $version Must be a valid PrestaShop version string, for example "1.7.4.0"
     *
     * @return bool
     *
     * @throws InvalidVersionException If the provided version is invalid
     */
    public function isLessThanOrEqualTo($version)
    {
        return $this->versionCompare($version, '<=');
    }

    /**
     * Returns if the current PrestaShop version is equal to the provided version.
     *
     * @param $version Must be a valid PrestaShop version string, for example "1.7.4.0"
     *
     * @return bool
     *
     * @throws InvalidVersionException If the provided version is invalid
     */
    public function isEqualTo($version)
    {
        return $this->versionCompare($version, '=');
    }

    /**
     * Returns if the current PrestaShop version is not equal to the provided version.
     *
     * @param $version Must be a valid PrestaShop version string, for example "1.7.4.0"
     *
     * @return bool
     *
     * @throws InvalidVersionException If the provided version is invalid
     */
    public function isNotEqualTo($version)
    {
        return $this->versionCompare($version, '!=');
    }

    /**
     * Compares the current PrestaShop version with the provided version depending on the provided operator.
     *
     * @param $version Must be a valid PrestaShop version string, for example "1.7.4.0"
     * @param $operator Operator for version_compare(), allowed values are: <, lt, <=, le, >, gt, >=, ge, ==, =, eq, !=, <>, ne
     *
     * @return boolean Result of the comparison.
     *
     * @throws InvalidVersionException If the provided version is invalid.
     */
    private function versionCompare($version, $operator)
    {
        if ($this->checkVersion($version)) {
            return version_compare($this->version, $version, $operator);
        }
    }

    /**
     * Checks if a given version is a valid version string
     *
     * @param $version
     *
     * @return bool true only if version is valid, else throw an exception.
     * @throws InvalidVersionException If the provided version is invalid
     */
    private function checkVersion($version)
    {
        if (!is_string($version)) {
            throw InvalidVersionException::mustBeAString();
        }

        if (!preg_match('/^[0-9]+\.[0-9]+\.[0-9]+\.[0-9]+$/', $version)) {
            throw InvalidVersionException::mustBeValidName();
        }

        return true;
    }
}
