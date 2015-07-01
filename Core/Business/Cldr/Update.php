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

namespace PrestaShop\PrestaShop\Core\Business\Cldr;

use PrestaShop\PrestaShop\Core\Foundation\Net\Curl;

class Update extends Repository
{
    const ZIP_CORE_URL = 'http://www.unicode.org/Public/cldr/26/json-full.zip';

    public function __construct($psCacheDir)
    {
        $this->cldrCacheFolder = $psCacheDir.'cldr';

        if (!is_dir($this->cldrCacheFolder)) {
            try {
                mkdir($this->cldrCacheFolder.DIRECTORY_SEPARATOR.'tmp', 0777, true);
            } catch (\Exception $e) {
                throw new \Exception('Cldr cache folder can\'t be created');
            }
        }
    }

    /**
     * fetch all CLDR translations datas
     */
    public function fetch()
    {
        if (!is_file($file = $this->cldrCacheFolder.DIRECTORY_SEPARATOR.'core.zip')) {
            $fp = fopen($file, "w");
            $curl = new Curl();
            $curl->setOptions(array(
                CURLOPT_FILE => $fp,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HEADER => false
            ));

            if ($curl->exec(self::ZIP_CORE_URL) === false) {
                throw new \Exception("Failed to download from '".
                    self::ZIP_CORE_URL."'.");
            };

            $curl->close();
            fclose($fp);

            $archive = new \ZipArchive();
            if ($archive->open($file) === true) {
                $archive->extractTo($this->cldrCacheFolder.DIRECTORY_SEPARATOR.'tmp');
                $archive->close();
            } else {
                throw new \Exception("Failed to unzip '".$file."'.");
            }

            $this->generateSupplementalDatas();
            $this->generateMainDatas();
            $this->rmdir($this->cldrCacheFolder.DIRECTORY_SEPARATOR.'tmp');
        }
    }

    /**
     * generate CLDR translations supplemental datas
     */
    private function generateSupplementalDatas()
    {
        $rootPath = $this->cldrCacheFolder.DIRECTORY_SEPARATOR.'tmp'.DIRECTORY_SEPARATOR;
        $files = @scandir($rootPath.'supplemental');

        foreach ($files as $file) {
            if ($file != '.' && $file != '..') {
                $newFileName = 'supplemental--'.pathinfo($file)['filename'];
                copy(
                    $rootPath.'supplemental'.DIRECTORY_SEPARATOR.$file,
                    $this->cldrCacheFolder.DIRECTORY_SEPARATOR.$newFileName
                );
            }
        }
    }

    /**
     * generate CLDR translations main datas
     */
    private function generateMainDatas()
    {
        $rootPath = $this->cldrCacheFolder.DIRECTORY_SEPARATOR.'tmp'.DIRECTORY_SEPARATOR;
        $files = @scandir($rootPath.'main');

        foreach ($files as $file) {
            if ($file != '.' && $file != '..' && !is_dir($file)) {
                $sections = @scandir($rootPath.'main'.DIRECTORY_SEPARATOR.$file);
                foreach ($sections as $section) {
                    if ($section != '.' && $section != '..') {
                        $newFileName = 'main--'.$file.'--'.pathinfo($section)['filename'];

                        copy(
                            $rootPath.'main'.DIRECTORY_SEPARATOR.$file.DIRECTORY_SEPARATOR.$section,
                            $this->cldrCacheFolder.DIRECTORY_SEPARATOR.$newFileName
                        );
                    }
                }
            }
        }
    }

    /*Recursive rmdir */
    private function rmdir($dir)
    {
        if (is_dir($dir)) {
            $objects = scandir($dir);
            foreach ($objects as $object) {
                if ($object != "." && $object != "..") {
                    if (filetype($dir."/".$object) == "dir") {
                        $this->rmdir($dir."/".$object);
                    } else {
                        unlink($dir."/".$object);
                    }
                }
            }
            reset($objects);
            rmdir($dir);
        }
    }
}
