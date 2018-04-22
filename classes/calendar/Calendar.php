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
 * Class CalendarCore
 */
abstract class CalendarCore
{
    /**
     * @var object
     */
    protected static $instance;

    /**
     * List of calendar types
     * @var array
     */
    protected static $calendars = array(
        1 => 'Gregorian',
        2 => 'Jalali',
    );

    /**
     * @var int
     */
    const DEFAULT_CORE_CALENDAR = 1;
  
    /**
    * Display date according to the settings
    *
    * @param string $dateFormat PHP Date format
    * @param int $timestamp UNIX timestamp
    * @return string Date
    */
    abstract public function displayDate($dateFormat, $timestamp);
	

    /**
     * Get a singleton instance of Calendar object
     *
     * @return Calendar
     */
    public static function getInstance()
    {
        if (!isset(self::$instance)) {
            self::$instance = static::loadCalendarObject();
        }
    
        return self::$instance;
    }

    /**
     * Load Calendar object
     *
     * @return Calendar
     */
    public static function loadCalendarObject()
    {
        $calendarNumber = static::getCalendarNumber();
        $calendarClass = static::$calendars[$calendarNumber] .'Calendar';
    
        if (class_exists($calendarClass) && is_subclass_of($calendarClass, 'CalendarCore')) {
            return new $calendarClass();
        }
    
        throw new PrestaShopException('Unable to load Calendar Object');
    }

    /**
     * @brief Return available calendars
     * @return Array Calendars
     */
    public static function getCalendars()
    {
        return static::$calendars;
    }

    /**
     * get Calendars List
     * @return Array
     */
    public static function getCalendarsList($showDefaultOption = false)
    {
        $translator = Context::getContext()->getTranslator();
        $list = array();
		
		if ($showDefaultOption) {
			$list[] = array(
				'id' => 0,
				'name' => $translator->trans('-- Based on language settings --', array(), 'Admin.Global'),
			);
		}
    
        foreach (static::$calendars as $id => $name) {
            $list[] = array(
                'id' => $id,
                'name' => $translator->trans(sprintf('%s calendar', $name), array(), 'Admin.Global')
            );
        }
    
        return $list;
    }


    /**
     * get Current Calender Number
     * @return int
     */
    public static function getCalendarNumber()
    {
        $context = Context::getContext();
        $controllerType = $context->controller->controller_type;
    
        if ($controllerType == 'front' || $controllerType == 'modulefront') {
            $userCalendarNumber = $context->customer->calendar_type;
        } else {
            $userCalendarNumber = $context->employee->calendar_type;
        }
    
        $calendarPriorities = array(
            $userCalendarNumber,
            $context->language->calendar_type,
        );
    
        foreach ($calendarPriorities as $calendarNumber) {
            if (isset(static::$calendars[$calendarNumber])) {
                return $calendarNumber;
            }
        }
    
        return self::DEFAULT_CORE_CALENDAR;
    }
}
