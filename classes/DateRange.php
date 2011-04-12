<?php
/*
* 2007-2011 PrestaShop 
*
* NOTICE OF LICENSE
*
* This source file is subject to the Open Software License (OSL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/osl-3.0.php
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
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2011 PrestaShop SA
*  @version  Release: $Revision: 1.4 $
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

class DateRangeCore extends ObjectModel
{	
	public $time_start;
	public $time_end;

	protected	$fieldsRequired = array ('time_start', 'time_end');	
	protected	$fieldsValidate = array ('time_start' => 'isDate', 'time_end' => 'isDate');

	protected 	$table = 'date_range';
	protected 	$identifier = 'id_date_range';
	
	public function getFields()
	{
		parent::validateFields();
		$fields['time_start'] = pSQL($this->time_start);
		$fields['time_end'] = pSQL($this->time_end);
		return $fields;
	}
	
	public static function getCurrentRange()
	{
		$result = Db::getInstance()->getRow('
		SELECT `id_date_range`, `time_end`
		FROM `'._DB_PREFIX_.'date_range`
		WHERE `time_end` = (SELECT MAX(`time_end`) FROM `'._DB_PREFIX_.'date_range`)');
		if (!$result['id_date_range'] OR strtotime($result['time_end']) < strtotime(date('Y-m-d H:i:s')))
		{
			// The default range is set to 1 day less 1 second (in seconds)
			$rangeSize = 86399;
			$dateRange = new DateRange();
			$dateRange->time_start = date('Y-m-d');
			$dateRange->time_end = strftime('%Y-%m-%d %H:%M:%S', strtotime($dateRange->time_start) + $rangeSize);
			$dateRange->add();
			return $dateRange->id;
		}
		return $result['id_date_range'];
	}
}


