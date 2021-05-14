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
    public function format($format)
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
     * {@inheritdoc}
     */
    public function add($interval)
    {
        throw $this::buildUnusableMethodException('add');
    }

    /**
     * {@inheritdoc}
     */
    public static function createFromFormat($format, $datetime, $timezone = null)
    {
        throw static::buildUnusableMethodException('createFromFormat');
    }

    /**
     * {@inheritdoc}
     */
    public static function createFromMutable($object)
    {
        throw static::buildUnusableMethodException('createFromMutable');
    }

    /**
     * {@inheritdoc}
     */
    public static function getLastErrors()
    {
        throw static::buildUnusableMethodException('getLastErrors');
    }

    /**
     * {@inheritdoc}
     */
    public function modify($modifier)
    {
        throw $this::buildUnusableMethodException('modify');
    }

    /**
     * {@inheritdoc}
     */
    public function setDate($year, $month, $day)
    {
        throw $this::buildUnusableMethodException('setDate');
    }

    /**
     * {@inheritdoc}
     */
    public function setISODate($year, $week, $dayOfWeek = 1)
    {
        throw $this::buildUnusableMethodException('setISODate');
    }

    /**
     * {@inheritdoc}
     */
    public function setTime($hour, $minute, $second = 0, $microsecond = 0)
    {
        throw $this::buildUnusableMethodException('setTime');
    }

    /**
     * {@inheritdoc}
     */
    public function setTimestamp($timestamp)
    {
        throw $this::buildUnusableMethodException('setTimestamp');
    }

    /**
     * {@inheritdoc}
     */
    public function setTimezone($timezone)
    {
        throw $this::buildUnusableMethodException('setTimezone');
    }

    /**
     * {@inheritdoc}
     */
    public function sub($interval)
    {
        throw $this::buildUnusableMethodException('sub');
    }

    /**
     * {@inheritdoc}
     */
    public function diff($targetObject, $absolute = false)
    {
        throw $this::buildUnusableMethodException('diff');
    }

    /**
     * {@inheritdoc}
     */
    public function getOffset()
    {
        throw $this::buildUnusableMethodException('getOffset');
    }

    /**
     * {@inheritdoc}
     */
    public function getTimestamp()
    {
        throw $this::buildUnusableMethodException('getTimestamp');
    }

    /**
     * {@inheritdoc}
     */
    public function getTimezone()
    {
        throw $this::buildUnusableMethodException('getTimezone');
    }

    /**
     * {@inheritdoc}
     */
    public function __wakeup()
    {
        throw $this::buildUnusableMethodException('__wakeup');
    }

    /**
     * {@inheritdoc}
     */
    public static function createFromInterface(DateTimeInterface $object)
    {
        throw static::buildUnusableMethodException('createFromInterface');
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
