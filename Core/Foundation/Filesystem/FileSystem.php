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

class FileSystem
{
	public function getDirContentRecursive($dir, $is_iterating = false)
	{
		if (!file_exists($dir) || !is_dir($dir))
			return false;

		$content_dir_scanned = scandir($dir);
		$content_list = array();

		if (!$content_dir_scanned)
			return false;

		foreach ($content_dir_scanned as $entry)
		{
			if ($entry != '.' && $entry != '..') {
				if (is_dir($dir . DIRECTORY_SEPARATOR . $entry)) {
					$recurse_iteration = $this->getDirContentRecursive($dir . DIRECTORY_SEPARATOR . $entry, true);
					if ($recurse_iteration)
						$content_list[$entry] = $recurse_iteration;
				} else {
					if ($is_iterating)
						$content_list[] = $entry;
					else
						$content_list['root'][] = $entry;
				}
			}
		}
		return $content_list;
	}
}