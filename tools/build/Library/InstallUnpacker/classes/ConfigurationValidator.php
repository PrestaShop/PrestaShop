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

class ConfigurationValidator
{
    /**
     * Check whether download, unzip & replace of latest PS version can be carried out.
     *
     * @return string[] array of issues. Empty if download can be performed.
     */
    public function testSystemCanPerformDownloadUnzipAndReplace()
    {
        $errors = [];

        if (!$this->testCurl() && !$this->testFopen()) {
            $errors[] = 'You need allow_url_fopen or cURL enabled for automatic download to work.';
        }

        $systemErrors = $this->testFilesystemCapabilities();
        if (false === empty($systemErrors)) {
            $errors[] = sprintf('Cannot manipulate file on this system: %s', implode('; ', $systemErrors));
        }

        if (false === class_exists('ZipArchive')) {
            $errors[] = 'Cannot unzip files as php class ZipArchive is not available.';
        }

        // @todo: be able to use fallback util directories
        $downloadDirPath = __DIR__.DIRECTORY_SEPARATOR.'download';
        if (is_dir($downloadDirPath) || file_exists($downloadDirPath)) {
            $errors[] = "Directory 'download' already exists.";
        }
        $downloadDirPath = __DIR__.DIRECTORY_SEPARATOR.'extracted';
        if (is_dir($downloadDirPath) || file_exists($downloadDirPath)) {
            $errors[] = "Directory 'extracted' already exists.";
        }

        return $errors;
    }

    /**
     * @return bool
     */
    public function testFopen()
    {
        return in_array(ini_get('allow_url_fopen'), array('On', 'on', '1'));
    }

    /**
     * @return bool
     */
    public function testCurl()
    {
        return extension_loaded('curl');
    }

    /**
     * Test whether files and directories can be manipulated by php on given system
     * by performing multiple file operations
     *
     * @return string[]
     */
    public function testFilesystemCapabilities()
    {
        // choose a random available directory name
        $dirPath = $this->getRandomDirectoryPath();

        $randomNameIsActuallyAlreadyUsed = (is_dir($dirPath) || file_exists($dirPath));
        if ($randomNameIsActuallyAlreadyUsed) {
            // retry another random directory name
            $dirPath = $this->getRandomDirectoryPath();

            $randomNameIsActuallyAlreadyUsed = (is_dir($dirPath) || file_exists($dirPath));
            if ($randomNameIsActuallyAlreadyUsed) {
                throw new \RuntimeException('Failed to find available directory name');
            }
        }

        // create directory
        $createDirectoryResult = @mkdir($dirPath);
        if (false === $createDirectoryResult) {
            return ['Cannot create directories'];
        }

        // create file in it
        $fileCreationTestPath = $dirPath . DIRECTORY_SEPARATOR . 'test-file.php';
        $createFileResult = @file_put_contents($fileCreationTestPath, "<?php echo 'Hello world !';");
        if (false === $createFileResult) {
            return ['Cannot write files'];
        }

        // download a file from github in directory
        $downloadTestPath = $dirPath . DIRECTORY_SEPARATOR . 'test-download.txt';
        // @todo: use another file from the network ?
        $target = 'https://raw.githubusercontent.com/PrestaShop/PrestaShop/develop/robots.txt';
        $downloadFileResult = @file_put_contents($downloadTestPath, Download::fileGetContents($target));
        if (false === $downloadFileResult) {
            return ['Cannot download files from network'];
        }

        // move a file from test directory into root directory
        $fileMoveTestPath = __DIR__ . DIRECTORY_SEPARATOR . 'test-move.php';
        $moveResult = @rename($fileCreationTestPath, $fileMoveTestPath);
        if (false === $moveResult) {
            return ['Cannot move files into prestashop root directory'];
        }

        // delete test file in root directory
        $deleteFileResult = unlink($fileMoveTestPath);
        if (false === $deleteFileResult) {
            return ['Cannot delete files in prestashop root directory'];
        }

        // delete test directory
        $deleteDirectoryContentResult = array_map('unlink', glob($dirPath . DIRECTORY_SEPARATOR . "*.*"));
        $deleteDirectoryResult = @rmdir($dirPath);
        if ((false === $deleteDirectoryContentResult) || (false === $deleteDirectoryResult)) {
            return ['Cannot delete directories in prestashop root directory'];
        }
    }

    /**
     * @return string
     */
    private function getRandomDirectoryPath()
    {
        $randomDirectoryName = 'test-' . uniqid();
        $dirPath = __DIR__ . DIRECTORY_SEPARATOR . $randomDirectoryName;

        return $dirPath;
    }
}
