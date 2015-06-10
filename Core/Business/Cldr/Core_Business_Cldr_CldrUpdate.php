<?php
/**
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
 *  @author 	PrestaShop SA <contact@prestashop.com>
 *  @copyright  2007-2015 PrestaShop SA
 *  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */

class Core_Business_Cldr_CldrUpdate extends Core_Business_Cldr_CldrRepository
{
	const ZIP_CORE_URL = 'http://www.unicode.org/Public/cldr/26/json-full.zip';

	public function __construct()
	{
		parent::__construct();
	}

	public function fetch(){

		if (!is_file($file = $this->cldrCacheFolder.DIRECTORY_SEPARATOR.'core.zip')) {
			$fp = fopen($file, "w");
			$curl = new Core_Foundation_Net_Curl();
			$curl->setOptions(array(
				CURLOPT_FILE => $fp,
				CURLOPT_FOLLOWLOCATION => TRUE,
				CURLOPT_HEADER => FALSE
			));

			if ($curl->exec(self::ZIP_CORE_URL) === FALSE) {
				throw new \Exception("Failed to download from '".
					self::ZIP_CORE_URL."'.");
			};

			$curl->close();
			fclose($fp);

			$archive = new \ZipArchive();

			if ($archive->open($file) === TRUE)	{
				$archive->extractTo($this->cldrCacheFolder.DIRECTORY_SEPARATOR.'tmp');
				$archive->close();
			} else {
				throw new \Exception("Failed to unzip '".$file."'.");
			}
		}

		$this->generateSupplementalDatas();
		$this->generateMainDatas();
	}

	private function generateSupplementalDatas(){
		$rootPath = $this->cldrCacheFolder.DIRECTORY_SEPARATOR.'tmp'.DIRECTORY_SEPARATOR;
		$files = @scandir($rootPath.'supplemental');

		foreach ($files as $file) {
			if ($file != '.' && $file != '..') {
				$newFileName = 'supplemental--'.pathinfo($file)['filename'];
				copy($rootPath.'supplemental'.DIRECTORY_SEPARATOR.$file, $this->cldrCacheFolder.DIRECTORY_SEPARATOR.$newFileName);
			}
		}
	}

	private function generateMainDatas(){
		$rootPath = $this->cldrCacheFolder.DIRECTORY_SEPARATOR.'tmp'.DIRECTORY_SEPARATOR;
		$files = @scandir($rootPath.'main');

		foreach ($files as $file) {
			if ($file != '.' && $file != '..' && !is_dir($file)) {

				$sections = @scandir($rootPath.'main'.DIRECTORY_SEPARATOR.$file);
				foreach ($sections as $section) {
					if ($section != '.' && $section != '..') {
						$newFileName = 'main--'.$file.'--'.pathinfo($section)['filename'];

						copy($rootPath.'main'.DIRECTORY_SEPARATOR.$file.DIRECTORY_SEPARATOR.$section, $this->cldrCacheFolder.DIRECTORY_SEPARATOR.$newFileName);
					}
				}
			}
		}
	}
}
