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

use PrestaShop\PrestaShop\Core\Business\Cldr\Localize;
use PrestaShop\PrestaShop\Core\Foundation\Net\Curl;

class Update extends Repository
{
    const ZIP_CORE_URL = 'http://www.unicode.org/Public/cldr/26/json-full.zip';

    protected $locale;

    public function __construct($psCacheDir)
    {
        $this->cldrCacheFolder = $psCacheDir.'cldr';
        $this->locale = null;

        if (!is_dir($this->cldrCacheFolder)) {
            try {
                mkdir($this->cldrCacheFolder.DIRECTORY_SEPARATOR.'datas', 0777, true);
            } catch (\Exception $e) {
                throw new \Exception('Cldr cache folder can\'t be created');
            }
        }
    }

    /*
     * set locale
     *
     * @param string $locale
     *
     */
    public function setLocale($locale)
    {
        $localize = new Localize();
        $localize::setLocale($locale);

        $this->locale = str_replace('_', '-', $localize::getLocale());
    }

    /*
     * Init CLDR datas and download default language
     */
    public function init()
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
                throw new \Exception("Failed to download from '" .
                    self::ZIP_CORE_URL . "'.");
            };

            $curl->close();
            fclose($fp);
        }

        //extract ONLY supplemental json files
        $archive = new \ZipArchive();

        if ($archive->open($file) === true) {
            for ($i = 0; $i < $archive->numFiles; $i++) {
                $filename = $archive->getNameIndex($i);
                if (preg_match('%^supplemental\/(.*).json$%', $filename)) {
                    if (!is_dir($this->cldrCacheFolder.DIRECTORY_SEPARATOR.'datas'.DIRECTORY_SEPARATOR.dirname($filename))) {
                        mkdir($this->cldrCacheFolder.DIRECTORY_SEPARATOR.'datas'.DIRECTORY_SEPARATOR.dirname($filename), 0777, true);
                    }

                    if (!file_exists($this->cldrCacheFolder.DIRECTORY_SEPARATOR.'datas'.DIRECTORY_SEPARATOR.$filename)) {
                        copy("zip://" . $file . "#" . $filename, $this->cldrCacheFolder . DIRECTORY_SEPARATOR . 'datas' . DIRECTORY_SEPARATOR . $filename);
                    }
                }
            }
            $archive->close();
        } else {
            throw new \Exception("Failed to unzip '".$file."'.");
        }

        $this->generateSupplementalDatas();
        $this->fetchLocale();
    }


    /*
     * fetch CLDR datas for a locale
     *
     * @param optional string $locale
     */
    public function fetchLocale($locale = null)
    {
        $file = $this->cldrCacheFolder.DIRECTORY_SEPARATOR.'core.zip';
        $locale = $locale ? $locale : $this->locale;

        $archive = new \ZipArchive();
        $archive->open($file);

        for ($i = 0; $i < $archive->numFiles; $i++) {
            $filename = $archive->getNameIndex($i);
            if (preg_match('%^main\/'.$locale.'\/(.*).json$%', $filename)) {
                if (!is_dir($this->cldrCacheFolder.DIRECTORY_SEPARATOR.'datas'.DIRECTORY_SEPARATOR.dirname($filename))) {
                    mkdir($this->cldrCacheFolder.DIRECTORY_SEPARATOR.'datas'.DIRECTORY_SEPARATOR.dirname($filename), 0777, true);
                }

                if (!file_exists($this->cldrCacheFolder.DIRECTORY_SEPARATOR.'datas'.DIRECTORY_SEPARATOR.$filename)) {
                    copy("zip://" . $file . "#" . $filename, $this->cldrCacheFolder . DIRECTORY_SEPARATOR . 'datas' . DIRECTORY_SEPARATOR . $filename);
                }
            }
        }
        $archive->close();

        $this->generateMainDatas($locale);
    }

    /**
     * generate CLDR supplemental datas
     */
    private function generateSupplementalDatas()
    {
        $rootPath = $this->cldrCacheFolder.DIRECTORY_SEPARATOR.'datas'.DIRECTORY_SEPARATOR;
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
     *
     * @param string $locale
     */
    private function generateMainDatas($locale)
    {
        $rootPath = $this->cldrCacheFolder.DIRECTORY_SEPARATOR.'datas'.DIRECTORY_SEPARATOR;
        $files = @scandir($rootPath.'main'.DIRECTORY_SEPARATOR.$locale);

        foreach ($files as $file) {
            if ($file != '.' && $file != '..') {
                $newFileName = 'main--'.$locale.'--'.pathinfo($file)['filename'];
                copy(
                    $rootPath.'main'.DIRECTORY_SEPARATOR.$locale.DIRECTORY_SEPARATOR.$file,
                    $this->cldrCacheFolder.DIRECTORY_SEPARATOR.$newFileName
                );
            }
        }
    }
}
