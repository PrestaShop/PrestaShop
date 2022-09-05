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
declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Util\DateTime;

use DateInterval;
use DateTimeImmutable;
use DateTimeInterface;
use PrestaShop\PrestaShop\Core\Util\DateTime\DateTime as DateTimeUtil;
use RuntimeException;

/**
 * Reflects null value of DateTime. Only format() method should be used, other methods might produce unexpected results
 */
class NullDateTime extends DateTimeImmutable
{
    public function __construct()
    {
        parent::__construct(DateTimeUtil::NULL_DATETIME);
    }

    /**
     * @return array<string, string>
     */
    public static function getSupportedFormats(): array
    {
        return [
            DateTime::DEFAULT_DATE_FORMAT => DateTime::NULL_DATE,
            DateTime::DEFAULT_DATETIME_FORMAT => DateTime::NULL_DATETIME,
        ];
    }

    /**
     * {@inheritdoc}
     *
     * Outputs string representing null date time
     */
    public function format($format): string
    {
        $supportedFormats = $this::getSupportedFormats();
        if (isset($supportedFormats[$format])) {
            return $supportedFormats[$format];
        }

        throw new RuntimeException(
            sprintf('Format "%s" is not supported by %s', $format, get_class($this))
        );
    }

    /**
     * Adds an amount of days, months, years, hours, minutes and seconds
     *
     * @param string|DateInterval $interval
     *
     * @return static
     */
    public function add($interval): DateTimeImmutable
    {
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    #[\ReturnTypeWillChange]
    public static function createFromFormat($format, $datetime, $timezone = null)
    {
        throw self::buildUnusableMethodException('createFromFormat');
    }

    /**
     * {@inheritdoc}
     */
    public static function createFromMutable($object): DateTimeImmutable
    {
        throw self::buildUnusableMethodException('createFromMutable');
    }

    /**
     * {@inheritdoc}
     */
    #[\ReturnTypeWillChange]
    public static function getLastErrors()
    {
        throw self::buildUnusableMethodException('getLastErrors');
    }

    /**
     * {@inheritdoc}
     */
    #[\ReturnTypeWillChange]
    public function modify($modifier)
    {
        throw $this::buildUnusableMethodException('modify');
    }

    /**
     * {@inheritdoc}
     */
    public function setDate($year, $month, $day): DateTimeImmutable
    {
        throw $this::buildUnusableMethodException('setDate');
    }

    /**
     * {@inheritdoc}
     */
    public function setISODate($year, $week, $dayOfWeek = 1): DateTimeImmutable
    {
        throw $this::buildUnusableMethodException('setISODate');
    }

    /**
     * {@inheritdoc}
     */
    public function setTime($hour, $minute, $second = 0, $microsecond = 0): DateTimeImmutable
    {
        throw $this::buildUnusableMethodException('setTime');
    }

    /**
     * {@inheritdoc}
     */
    public function setTimestamp($timestamp): DateTimeImmutable
    {
        throw $this::buildUnusableMethodException('setTimestamp');
    }

    /**
     * {@inheritdoc}
     */
    public function setTimezone($timezone): DateTimeImmutable
    {
        return $this;
    }

    /**
     * Subtracts an amount of days, months, years, hours, minutes and seconds
     *
     * @param string|DateInterval $interval
     *
     * @return static
     */
    public function sub($interval): DateTimeImmutable
    {
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function diff($targetObject, $absolute = false): DateInterval
    {
        throw $this::buildUnusableMethodException('diff');
    }

    /**
     * {@inheritdoc}
     */
    public function getOffset(): int
    {
        throw $this::buildUnusableMethodException('getOffset');
    }

    /**
     * {@inheritdoc}
     */
    public function getTimestamp(): int
    {
        throw $this::buildUnusableMethodException('getTimestamp');
    }

    /**
     * {@inheritdoc}
     */
    #[\ReturnTypeWillChange]
    public function getTimezone()
    {
        throw $this::buildUnusableMethodException('getTimezone');
    }

    /**
     * {@inheritdoc}
     */
    public static function createFromInterface(DateTimeInterface $object): DateTimeImmutable
    {
        throw self::buildUnusableMethodException('createFromInterface');
    }

    /**
     * @param string $method
     *
     * @return RuntimeException
     */
    private static function buildUnusableMethodException(string $method): RuntimeException
    {
        return new RuntimeException(sprintf(
            '%s::%s should not be used, it might produce unexpected results',
            static::class,
            $method
        ));
    }
}
