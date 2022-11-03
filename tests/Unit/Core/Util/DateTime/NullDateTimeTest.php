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
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */
declare(strict_types=1);

namespace Tests\Unit\Core\Util\DateTime;

use DateTime;
use DateTimeZone;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Util\DateTime\NullDateTime;
use RuntimeException;

class NullDateTimeTest extends TestCase
{
    /**
     * @dataProvider getSupportedDateFormats
     *
     * @param array<string, string> $expected
     */
    public function testItReturnsSupportedFormats(array $expected): void
    {
        $actual = NullDateTime::getSupportedFormats();
        Assert::assertEquals($expected, $actual);
    }

    /**
     * @dataProvider getValidDataForDateFormatting
     *
     * @param string $format
     * @param string $expected
     */
    public function testItFormatsDateTimeToString(string $format, string $expected): void
    {
        $nullDateTime = new NullDateTime();

        Assert::assertEquals($expected, $nullDateTime->format($format));
    }

    /**
     * @dataProvider getUnsupportedDateFormats
     *
     * @param string $format
     */
    public function testItThrowsExceptionWhenUnsupportedFormatIsProvided(string $format): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage(sprintf(
            'Format "%s" is not supported by %s', $format, NullDateTime::class
        ));
        $nullDateTime = new NullDateTime();
        $nullDateTime->format($format);
    }

    public function testMethodAddChangesNothing(): void
    {
        $nullDateTime = new NullDateTime();
        $modifiedDateTime = $nullDateTime->add('+1 day');
        $this->assertEquals($nullDateTime, $modifiedDateTime);
    }

    public function testMethodCreateFromFormatShouldThrowException(): void
    {
        $this->expectUnusableMethodException('createFromFormat');
        NullDateTime::createFromFormat('Y-m-d', '2001-01-01');
    }

    public function testMethodCreateFromMutableShouldThrowException(): void
    {
        $this->expectUnusableMethodException('createFromMutable');
        NullDateTime::createFromMutable(new DateTime());
    }

    public function testMethodGetLastErrorsShouldThrowException(): void
    {
        $this->expectUnusableMethodException('getLastErrors');
        NullDateTime::getLastErrors();
    }

    public function testMethodModifyShouldThrowException(): void
    {
        $this->expectUnusableMethodException('modify');
        $nullDateTime = new NullDateTime();
        $nullDateTime->modify('+1 day');
    }

    public function testMethodSetDateShouldThrowException(): void
    {
        $this->expectUnusableMethodException('setDate');
        $nullDateTime = new NullDateTime();
        $nullDateTime->setDate(1990, 1, 1);
    }

    public function testMethodSetISODateShouldThrowException(): void
    {
        $this->expectUnusableMethodException('setISODate');
        $nullDateTime = new NullDateTime();
        $nullDateTime->setISODate(1990, 1);
    }

    public function testMethodSetTimeShouldThrowException(): void
    {
        $this->expectUnusableMethodException('setTime');
        $nullDateTime = new NullDateTime();
        $nullDateTime->setTime(13, 5);
    }

    public function testMethodSetTimestampShouldThrowException(): void
    {
        $this->expectUnusableMethodException('setTimestamp');
        $nullDateTime = new NullDateTime();
        $nullDateTime->setTimestamp(0);
    }

    public function testMethodSetTimezoneShouldThrowException(): void
    {
        $nullDateTime = new NullDateTime();
        $modifiedDateTime = $nullDateTime->setTimezone(new DateTimeZone('UTC'));
        $this->assertEquals($nullDateTime, $modifiedDateTime);
    }

    public function testMethodSubShouldThrowException(): void
    {
        $nullDateTime = new NullDateTime();
        $modifiedDateTime = $nullDateTime->sub('-1 day');
        $this->assertEquals($nullDateTime, $modifiedDateTime);
    }

    public function testMethodDiffShouldThrowException(): void
    {
        $this->expectUnusableMethodException('diff');
        $nullDateTime = new NullDateTime();
        $nullDateTime->diff(new DateTime());
    }

    public function testMethodGetOffsetShouldThrowException(): void
    {
        $this->expectUnusableMethodException('getOffset');
        $nullDateTime = new NullDateTime();
        $nullDateTime->getOffset();
    }

    public function testMethodGetTimestampShouldThrowException(): void
    {
        $this->expectUnusableMethodException('getTimestamp');
        $nullDateTime = new NullDateTime();
        $nullDateTime->getTimestamp();
    }

    public function testMethodGetTimezoneShouldThrowException(): void
    {
        $this->expectUnusableMethodException('getTimezone');
        $nullDateTime = new NullDateTime();
        $nullDateTime->getTimezone();
    }

    public function testMethodCreateFromInterfaceShouldThrowException(): void
    {
        $this->expectUnusableMethodException('createFromInterface');
        NullDateTime::createFromInterface(new DateTime());
    }

    /**
     * @return iterable
     */
    public function getValidDataForDateFormatting(): iterable
    {
        yield ['Y-m-d', '0000-00-00'];
        yield ['Y-m-d H:i:s', '0000-00-00 00:00:00'];
    }

    /**
     * @return iterable
     */
    public function getSupportedDateFormats(): iterable
    {
        yield [
            [
                'Y-m-d' => '0000-00-00',
                'Y-m-d H:i:s' => '0000-00-00 00:00:00',
            ],
        ];
    }

    /**
     * @return iterable
     */
    public function getUnsupportedDateFormats(): iterable
    {
        yield ['d-m-Y'];
        yield ['H:i:s'];
        yield ['Y-m-d H:i'];
        yield ['Y-m'];
        yield ['undefined'];
    }

    /**
     * @param string $method
     */
    private function expectUnusableMethodException(string $method): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage(
            sprintf(
                '%s::%s should not be used, it might produce unexpected results',
                NullDateTime::class,
                $method
            )
        );
    }
}
