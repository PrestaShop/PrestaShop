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

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Foundation\Exception\InvalidVersionException;
use PrestaShop\PrestaShop\Core\Foundation\Version;

class VersionTest extends TestCase
{
    /**
     * @var Version
     */
    protected $version;

    /**
     * @var Version
     */
    protected $anotherVersion;

    const VERSION = '1.2.3.4';
    const MAJOR_VERSION_STRING = '1.2';
    const MAJOR_VERSION = 2;
    const MINOR_VERSION = 3;
    const RELEASE_VERSION = 4;

    const ANOTHER_VERSION = '1.2.0.0';
    const ANOTHER_MAJOR_VERSION_STRING = '1.2';
    const ANOTHER_MAJOR_VERSION = 2;
    const ANOTHER_MINOR_VERSION = 3;
    const ANOTHER_RELEASE_VERSION = 4;

    protected function setUp()
    {
        $this->version = new Version(
            self::VERSION,
            self::MAJOR_VERSION_STRING,
            self::MAJOR_VERSION,
            self::MINOR_VERSION,
            self::RELEASE_VERSION
        );

        $this->anotherVersion = new Version(
            self::ANOTHER_VERSION,
            self::ANOTHER_MAJOR_VERSION_STRING,
            self::ANOTHER_MAJOR_VERSION,
            self::ANOTHER_MINOR_VERSION,
            self::ANOTHER_RELEASE_VERSION
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
        $this->assertSame(self::RELEASE_VERSION, $this->version->getPatchVersion());
    }

    /**
     * @dataProvider provideVersions
     *
     * @param string $string
     * @param array $expected
     */
    public function testBuildFromString($string, $expected)
    {
        $version = Version::buildFromString($string);

        $this->assertSame($expected['version'], $version->getVersion(), 'Version string is incorrect');
        $this->assertSame($expected['fullVersion'], $version->getVersion(true), 'Full version string is incorrect');
        $this->assertSame($expected['semVersion'], $version->getSemVersion(), 'Semantic version string is incorrect');
        $this->assertSame($expected['majorString'], $version->getMajorVersionString(), 'Major version string is incorrect');
        $this->assertSame($expected['major'], $version->getMajorVersion(), 'Major version is incorrect');
        $this->assertSame($expected['minor'], $version->getMinorVersion(), 'Minor version is incorrect');
        $this->assertSame($expected['patch'], $version->getPatchVersion(), 'Patch version is incorrect');
        $this->assertSame($expected['preRelease'], $version->getPreReleaseVersion(), 'Pre release version is incorrect');
        $this->assertSame($expected['buildMeta'], $version->getBuildMetadata(), 'Build metadata is incorrect');
    }

    /**
     * @dataProvider getCompareGreater
     *
     * @param $version string  Version
     * @param $result  boolean Result
     */
    public function testCompareGreaterVersion($version, $result)
    {
        $this->assertEquals(
            $result,
            $this->version->isGreaterThan($version),
            sprintf('Failed to assert that %s %s greater than %s', $this->version, $this->getVerb($result), $version)
        );
    }

    /**
     * @dataProvider getCompareGreaterEqual
     *
     * @param $version string  Version
     * @param $result  boolean Result
     */
    public function testCompareGreaterEqualVersion($version, $result)
    {
        $this->assertEquals(
            $result,
            $this->version->isGreaterThanOrEqualTo($version),
            sprintf(
                'Failed to assert that %s %s greater or equal to %s',
                $this->version,
                $this->getVerb($result),
                $version
            )
        );
    }

    /**
     * @dataProvider getCompareLess
     *
     * @param $version string  Version
     * @param $result  boolean Result
     */
    public function testCompareLessVersion($version, $result)
    {
        $this->assertEquals(
            $result,
            $this->version->isLessThan($version),
            sprintf(
                'Failed to assert that %s %s less than %s',
                $this->version,
                $this->getVerb($result),
                $version
            )
        );
    }

    /**
     * @dataProvider getAnotherCompareGreater
     *
     * @param $version string  Version
     * @param $result  boolean Result
     */
    public function testCompareGreaterAnotherVersion($version, $result)
    {
        $this->assertEquals(
            $result,
            $this->anotherVersion->isGreaterThan($version),
            sprintf(
                'Failed to assert that %s %s greater than %s',
                $this->anotherVersion,
                $this->getVerb($result),
                $version
            )
        );
    }

    /**
     * @dataProvider getCompareLessEqual
     *
     * @param $version string  Version
     * @param $result  boolean Result
     */
    public function testCompareLessEqualVersion($version, $result)
    {
        $this->assertEquals(
            $result,
            $this->version->isLessThanOrEqualTo($version),
            sprintf(
                'Failed to assert that %s %s less or equal to %s',
                $this->version,
                $this->getVerb($result),
                $version
            )
        );
    }

    /**
     * @dataProvider getCompareEqual
     *
     * @param $version string  Version
     * @param $result  boolean Result
     */
    public function testCompareEqualVersion($version, $result)
    {
        $this->assertEquals(
            $result,
            $this->version->isEqualTo($version),
            sprintf(
                'Failed to assert that %s %s equal to %s',
                $this->version,
                $this->getVerb($result),
                $version
            )
        );
    }

    /**
     * @dataProvider getCompareNotEqual
     *
     * @param $version string  Version
     * @param $result  boolean Result
     */
    public function testCompareNotEqualVersion($version, $result)
    {
        $this->assertEquals(
            $result,
            $this->version->isNotEqualTo($version),
            sprintf(
                'Failed to assert that %s %s equal to %s',
                $this->version,
                $this->getVerb(!$result),
                $version
            )
        );
    }

    /**
     * @dataProvider getInvalidVersions
     *
     * @param $version string  Version
     * @param $result  boolean Result
     */
    public function testCheckInvalidVersion($version)
    {
        $this->expectException(InvalidVersionException::class);
        $this->version->isLessThan($version);
    }

    public function provideVersions()
    {
        return [
            '1.7.5.0' => [
                '1.7.5.0',
                [
                    'version' => '1.7.5.0',
                    'fullVersion' => '1.7.5.0',
                    'semVersion' => '7.5.0',
                    'majorString' => '1.7',
                    'major' => 7,
                    'minor' => 5,
                    'patch' => 0,
                    'preRelease' => '',
                    'buildMeta' => '',
                ],
            ],
            '1.7.5.1' => [
                '1.7.5.1',
                [
                    'version' => '1.7.5.1',
                    'fullVersion' => '1.7.5.1',
                    'semVersion' => '7.5.1',
                    'majorString' => '1.7',
                    'major' => 7,
                    'minor' => 5,
                    'patch' => 1,
                    'preRelease' => '',
                    'buildMeta' => '',
                ],
            ],
            '1.7.6.0' => [
                '1.7.6.0',
                [
                    'version' => '1.7.6.0',
                    'fullVersion' => '1.7.6.0',
                    'semVersion' => '7.6.0',
                    'majorString' => '1.7',
                    'major' => 7,
                    'minor' => 6,
                    'patch' => 0,
                    'preRelease' => '',
                    'buildMeta' => '',
                ],
            ],
            '1.7.6.0-dev' => [
                '1.7.6.0-dev',
                [
                    'version' => '1.7.6.0',
                    'fullVersion' => '1.7.6.0-dev',
                    'semVersion' => '7.6.0-dev',
                    'majorString' => '1.7',
                    'major' => 7,
                    'minor' => 6,
                    'patch' => 0,
                    'preRelease' => 'dev',
                    'buildMeta' => '',
                ],
            ],
            '1.7.6.0+test.build' => [
                '1.7.6.0+test.build',
                [
                    'version' => '1.7.6.0',
                    'fullVersion' => '1.7.6.0+test.build',
                    'semVersion' => '7.6.0+test.build',
                    'majorString' => '1.7',
                    'major' => 7,
                    'minor' => 6,
                    'patch' => 0,
                    'preRelease' => '',
                    'buildMeta' => 'test.build',
                ],
            ],
            '1.7.7.0-beta.1+build.156' => [
                '1.7.7.0-beta.1+build.156',
                [
                    'version' => '1.7.7.0',
                    'fullVersion' => '1.7.7.0-beta.1+build.156',
                    'semVersion' => '7.7.0-beta.1+build.156',
                    'majorString' => '1.7',
                    'major' => 7,
                    'minor' => 7,
                    'patch' => 0,
                    'preRelease' => 'beta.1',
                    'buildMeta' => 'build.156',
                ],
            ],
            '1.7.7.0-dev+nightly.20190526' => [
                '1.7.7.0-dev+nightly.20190526',
                [
                    'version' => '1.7.7.0',
                    'fullVersion' => '1.7.7.0-dev+nightly.20190526',
                    'semVersion' => '7.7.0-dev+nightly.20190526',
                    'majorString' => '1.7',
                    'major' => 7,
                    'minor' => 7,
                    'patch' => 0,
                    'preRelease' => 'dev',
                    'buildMeta' => 'nightly.20190526',
                ],
            ],
            '7.5.0' => [
                '7.5.0',
                [
                    'version' => '1.7.5.0',
                    'fullVersion' => '1.7.5.0',
                    'semVersion' => '7.5.0',
                    'majorString' => '1.7',
                    'major' => 7,
                    'minor' => 5,
                    'patch' => 0,
                    'preRelease' => '',
                    'buildMeta' => '',
                ],
            ],
            '7.5.1' => [
                '7.5.1',
                [
                    'version' => '1.7.5.1',
                    'fullVersion' => '1.7.5.1',
                    'semVersion' => '7.5.1',
                    'majorString' => '1.7',
                    'major' => 7,
                    'minor' => 5,
                    'patch' => 1,
                    'preRelease' => '',
                    'buildMeta' => '',
                ],
            ],
            '7.6.0' => [
                '7.6.0',
                [
                    'version' => '1.7.6.0',
                    'fullVersion' => '1.7.6.0',
                    'semVersion' => '7.6.0',
                    'majorString' => '1.7',
                    'major' => 7,
                    'minor' => 6,
                    'patch' => 0,
                    'preRelease' => '',
                    'buildMeta' => '',
                ],
            ],
            '7.6.0-dev' => [
                '7.6.0-dev',
                [
                    'version' => '1.7.6.0',
                    'fullVersion' => '1.7.6.0-dev',
                    'semVersion' => '7.6.0-dev',
                    'majorString' => '1.7',
                    'major' => 7,
                    'minor' => 6,
                    'patch' => 0,
                    'preRelease' => 'dev',
                    'buildMeta' => '',
                ],
            ],
            '7.6.0+test.build' => [
                '7.6.0+test.build',
                [
                    'version' => '1.7.6.0',
                    'fullVersion' => '1.7.6.0+test.build',
                    'semVersion' => '7.6.0+test.build',
                    'majorString' => '1.7',
                    'major' => 7,
                    'minor' => 6,
                    'patch' => 0,
                    'preRelease' => '',
                    'buildMeta' => 'test.build',
                ],
            ],
            '7.0-beta.1+build.156' => [
                '7.7.0-beta.1+build.156',
                [
                    'version' => '1.7.7.0',
                    'fullVersion' => '1.7.7.0-beta.1+build.156',
                    'semVersion' => '7.7.0-beta.1+build.156',
                    'majorString' => '1.7',
                    'major' => 7,
                    'minor' => 7,
                    'patch' => 0,
                    'preRelease' => 'beta.1',
                    'buildMeta' => 'build.156',
                ],
            ],
            '7.7.0-dev+nightly.20190526' => [
                '7.7.0-dev+nightly.20190526',
                [
                    'version' => '1.7.7.0',
                    'fullVersion' => '1.7.7.0-dev+nightly.20190526',
                    'semVersion' => '7.7.0-dev+nightly.20190526',
                    'majorString' => '1.7',
                    'major' => 7,
                    'minor' => 7,
                    'patch' => 0,
                    'preRelease' => 'dev',
                    'buildMeta' => 'nightly.20190526',
                ],
            ],
            '8.1.2' => [
                '8.1.2',
                [
                    'version' => '1.8.1.2',
                    'fullVersion' => '1.8.1.2',
                    'semVersion' => '8.1.2',
                    'majorString' => '1.8',
                    'major' => 8,
                    'minor' => 1,
                    'patch' => 2,
                    'preRelease' => '',
                    'buildMeta' => '',
                ],
            ],
        ];
    }

    public function getCompareGreater()
    {
        return [
            ['1.2.3.4', false],
            ['1', true],
            ['1.2', true],
            ['1.2.3', true],
            ['2', true],
            ['2.0', true],
            ['1.3', true],
            ['1.2.4', true],
            ['1.2.3.5', false],
            ['1.1', true],
            ['1.2.2', true],
            ['1.2.3.3', true],
        ];
    }

    public function getCompareGreaterEqual()
    {
        return [
            ['1.2.3.4', true],
            ['1', true],
            ['1.2', true],
            ['1.2.3', true],
            ['2', true],
            ['2.0', true],
            ['1.3', true],
            ['1.2.4', true],
            ['1.2.3.5', false],
            ['1.1', true],
            ['1.2.2', true],
            ['1.2.3.3', true],
        ];
    }

    public function getCompareLess()
    {
        return [
            ['1.2.3.4', false],
            ['1', false],
            ['1.2', false],
            ['1.2.3', false],
            ['2', false],
            ['2.0', false],
            ['1.3', false],
            ['1.2.4', false],
            ['1.2.3.5', true],
            ['1.1', false],
            ['1.2.2', false],
            ['1.2.3.3', false],
        ];
    }

    public function getAnotherCompareGreater()
    {
        return [
            ['1.2.0', true],
        ];
    }

    public function getCompareLessEqual()
    {
        return [
            ['1.2.3.4', true],
            ['1', false],
            ['1.2', false],
            ['1.2.3', false],
            ['2', false],
            ['2.0', false],
            ['1.3', false],
            ['1.2.4', false],
            ['1.2.3.5', true],
            ['1.1', false],
            ['1.2.2', false],
            ['1.2.3.3', false],
        ];
    }

    public function getCompareEqual()
    {
        return [
            ['1.2.3.4', true],
            ['1', false],
            ['1.2', false],
            ['1.2.3', false],
            ['2', false],
            ['2.0', false],
            ['1.3', false],
            ['1.2.4', false],
            ['1.2.3.5', false],
            ['1.1', false],
            ['1.2.2', false],
            ['1.2.3.3', false],
        ];
    }

    public function getCompareNotEqual()
    {
        return [
            ['1.2.3.4', false],
            ['1', true],
            ['1.2', true],
            ['1.2.3', true],
            ['2', true],
            ['2.0', true],
            ['1.3', true],
            ['1.2.4', true],
            ['1.2.3.5', true],
            ['1.1', true],
            ['1.2.2', true],
            ['1.2.3.3', true],
        ];
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
            ['1.2-beta_1'],
            ['1.2+dev@beta'],
            ['1.2#hashtag'],
        ];
    }

    /**
     * @param $result
     *
     * @return string
     */
    private function getVerb($result)
    {
        return $result ? 'is' : 'is NOT';
    }
}
