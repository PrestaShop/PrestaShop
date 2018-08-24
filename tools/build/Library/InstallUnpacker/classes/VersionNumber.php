<?php
/**
 * 2007-2018 PrestaShop.
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
class VersionNumber
{
    /**
     * @var float
     */
    private $major;

    /**
     * @var int
     */
    private $minor;

    /**
     * @var int
     */
    private $patch;

    /**
     * @param float $major
     * @param int $minor
     * @param int $patch
     */
    public function __construct($major, $minor, $patch)
    {
        $this->major = $major;
        $this->minor = $minor;
        $this->patch = $patch;
    }

    /**
     * @param string $versionNumberAsString
     *
     * @return VersionNumber
     */
    public static function fromString($versionNumberAsString)
    {
        $regexp = '#^([\d\.]+)\.(\d+)\.(\d+)$#';
        $matches = [];

        $matchingResult = preg_match($regexp, $versionNumberAsString, $matches);

        if (1 !== $matchingResult) {
            throw new \InvalidArgumentException(sprintf(
                'Failed to parse version number %s',
                $versionNumberAsString
            ));
        }

        return new VersionNumber(
            $matches[1],
            $matches[2],
            $matches[3]
        );
    }

    /**
     * @return float
     */
    public function getMajor()
    {
        return $this->major;
    }

    /**
     * @return int
     */
    public function getMinor()
    {
        return $this->minor;
    }

    /**
     * @return int
     */
    public function getPatch()
    {
        return $this->patch;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return sprintf('%g.%d.%d', $this->major, $this->minor, $this->patch);
    }

    /**
     * @param VersionNumber $otherNumber
     *
     * @return int 1 if this version number is higher, -1 if lower, 0 if equal
     */
    public function compare(VersionNumber $otherNumber)
    {
        if ($this->major > $otherNumber->getMajor()) {
            return 1;
        }
        if ($this->major < $otherNumber->getMajor()) {
            return -1;
        }

        if ($this->minor > $otherNumber->getMinor()) {
            return 1;
        }
        if ($this->minor < $otherNumber->getMinor()) {
            return -1;
        }

        if ($this->patch > $otherNumber->getPatch()) {
            return 1;
        }
        if ($this->patch < $otherNumber->getPatch()) {
            return -1;
        }

        return 0;
    }
}
