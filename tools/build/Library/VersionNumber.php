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

/**
 * Represents a version object
 *
 * @todo: add unit tests
 */
class VersionNumber
{
    /** @var string */
    private $majorNumber;
    /** @var string */
    private $minorNumber;
    /** @var string */
    private $patchNumber;

    /**
     * @param string $prestashopVersionNumber
     *
     * @throws \InvalidArgumentException
     */
    public function __construct($prestashopVersionNumber)
    {
        $split = $this->parseVersionNumber($prestashopVersionNumber);

        if (5 !== count($split)) {
            throw new \InvalidArgumentException(sprintf(
                'Could not parse given PrestaShop version number %s. Wrong format.',
                $prestashopVersionNumber
            ));
        }

        $this->majorNumber = $split[1] . '.' . $split[2];
        $this->minorNumber = $split[3];
        $this->patchNumber = $split[4];
    }

    /**
     * @return bool
     */
    public function isMajorVersion()
    {
        return ('0' === $this->patchNumber);
    }

    /**
     * @return string
     */
    public function getMajorNumber()
    {
        return $this->majorNumber;
    }

    /**
     * @return string
     */
    public function getMinorNumber()
    {
        return $this->minorNumber;
    }

    /**
     * @return string
     */
    public function getPatchNumber()
    {
        return $this->patchNumber;
    }

    /**
     * @param string $number
     *
     * @return array
     */
    private function parseVersionNumber($number)
    {
        $matches = [];
        preg_match("#(\d+)\.(\d+)\.(\d+)\.(\d)#", $number, $matches);

        return $matches;
    }
}
