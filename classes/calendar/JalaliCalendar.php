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
 * Class JalaliCalendarCore
 */
class JalaliCalendarCore extends Calendar
{

    /**
    * Display date in Jalali Calendar
    *
    * @param string $dateFormat Date format
    * @param int $timestamp UNIX timestamp
    * @return string
    */
    public function displayDate($dateFormat, $timestamp)
    {
      return $this->toJalalidate($dateFormat, $timestamp);
    }

    /**
     * Display date based on Jalali Date
     * @return String jalali date
     */
    private function toJalalidate($format, $timestamp = null, $numberType = 'fa')
    {
        if (empty($timestamp)) {
            $timestamp = time();
        } else {
            $timestamp = $this->convertNumberType($timestamp);
        }
    
        list(
            $hour,
            $minute,
            $dayOfMonth,
            $month,
            $diffGmtHour,
            $diffGmtHoursAndMinute,
            $second,
            $dayOfWeek,
            $year
        ) = explode('-', date('H-i-j-n-O-P-s-w-Y', $timestamp));
		
        list($jalaliYear, $jalaliMonth, $jalaliDay) = $this->gregorianToJalali($year, $month, $dayOfMonth);
    
        $daysOfYear = 0;
        if ($jalaliMonth < 7) {
            $daysOfYear = (($jalaliMonth - 1) * 31) + $jalaliDay - 1;
        } else {
            $daysOfYear = (($jalaliMonth - 7) * 30) + $jalaliDay + 185;
        }
    
        $kabise = 0;
        if ((($jalaliYear % 33) % 4) - 1 == (int)(($jalaliYear % 33) * 0.05)) {
            $kabise = 1;
        }
    
        $charsNumber = strlen($format);
        $output = '';
	 
        for ($i = 0; $i < $charsNumber; $i++) {
    
            $char = substr($format, $i, 1);
            if ($char == '\\') {
                $output .= substr($format, ++$i, 1);
                continue;
            }
			
            switch ($char) {
    
                case 'B':
                case 'e':
                case 'g':
                case 'G':
                case 'h':
                case 'I':
                case 'T':
                case 'u':
                case 'Z':
                    $output .= date($char, $timestamp);
                    break;
    
                case 'a':
                    if ($hour < 12) {
                        $output .= 'ق.ظ';
                    } else {
                        $output .= 'ب.ظ';
                    }
                    break;
    
                case 'A':
                    if ($hour < 12) {
                        $output .= 'قبل از ظهر';
                    } else {
                        $output .= 'بعد از ظهر';
                    }
                    break;
    
                case 'c':
                    $output .= $jalaliYear . '/' . $jalaliMonth . '/' . $jalaliDay . ' ' . $hour . ':' . $minute . ':' . $second . ' ' . $diffGmtHoursAndMinute;
                    break;
    
                case 'd':
                    if ($jalaliDay < 10) {
                        $output .= '0'. $jalaliDay;
                    } else {
                        $output .= $jalaliDay;
                    }
                    break;
    
                case 'D':
                    $output .= $this->getDayShortName($dayOfWeek);
                    break;
    
                case 'F':
                    $output .= $this->getMonthFullName($jalaliMonth);
                    break;
    
                case 'H':
                    $output .= $hour;
                    break;
    
                case 'i':
                    $output .= $minute;
                    break;
    
                case 'j':
                    $output .= $jalaliDay;
                    break;
    
                case 'l':
                    $output .= $this->getDayFullName($dayOfWeek);
                    break;
    
                case 'L':
                    $output .= $kabise;
                    break;
    
                case 'm':
                    if( $jalaliMonth > 9 ){
                        $output .= $jalaliMonth;
                    } else {
                        $output .= '0' . $jalaliMonth;
                    }
                    break;

                case 'M':
                    $output .= $this->getMonthShortName($jalaliMonth);
                    break;

                case 'n':
                    $output .= $jalaliMonth;
                    break;

                case 'N':
                    $output .= $dayOfWeek + 1;
                    break;

                case 'o':
                    if ($dayOfWeek == 6) {
                        $jalaliDayOfWeek = 0;
                    } else {
                        $jalaliDayOfWeek = $dayOfWeek + 1;
                    }
                    $daysNumberRemainingOfYear = 364 + $kabise - $daysOfYear;

                    if ($jalaliDayOfWeek > ($daysOfYear + 3) && $daysOfYear < 3) {
                        $output .= $jalaliYear - 1;
                    } else {
                        if ((3 - $daysNumberRemainingOfYear) > $jalaliDayOfWeek && $daysNumberRemainingOfYear < 3){
                            $output .= $jalaliYear + 1;
                        } else {
                            $output .= $jalaliYear;
                        }
                    }
                    break;

                case 'O':
				    $output .= $diffGmtHour;
					break;
                
                case 'P':
				    $output .= $diffGmtHoursAndMinute;
                    break;

                case 'r':
                    $dayFullName = $this->getDayFullName($dayOfWeek);
                    $monthFullName = $this->getMonthFullName($jalaliMonth);
                    $output .= $hour . ':' . $minute . ':' . $second . ' ' . $diffGmtHour . ' ' . $dayFullName . ' ' . $jalaliDay . ' ' . $monthFullName . ' ' . $jalaliYear;
                    break;

                case 's':
                    $output .= $second;
                    break;

                case 'S':
                    $output .= 'ام';
                    break;

                case 't':
                    if ($jalaliMonth != 12) {
                        $output .= 31 - (int)($jalaliMonth / 6.5);
                    } else {
                        $output .= $kabise + 29;
                    }
                    break;

                case 'U':
                    $output .= $timestamp;
                    break;

                case 'w':
                    if ($dayOfWeek == 6) {
                        $output .= 0;
                    } else {
                        $output .= $dayOfWeek+1;
                    }
                    break;

                case 'W':
                    if ($dayOfWeek == 6) {
                        $jalaliNormalDayOfWeek = 0;
                    } else {
                        $jalaliNormalDayOfWeek = ($dayOfWeek + 1) - ($daysOfYear % 7);
                    }
                
                    if ($jalaliNormalDayOfWeek < 0) {
                        $jalaliNormalDayOfWeek += 7;
					}

                    $numbersOfWeeks = (int)(($daysOfYear + $jalaliNormalDayOfWeek) / 7);
                    if ($jalaliNormalDayOfWeek < 4) {
                        $numbersOfWeeks++;
                    } else if($numbersOfWeeks < 1) {
						
                        if ((($jalaliYear % 33) % 4) - 2 == (int)(($jalaliYear % 33) * 0.05)) {
                            $result = 5;
                        } else {
                            $result = 4;
                        }

                        if ($jalaliNormalDayOfWeek == 4 || $jalaliNormalDayOfWeek == $result) {
                            $numbersOfWeeks = 53;
                        } else {
                            $numbersOfWeeks = 52;
                        }
                    }

                    $jalaliKabiseDayOfWeek = $jalaliNormalDayOfWeek + $kabise;
                    if ($jalaliKabiseDayOfWeek == 7) {
                        $jalaliKabiseDayOfWeek = 0;
                    }

                    if ($kabise + 363 - $daysOfYear < $jalaliKabiseDayOfWeek && $jalaliKabiseDayOfWeek < 3) {
                        $output .= '01';
                    } else {
                        if ($numbersOfWeeks < 10){
                            $output .= '0' . $numbersOfWeeks;
                        } else {
                            $output .= $numbersOfWeeks;
                        }
                    }
                    break;

                case 'y':
                    $output .= substr($jalaliYear, 2, 2);
                    break;

                case 'Y':
                    $output .= $jalaliYear;
                    break;

                case 'z':
                    $output .= $daysOfYear;
                    break;

                default:
                    $output .= $char;
            }
        }
    
        if ($numberType != 'en') {
            return $this->convertNumberType($output, 'fa', '.');
        } 
		
        return $output;
    }

    /**
     * convert number types to each other
     * @return String
     */
    private function convertNumberType($dateString, $type = 'en', $separator = '٫')
    {
        $englishNumbers = array('0', '1', '2', '3', '4', '5', '6', '7', '8', '9', '.');
        $persianNumbers = array('۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹', $separator);
    
        if ($type == 'fa') {
            $newDateString = str_replace($englishNumbers, $persianNumbers, $dateString);
        } else {
            $newDateString = str_replace($persianNumbers, $englishNumbers, $dateString);
        }
    
        return $newDateString;
    }

    /**
     * retun month full name
     * @return String
     */
    private function getMonthFullName($jalaliMonth)
    {
        $jalaliMonth = (int)$this->convertNumberType($jalaliMonth);
        $months = array('فروردین' ,'اردیبهشت' ,'خرداد' ,'تیر' ,'مرداد' ,'شهریور' ,'مهر' ,'آبان' ,'آذر' ,'دی' ,'بهمن' ,'اسفند');
        return $months[$jalaliMonth - 1];
    }

    /**
     * retun month short name
     * @return String
     */
    private function getMonthShortName($jalaliMonth)
    {
        $jalaliMonth = (int)$this->convertNumberType($jalaliMonth);
        $months = array('فر','ار','خر','تی‍','مر','شه‍','مه‍','آب‍','آذ','دی','به‍','اس‍');
        return $months[$jalaliMonth - 1];
    }

    /**
     * return day full name
     * @return String
     */
    private function getDayFullName($jalaliDay)
    {
        $jalaliDay = (int)$this->convertNumberType($jalaliDay);
        $days = array('یکشنبه','دوشنبه','سه شنبه','چهارشنبه','پنجشنبه','جمعه','شنبه');
        return $days[$jalaliDay];
    }


    /**
     * return day short name
     * @return String
     */
    private function getDayShortName($jalaliDay)
    {
        $jalaliDay = (int)$this->convertNumberType($jalaliDay);
        $days = array('ی','د','س','چ','پ','ج','ش');
        return $days[$jalaliDay];
    }
	
    /**
     * convert gregorian To Jalali
     * @return array
     */
    private function gregorianToJalali($gregorianYear, $gregorianMonth, $gregorianDay)
    {
        list(
		    $gregorianYear, 
			$gregorianMonth, 
			$gregorianDay
		) = explode('_', $this->convertNumberType($gregorianYear . '_' . $gregorianMonth . '_' . $gregorianDay));
		
        $dayNumbersUntilSpecialMonth = array(0, 31, 59, 90, 120, 151, 181, 212, 243, 273, 304, 334);
        if ($gregorianYear > 1600) {
            $jalaliYear = 979;
            $gregorianYear -= 1600;
        }else{
            $jalaliYear = 0;
            $gregorianYear -= 621;
        }
    
        if ($gregorianMonth > 2) {
            $gregorianYear_ = $gregorianYear + 1;
        } else {
            $gregorianYear_ = $gregorianYear;
        }
    
        $days = (365 * $gregorianYear) + ((int)(($gregorianYear_ + 3) / 4)) - ((int)(($gregorianYear_ + 99) / 100)) + ((int)(($gregorianYear_ + 399) / 400)) - 80 + $gregorianDay + $dayNumbersUntilSpecialMonth[$gregorianMonth - 1];
        $jalaliYear += 33 * (int)($days / 12053);
        $days %= 12053;
        $jalaliYear += 4 * (int)($days / 1461);
        $days %= 1461;
        $jalaliYear += (int)(($days - 1) / 365);
		
        if ($days > 365) {
            $days = ($days - 1) % 365;
		}
    
        if ($days < 186) {
            $jalaliMonth = 1 + (int)($days / 31);
            $jalaliDay = 1 + (int)($days % 31);
        }else{
            $jalaliMonth = 7 + (int)(($days - 186) / 30);
            $jalaliDay = 1 + (int)(($days - 186) % 30);
        }
    
        return array($jalaliYear, $jalaliMonth, $jalaliDay);
    }
}
