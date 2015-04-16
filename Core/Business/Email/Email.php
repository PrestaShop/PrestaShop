<?php
/*
* 2007-2015 PrestaShop
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
*  @copyright  2007-2015 PrestaShop SA
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

class Email extends BaseObject
{
	private $filesytem;

	public function __construct(FileSystem $fs)
	{
		// Register dependencies
		$this->filesytem = $fs;
	}

	public function getAvailableMails($lang = null, $dir = null)
	{
		if (is_null($lang))
			$iso_lang = Language::getIsoById((int)Configuration::get('PS_LANG_DEFAULT'));
		else
			$iso_lang = $lang;

		if (is_null($dir))
			$mail_directory = _PS_MAIL_DIR_.$iso_lang.DIRECTORY_SEPARATOR;
		else
			$mail_directory = $dir;

		if (!file_exists($mail_directory))
			return null;

		// @TODO: Make scanned directory dynamic ?
		$mail_directory = $this->filesystem->getDirContentRecursive(_PS_MAIL_DIR_.$iso_lang.DIRECTORY_SEPARATOR);
		// Prestashop Mail should only be at root level
		$mail_directory = $mail_directory['root'];
		$clean_mail_list = array();

		// Remove duplicate .html / .txt / .tpl
		foreach ($mail_directory as $mail) {
			$exploded_filename = explode('.', $mail, 3);
			// Avoid badly named mail templates
			if (is_array($exploded_filename) && count($exploded_filename) == 2) {
				$clean_filename = (string)$exploded_filename[0];
				if (!in_array($clean_filename, $clean_mail_list)) {
					$clean_mail_list[] = $clean_filename;
				}
			}
		}
		return $clean_mail_list;
	}
}