<?php
/*
 * 2007-2016 PrestaShop
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
 *  @copyright  2007-2016 PrestaShop SA
 *  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */

class PSDezipper
{
    private $storage_path;
    private $git_repo;
    private $orga;
    private $github;

    public function __construct($github)
    {
        $this->github = $github;
        $this->storage_path = __DIR__ . "/app/release/dezipper/";
        $this->git_repo = 'InstallUnpacker';
        $this->orga = 'PrestaShop';
    }

    public function getStoragePath()
    {
        return $this->storage_path;
    }

    public function isReady($file)
    {
        return (bool)file_exists($this->storage_path.$file);
    }

    public function prepare()
    {
        $message = '';
        $zip_file = $this->storage_path.'repo_archive.zip';
        try {
            $archive = $this->github->client->api('repo')->contents()->archive($this->orga, $this->git_repo, 'zipball', 'master');
            if (empty($archive)) {
                throw new \Exception('Empty archive');
            }
            // Just in case the folder does not exist
            if (!is_dir($this->storage_path)) {
                mkdir($this->storage_path, 0777, true);
            }
            $wrote = file_put_contents($zip_file, $archive);

            if ($wrote === false) {
                throw new \Exception('Could not write on disk after download.');
            }

            $zip = new ZipArchive();
            $zip->open($zip_file);
            $zip->extractTo($this->storage_path);
            $zip->close();
        } catch (\Exception $ex) {
            $message = 'Cannot get archive: '. $ex->getMessage();
        }

        return $message;
    }

    public function compile()
    {
        $files = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($this->storage_path, FilesystemIterator::CURRENT_AS_SELF | FilesystemIterator::SKIP_DOTS),
                RecursiveIteratorIterator::LEAVES_ONLY
        );


        foreach ($files as $name => $file) {
            if (strpos($name, 'compile.php') !== false) {
                // Generate
                exec('cd '.$this->storage_path.' && php '.$name);
                // Cleaning
                exec('rm -r '. $this->storage_path.'PrestaShop-InstallUnpacker*');
                exec('rm '. $this->storage_path.'repo_archive.zip');
                return true;
            }
        }

        return false;
    }
}