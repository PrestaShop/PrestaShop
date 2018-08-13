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
namespace PrestaShop\PrestaShop\Core\Cldr;

use Tools as ToolsLegacy;
use Curl\Curl;
use ZipArchive;

/**
 * Class Update will download CLDR data and extract/install them into the cache directory.
 *
 * @package PrestaShop\PrestaShop\Core\Cldr
 */
class Update extends Repository
{
    const ZIP_CORE_URL = 'http://i18n.prestashop.com/cldr/json-full.zip';

    protected $newDatasFile = [];
    protected $oldUmask;

    /**
     * Constructor.
     *
     * @param string $psCacheDir The cache directory for CLDR downloads.
     */
    public function __construct($psCacheDir)
    {
        $this->oldUmask = umask(0000);
        $this->cldrCacheFolder = $psCacheDir.'cldr';

        if (!is_dir($this->cldrCacheFolder)) {
            try {
                mkdir($this->cldrCacheFolder.DIRECTORY_SEPARATOR.'datas', 0777, true);
            } catch (\Exception $e) {
                throw new \Exception('Cldr cache folder can\'t be created');
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function __destruct()
    {
        umask($this->oldUmask);
    }

    /**
     * Init CLDR data and download default language
     */
    public function init()
    {
        if (!is_file($file = $this->cldrCacheFolder.DIRECTORY_SEPARATOR.'core.zip')) {
            $fp = fopen($file, "w");

            $curl = new Curl();
            $curl->setopt(CURLOPT_FILE, $fp);
            $curl->setopt(CURLOPT_FOLLOWLOCATION, true);
            $curl->setopt(CURLOPT_HEADER, false);
            $curl->get(self::ZIP_CORE_URL);

            if ($curl->error) {
                throw new \Exception("Failed to download '" .
                    self::ZIP_CORE_URL . "'.");
            };

            fclose($fp);
        }

        //extract ONLY supplemental json files
        $archive = new ZipArchive();
        if ($archive->open($file) === true) {
            for ($i = 0; $i < $archive->numFiles; $i++) {
                $filename = $archive->getNameIndex($i);
                if (preg_match('%^supplemental\/(.*).json$%', $filename)) {
                    if (!is_dir($this->cldrCacheFolder.DIRECTORY_SEPARATOR.'datas'.DIRECTORY_SEPARATOR.dirname($filename))) {
                        mkdir($this->cldrCacheFolder.DIRECTORY_SEPARATOR.'datas'.DIRECTORY_SEPARATOR.dirname($filename), 0777, true);
                    }

                    if (!file_exists($this->cldrCacheFolder.DIRECTORY_SEPARATOR.'datas'.DIRECTORY_SEPARATOR.$filename)) {
                        copy("zip://" . $file . "#" . $filename, $this->cldrCacheFolder . DIRECTORY_SEPARATOR . 'datas' . DIRECTORY_SEPARATOR . $filename);
                        $this->newDatasFile[] = $this->cldrCacheFolder . DIRECTORY_SEPARATOR . 'datas' . DIRECTORY_SEPARATOR . $filename;
                    }
                }
            }
            $archive->close();
        } else {
            throw new \Exception("Failed to unzip '".$file."'.");
        }

        $this->generateSupplementalDatas();
    }


    /**
     * Fetch CLDR data for a locale
     *
     * @param string $locale
     */
    public function fetchLocale($locale)
    {
        if (!$locale) {
            throw new \Exception('Error : the locale is not valid');
        }

        $cldrRepository = ToolsLegacy::getCldr(null, $locale);
        $locale = $cldrRepository->getCulture();

        $file = $this->cldrCacheFolder.DIRECTORY_SEPARATOR.'core.zip';

        $archive = new ZipArchive();
        $archive->open($file);

        for ($i = 0; $i < $archive->numFiles; $i++) {
            $filename = $archive->getNameIndex($i);

            if (preg_match('%^main\/'.$locale.'\/(.*).json$%', $filename)) {
                if (!is_dir($this->cldrCacheFolder.DIRECTORY_SEPARATOR.'datas'.DIRECTORY_SEPARATOR.dirname($filename))) {
                    mkdir($this->cldrCacheFolder.DIRECTORY_SEPARATOR.'datas'.DIRECTORY_SEPARATOR.dirname($filename), 0777, true);
                }

                if (!file_exists($this->cldrCacheFolder.DIRECTORY_SEPARATOR.'datas'.DIRECTORY_SEPARATOR.$filename)) {
                    copy("zip://" . $file . "#" . $filename, $this->cldrCacheFolder . DIRECTORY_SEPARATOR . 'datas' . DIRECTORY_SEPARATOR . $filename);
                    $this->newDatasFile[] = $this->cldrCacheFolder . DIRECTORY_SEPARATOR . 'datas' . DIRECTORY_SEPARATOR . $filename;
                }
            }
        }
        $archive->close();

        $this->generateMainDatas($locale);
    }

    /**
     * Generate CLDR supplemental data
     */
    private function generateSupplementalDatas()
    {
        $rootPath = $this->cldrCacheFolder.DIRECTORY_SEPARATOR.'datas'.DIRECTORY_SEPARATOR;
        $files = @scandir($rootPath . 'supplemental', SCANDIR_SORT_NONE);

        foreach ($files as $file) {
            if (is_file($file)) {
                $newFileName = 'supplemental--'.pathinfo($file)['filename'];
                if (!file_exists($this->cldrCacheFolder.DIRECTORY_SEPARATOR.$newFileName)) {
                    copy(
                        $rootPath . 'supplemental' . DIRECTORY_SEPARATOR . $file,
                        $this->cldrCacheFolder . DIRECTORY_SEPARATOR . $newFileName
                    );
                }
            }
        }
    }

    /**
     * Generate CLDR translations main data
     *
     * @param string $locale
     */
    private function generateMainDatas($locale)
    {
        $rootPath = $this->cldrCacheFolder.DIRECTORY_SEPARATOR.'datas'.DIRECTORY_SEPARATOR;
        $files = @scandir($rootPath . 'main' . DIRECTORY_SEPARATOR . $locale, SCANDIR_SORT_NONE);

        if (!$files) {
            return;
        }
        foreach ($files as $file) {
            if (is_file($file)) {
                $newFileName = 'main--'.$locale.'--'.pathinfo($file)['filename'];
                if (!file_exists($this->cldrCacheFolder . DIRECTORY_SEPARATOR . $newFileName)) {
                    copy(
                        $rootPath . 'main' . DIRECTORY_SEPARATOR . $locale . DIRECTORY_SEPARATOR . $file,
                        $this->cldrCacheFolder . DIRECTORY_SEPARATOR . $newFileName
                    );
                }
            }
        }
    }
}
