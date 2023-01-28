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

    public const VERSION = '1.2.3.4';
    public const MAJOR_VERSION_STRING = '1.2';
    public const MAJOR_VERSION = 2;
    public const MINOR_VERSION = 3;
    public const RELEASE_VERSION = 4;

    public const ANOTHER_VERSION = '1.2.0.0';
    public const ANOTHER_MAJOR_VERSION_STRING = '1.2';
    public const ANOTHER_MAJOR_VERSION = 2;
    public const ANOTHER_MINOR_VERSION = 3;
    public const ANOTHER_RELEASE_VERSION = 4;

    protected function setUp(): void
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

    public function testGetVersion(): void
    {
        $this->assertSame(self::VERSION, $this->version->getVersion());
    }

    public function testGetMajorVersionString(): void
    {
        $this->assertSame(self::MAJOR_VERSION_STRING, $this->version->getMajorVersionString());
    }

    public function testGetMajorVersion(): void
    {
        $this->assertSame(self::MAJOR_VERSION, $this->version->getMajorVersion());
    }

    public function testGetMinorVersion(): void
    {
        $this->assertSame(self::MINOR_VERSION, $this->version->getMinorVersion());
    }

    public function testGetReleaseVersion(): void
    {
        $this->assertSame(self::RELEASE_VERSION, $this->version->getPatchVersion());
    }

    /**
     * @dataProvider provideVersions
     *
     * @param string $string
     * @param array $expected
     */
    public function testBuildFromString(string $string, array $expected): void
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
     * @param string $version Version
     * @param bool $result Result
     */
    public function testCompareGreaterVersion(string $version, bool $result): void
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
     * @param string $version Version
     * @param bool $result Result
     */
    public function testCompareGreaterEqualVersion(string $version, bool $result): void
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
     * @param string $version Version
     * @param bool $result Result
     */
    public function testCompareLessVersion(string $version, bool $result): void
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
     * @param string $version Version
     * @param bool $result Result
     */
    public function testCompareGreaterAnotherVersion(string $version, bool $result): void
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
     * @param string $version Version
     * @param bool $result Result
     */
    public function testCompareLessEqualVersion(string $version, bool $result): void
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
     * @param string $version Version
     * @param bool $result Result
     */
    public function testCompareEqualVersion(string $version, bool $result): void
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
     * @param string $version Version
     * @param bool $result Result
     */
    public function testCompareNotEqualVersion(string $version, bool $result): void
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
     * @dataProvider getTwoVersionsToCompare
     *
     * @param string $first Version
     * @param string $second Version
     * @param string $expectedComparison Comparison character
     *
     * @throws InvalidVersionException
     */
    public function testCompareTwoVersions(string $first, string $second, string $expectedComparison)
    {
        $firstVersion = Version::buildFromString($first);
        $secondVersion = Version::buildFromString($second);

        if ($expectedComparison === '<') {
            $this->assertTrue(
                $firstVersion->isLessThan($secondVersion),
                sprintf(
                    'Failed to assert that %s is less than %s',
                    $firstVersion,
                    $secondVersion
                )
            );
        } elseif ($expectedComparison === '>') {
            $this->assertTrue(
                $firstVersion->isGreaterThan($secondVersion),
                sprintf(
                    'Failed to assert that %s is greater than %s',
                    $firstVersion,
                    $secondVersion
                )
            );
        } else {
            $this->assertTrue(
                $firstVersion->isEqualTo($secondVersion),
                sprintf(
                    'Failed to assert that %s is equal to %s',
                    $firstVersion,
                    $secondVersion
                )
            );
        }
    }

    /**
     * @dataProvider getInvalidVersions
     *
     * @param string $version Version
     */
    public function testCheckInvalidVersion($version)
    {
        $this->expectException(InvalidVersionException::class);
        $this->version->isLessThan($version);
    }

    public function provideVersions(): array
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
     * @param bool $result
     *
     * @return string
     */
    private function getVerb(bool $result): string
    {
        return $result ? 'is' : 'is NOT';
    }

    public function getTwoVersionsToCompare(): array
    {
        return [
            // incremental build versions
            ['1.7.7.0+build.1', '1.7.7.0+build.2', '<'],
            // incremental nightly versions
            ['1.7.7.0-dev+nightly.20190526', '1.7.7.0-dev+nightly.20190527', '<'],
            // dev is less than alpha
            ['1.7.7.0-dev+nightly.20190526', '1.7.7.0-alpha.1+build.156', '<'],
            // alpha 1 is less than alpha 2
            ['1.7.7.0-alpha.1', '1.7.7.0-alpha.2', '<'],
            // alpha is less than beta
            ['1.7.7.0-alpha.1', '1.7.7.0-beta.1', '<'],
            // beta is less than RC
            ['1.7.7.0-beta.1', '1.7.7.0-RC.1', '<'],
            // RC is less than final
            ['1.7.7.0-RC.1', '1.7.7.0', '<'],
        ];
    }
}
