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

namespace PrestaShop\PrestaShop\Core\Util\DateTime;

use DateInterval;
use DateTime;
use DateTimeImmutable;
use DateTimeInterface;
use DateTimeZone;
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
     * {@inheritdoc}
     */
    public function add(DateInterval $interval)
    {
        throw new RuntimeException(
            sprintf(
                'This method should not be used in %s class, it might produce unexpected results',
                static::class
            )
        );
    }

    /**
     * {@inheritdoc}
     */
    public static function createFromFormat($format, $datetime, DateTimeZone $timezone = null)
    {
        throw new RuntimeException(
            sprintf(
                'This method should not be used in %s, it might produce unexpected results',
                static::class
            )
        );
    }

    /**
     * {@inheritdoc}
     */
    public static function createFromMutable(DateTime $object)
    {
        throw new RuntimeException(
            sprintf(
                'This method should not be used in %s, it might produce unexpected results',
                static::class
            )
        );
    }

    /**
     * {@inheritdoc}
     */
    public static function getLastErrors()
    {
        throw new RuntimeException(
            sprintf(
                'This method should not be used in %s, it might produce unexpected results',
                static::class
            )
        );
    }

    /**
     * {@inheritdoc}
     */
    public function modify($modifier)
    {
        throw new RuntimeException(
            sprintf(
                'This method should not be used in %s class, it might produce unexpected results',
                static::class
            )
        );
    }

    /**
     * {@inheritdoc}
     */
    public static function __set_state(array $array)
    {
        throw new RuntimeException(
            sprintf(
                'This method should not be used in %s, it might produce unexpected results',
                static::class
            )
        );
    }

    /**
     * {@inheritdoc}
     */
    public function setDate($year, $month, $day)
    {
        throw new RuntimeException(
            sprintf(
                'This method should not be used in %s class, it might produce unexpected results',
                static::class
            )
        );
    }

    /**
     * {@inheritdoc}
     */
    public function setISODate($year, $week, $dayOfWeek = 1)
    {
        throw new RuntimeException(
            sprintf(
                'This method should not be used in %s class, it might produce unexpected results',
                static::class
            )
        );
    }

    /**
     * {@inheritdoc}
     */
    public function setTime($hour, $minute, $second = 0, $microsecond = 0)
    {
        throw new RuntimeException(
            sprintf(
                'This method should not be used in %s class, it might produce unexpected results',
                static::class
            )
        );
    }

    /**
     * {@inheritdoc}
     */
    public function setTimestamp($timestamp)
    {
        throw new RuntimeException(
            sprintf(
                'This method should not be used in %s class, it might produce unexpected results',
                static::class
            )
        );
    }

    /**
     * {@inheritdoc}
     */
    public function setTimezone(DateTimeZone $timezone)
    {
        throw new RuntimeException(
            sprintf(
                'This method should not be used in %s class, it might produce unexpected results',
                static::class
            )
        );
    }

    /**
     * {@inheritdoc}
     */
    public function sub(DateInterval $interval)
    {
        throw new RuntimeException(
            sprintf(
                'This method should not be used in %s class, it might produce unexpected results',
                static::class
            )
        );
    }

    /**
     * {@inheritdoc}
     */
    public function diff($targetObject, $absolute = false)
    {
        throw new RuntimeException(
            sprintf(
                'This method should not be used in %s class, it might produce unexpected results',
                static::class
            )
        );
    }

    /**
     * {@inheritdoc}
     */
    public function format($format)
    {
        throw new RuntimeException(
            sprintf(
                'This method should not be used in %s class, it might produce unexpected results',
                static::class
            )
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getOffset()
    {
        throw new RuntimeException(
            sprintf(
                'This method should not be used in %s class, it might produce unexpected results',
                static::class
            )
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getTimestamp()
    {
        throw new RuntimeException(
            sprintf(
                'This method should not be used in %s class, it might produce unexpected results',
                static::class
            )
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getTimezone()
    {
        throw new RuntimeException(
            sprintf(
                'This method should not be used in %s class, it might produce unexpected results',
                static::class
            )
        );
    }

    /**
     * {@inheritdoc}
     */
    public function __wakeup()
    {
        throw new RuntimeException(
            sprintf(
                'This method should not be used in %s class, it might produce unexpected results',
                static::class
            )
        );
    }

    /**
     * {@inheritdoc}
     */
    public static function createFromInterface(DateTimeInterface $object)
    {
        throw new RuntimeException(sprintf(
                'This method should not be used in %s, it might produce unexpected results',
                static::class
            )
        );
    }
}
