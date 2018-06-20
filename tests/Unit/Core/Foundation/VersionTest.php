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
namespace Tests\Unit\Core\Foundation;

use PrestaShop\PrestaShop\Core\Foundation\Version;
use PrestaShopBundle\Exception\InvalidVersionException;
use PHPUnit\Framework\TestCase;

class VersionTest extends TestCase
{
    /**
     * @var Version
     */
    protected $version;

    const VERSION = '1.2.3.4';
    const MAJOR_VERSION_STRING = '1.2';
    const MAJOR_VERSION = 12;
    const MINOR_VERSION = 3;
    const RELEASE_VERSION = 4;

    public function setUp()
    {
        $this->version = new Version(
            self::VERSION,
            self::MAJOR_VERSION_STRING,
            self::MAJOR_VERSION,
            self::MINOR_VERSION,
            self::RELEASE_VERSION
        );
    }

    public function testGetVersion()
    {
        $this->assertSame(self::VERSION, $this->version->getVersion());
    }

    public function testGetMajorVersionString()
    {
        $this->assertSame(self::MAJOR_VERSION_STRING, $this->version->getMajorVersionString());
    }

    public function testGetMajorVersion()
    {
        $this->assertSame(self::MAJOR_VERSION, $this->version->getMajorVersion());
    }

    public function testGetMinorVersion()
    {
        $this->assertSame(self::MINOR_VERSION, $this->version->getMinorVersion());
    }

    public function testGetReleaseVersion()
    {
        $this->assertSame(self::RELEASE_VERSION, $this->version->getReleaseVersion());
    }

    /**
     * @dataProvider getCompareGreater
     *
     * @param $version string  Version
     * @param $result  boolean Result
     */
    public function testCompareGreaterVersion($version, $result)
    {
        $this->assertEquals($result, $this->version->isGreaterThan($version));
    }

    public function getCompareGreater()
    {
        return [
            ['1.2', true],
            ['1.2.3.1', true],
            ['2.0', false],
            ['1.2.3.5', false],
        ];
    }

    /**
     * @dataProvider getCompareGreaterEqual
     *
     * @param $version string  Version
     * @param $result  boolean Result
     */
    public function testCompareGreaterEqualVersion($version, $result)
    {
        $this->assertEquals($result, $this->version->isGreaterThanOrEqualTo($version));
    }

    public function getCompareGreaterEqual()
    {
        return [
            ['1.2.3.4', true],
            ['1.2.3.1', true],
            ['2.0', false],
            ['1.2.3.5', false],
        ];
    }

    /**
     * @dataProvider getCompareLess
     *
     * @param $version string  Version
     * @param $result  boolean Result
     */
    public function testCompareLessVersion($version, $result)
    {
        $this->assertEquals($result, $this->version->isLessThan($version));
    }

    public function getCompareLess()
    {
        return [
            ['1.2.3.4', false],
            ['1.2.3.1', false],
            ['2.0', true],
            ['1.2.3.5', true],
        ];
    }

    /**
     * @dataProvider getCompareLessEqual
     *
     * @param $version string  Version
     * @param $result  boolean Result
     */
    public function testCompareLessEqualVersion($version, $result)
    {
        $this->assertEquals($result, $this->version->isLessThanOrEqualTo($version));
    }

    public function getCompareLessEqual()
    {
        return [
            ['1.2.3.4', true],
            ['1.2.3.1', false],
            ['2.0', true],
            ['1.2.3.5', true],
        ];
    }

    /**
     * @dataProvider getCompareNotEqual
     *
     * @param $version string  Version
     * @param $result  boolean Result
     */
    public function testCompareNotEqualVersion($version, $result)
    {
        $this->assertEquals($result, $this->version->isNotEqualTo($version));
    }

    public function getCompareNotEqual()
    {
        return [
            ['1.2.3.4', false],
            ['1.2.3.1', true],
            ['2.0', true],
            ['1.2.3.5', true],
        ];
    }

    /**
     * @dataProvider getInvalidVersions
     *
     * @param $version string  Version
     * @param $result  boolean Result
     *
     */
    public function testCheckInvalidVersion($version)
    {
        $this->expectException(InvalidVersionException::class);
        $this->version->isLessThan($version);
    }

    public function getInvalidVersions()
    {
        return [
            ['1.2.3.1.x'],
            ['2.x'],
            ['2   '],
            [' 1  '],
            ['11.'],
            ['.2'],
            ['1.2-beta'],
            ['1.2-dev'],
            ['1.2-rc1'],
        ];
    }
}
