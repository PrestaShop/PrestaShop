<?php
/*
* 2007-2011 PrestaShop 
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
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
*  @version  Release: $Revision: 6839 $
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

/*
** Some tools using used in the module
*/
class MRTools
{
	static public function replaceAccentedCharacters($string)
	{
		$currentLocale = setlocale(LC_ALL, NULL);
		setlocale(LC_ALL, 'en_US.UTF8');
		$cleanedString = iconv('UTF-8','ASCII//TRANSLIT', $string);
		setLocale(LC_ALL, $currentLocale);
		return $cleanedString;
	}
	
	static public function checkZipcodeByCountry($zipcode, $params)
	{
		$id_country = $params['id_country'];
		
		$zipcodeFormat = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('
				SELECT `zip_code_format`
				FROM `'._DB_PREFIX_.'country`
				WHERE `id_country` = '.(int)$id_country);

		if (!$zipcodeFormat)
			return false;

		$regxMask = str_replace(
				array('N', 'C', 'L'),
				array(
					'[0-9]',
					Country::getIsoById((int)$id_country),
					'[a-zA-Z]'),
				$zipcodeFormat);
		if (preg_match('/'.$regxMask.'/', $zipcode))
			return true;
		return false;
	}
}

?>