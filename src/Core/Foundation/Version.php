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

namespace PrestaShop\PrestaShop\Core\Foundation;

use PrestaShop\PrestaShop\Core\Foundation\Exception\InvalidVersionException;

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
     * Patch version.
     *
     * @var int
     */
    private $patchVersion;

    /**
     * Pre release version, (eg. "dev", "beta"...)
     *
     * @var string
     */
    private $preReleaseVersion;

    /**
     * Build metadata (eg. build number)
     *
     * @var string
     */
    private $buildMetadata;

    /**
     * Initialize version data.
     *
     * @param string $version Version
     * @param string $majorVersionString Legacy major version in string format (eg. "1.7")
     * @param int $majorVersion Major version
     * @param int $minorVersion [default=0] Minor version
     * @param int $patchVersion [default=0] Patch version
     * @param string $preReleaseVersion [default=''] Pre release version (eg. "dev", "beta"...)
     * @param string $buildMetadata [default=''] Build metadata (eg. build number)
     */
    public function __construct(
        $version,
        $majorVersionString,
        $majorVersion,
        $minorVersion = 0,
        $patchVersion = 0,
        $preReleaseVersion = '',
        $buildMetadata = ''
    ) {
        $this->version = $this->removeLegacyPrefix($version, $majorVersionString);
        $this->majorVersionString = $majorVersionString;
        $this->majorVersion = $majorVersion;
        $this->minorVersion = $minorVersion;
        $this->patchVersion = $patchVersion;
        $this->preReleaseVersion = $preReleaseVersion;
        $this->buildMetadata = $buildMetadata;
    }

    /**
     * Builds an instance form a version string
     *
     * @param string $version
     *
     * @return self
     *
     * @throws InvalidVersionException If the version is invalid
     */
    public static function buildFromString($version)
    {
        $matches = [];
        $regex = '/^([\d]+)(?:\.([\d]+))?(?:\.([\d]+))?(?:\.(?<legacy>[\d]+))?(?:-(?<prerelease>[0-9A-Za-z-.]+))?(?:\+(?<build>[0-9A-Za-z-.]+))?$/';

        if (!preg_match($regex, $version, $matches)) {
            throw new InvalidVersionException($version);
        }

        if (isset($matches['legacy']) && '' !== $matches['legacy']) {
            // legacy version like "1.7.5.0"
            $major = (int) $matches[2];
            $minor = (int) $matches[3];
            $patch = (int) $matches['legacy'];
            $version = substr($version, strpos($version, '.') + 1);
        } else {
            $major = (int) $matches[1];
            $minor = isset($matches[2]) ? (int) $matches[2] : 0;
            $patch = isset($matches[3]) ? (int) $matches[3] : 0;
        }

        return new self(
            $version,
            "1.$major",
            $major,
            $minor,
            $patch,
            isset($matches['prerelease']) ? $matches['prerelease'] : '',
            isset($matches['build']) ? $matches['build'] : ''
        );
    }

    /**
     * Returns the current version in legacy format (eg. "1.7.6.0")
     *
     * @param bool $full [default=false] If true, include pre-release and build metadata (eg. "1.7.6.0-dev+build.1")
     *
     * @return string
     */
    public function getVersion($full = false)
    {
        $version = '1.' . $this->version;

        if (!$full) {
            // remove extra parts
            return preg_replace('/[-\+].*/', '', $version);
        }

        return $version;
    }

    /**
     * Returns SemVer compliant version (eg. "7.6.0")
     *
     * @return string
     */
    public function getSemVersion()
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
     * Returns the current patch version.
     *
     * @return int
     */
    public function getPatchVersion()
    {
        return $this->patchVersion;
    }

    /**
     * Returns the current pre release version (if any)
     *
     * @return string
     */
    public function getPreReleaseVersion()
    {
        return $this->preReleaseVersion;
    }

    /**
     * Returns the current build metadata (if any)
     *
     * @return string
     */
    public function getBuildMetadata()
    {
        return $this->buildMetadata;
    }

    /**
     * Returns if the current version is greater than the provided version.
     *
     * @param string $version Must be a valid version string, for example "1.7.4.0"
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
     * Returns if the current version is greater than or equal to the provided version.
     *
     * @param string $version Must be a valid version string, for example "1.7.4.0"
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
     * Returns if the current version is less than the provided version.
     *
     * @param string $version Must be a valid version string, for example "1.7.4.0"
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
     * Returns if the current version is less than or equal to the provided version.
     *
     * @param string $version Must be a valid version string, for example "1.7.4.0"
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
     * Returns if the current version is equal to the provided version.
     *
     * @param string $version Must be a valid version string, for example "1.7.4.0"
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
     * Returns if the current version is not equal to the provided version.
     *
     * @param string $version Must be a valid version string, for example "1.7.4.0"
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
     * Returns the semantic version string
     */
    public function __toString()
    {
        return $this->getSemVersion();
    }

    /**
     * Compares the current version with the provided version depending on the provided operator.
     * It sanitized both version to have a.
     *
     * @param string $version Must be a valid version string, for example "1.7.4.0"
     * @param string $operator Operator for version_compare(),
     *                         allowed values are: <, lt, <=, le, >, gt, >=, ge, ==, =, eq, !=, <>, ne
     *
     * @return bool result of the comparison
     *
     * @throws InvalidVersionException if the provided version is invalid
     */
    private function versionCompare($version, $operator)
    {
        $otherVersion = self::buildFromString($version);

        $first = $this->getSemVersion();
        $other = $otherVersion->getSemVersion();

        $result = version_compare($first, $other, $operator);

        return $result;
    }

    /**
     * Remove legacy 1.x prefix if needed
     *
     * @param string $version
     * @param string $majorVersionString
     *
     * @return string
     */
    private function removeLegacyPrefix($version, $majorVersionString)
    {
        if ('1.' === substr($version, 0, 2) && substr($version, 0, strlen($majorVersionString)) === $majorVersionString) {
            $version = substr($version, 2);
        }

        return $version;
    }
}
