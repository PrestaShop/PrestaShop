<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
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
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
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
        $downloadDirPath = __DIR__ . DIRECTORY_SEPARATOR . 'download';
        if (is_dir($downloadDirPath) || file_exists($downloadDirPath)) {
            $errors[] = "Directory 'download' already exists.";
        }
        $downloadDirPath = __DIR__ . DIRECTORY_SEPARATOR . 'extracted';
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
     * by performing multiple file operations.
     *
     * @return string[]
     */
    public function testFilesystemCapabilities()
    {
        $dirPath = $this->getRandomDirectoryPath();

        $this->checkRandomNameIsNotAlreadyUsed($dirPath);

        if (false === $this->createDirectoryTest($dirPath)) {
            return ['Cannot create directories'];
        }

        list($fileCreationTestPath, $createFileResult) = $this->createFileTest($dirPath);
        if (false === $createFileResult) {
            $this->deleteDirectoryTest($dirPath);

            return ['Cannot write files'];
        }

        if (false === $this->downloadFileTest($dirPath)) {
            $this->deleteDirectoryTest($dirPath);

            return ['Cannot download files from network'];
        }

        list($fileMoveTestPath, $moveResult) = $this->moveFileTest($fileCreationTestPath);
        if (false === $moveResult) {
            $this->deleteDirectoryTest($dirPath);

            return ['Cannot move files into prestashop root directory'];
        }

        if (false === $this->deleteFileTest($fileMoveTestPath)) {
            $this->deleteDirectoryTest($dirPath);

            return ['Cannot delete files in prestashop root directory'];
        }

        list($deleteDirectoryContentResult, $deleteDirectoryResult) = $this->deleteDirectoryTest($dirPath);
        if ((false === $deleteDirectoryContentResult) || (false === $deleteDirectoryResult)) {
            return ['Cannot delete directories in prestashop root directory'];
        }

        return [];
    }

    /**
     * Choose a random available directory name.
     *
     * @return string
     */
    private function getRandomDirectoryPath()
    {
        $randomDirectoryName = 'test-' . uniqid();

        return __DIR__ . DIRECTORY_SEPARATOR . $randomDirectoryName;
    }

    /**
     * @param string $dirPath
     *
     * @return bool
     */
    private function createDirectoryTest($dirPath)
    {
        return @mkdir($dirPath);
    }

    /**
     * @param string $dirPath
     *
     * @return array
     */
    private function createFileTest($dirPath)
    {
        $fileCreationTestPath = $dirPath . DIRECTORY_SEPARATOR . 'test-file.php';
        $createFileResult = @file_put_contents($fileCreationTestPath, "<?php echo 'Hello world !';");

        return [$fileCreationTestPath, $createFileResult];
    }

    /**
     * @param string $dirPath
     *
     * @return bool
     */
    private function downloadFileTest($dirPath)
    {
        $downloadTestPath = $dirPath . DIRECTORY_SEPARATOR . 'test-download.txt';
        $target = 'https://www.google.com/robots.txt';

        return (bool) @file_put_contents($downloadTestPath, Download::fileGetContents($target));
    }

    /**
     * Move a file from test directory into root directory.
     *
     * @param string $fileCreationTestPath
     *
     * @return array
     */
    private function moveFileTest($fileCreationTestPath)
    {
        $fileMoveTestPath = __DIR__ . DIRECTORY_SEPARATOR . 'test-move.php';
        $moveResult = @rename($fileCreationTestPath, $fileMoveTestPath);

        return [$fileMoveTestPath, $moveResult];
    }

    /**
     * @param string $fileMoveTestPath
     *
     * @return bool
     */
    private function deleteFileTest($fileMoveTestPath)
    {
        return unlink($fileMoveTestPath);
    }

    /**
     * @param string $dirPath
     *
     * @return array
     */
    private function deleteDirectoryTest($dirPath)
    {
        $deleteDirectoryContentResult = array_map('unlink', glob($dirPath . DIRECTORY_SEPARATOR . '*.*'));
        $deleteDirectoryResult = @rmdir($dirPath);

        return [$deleteDirectoryContentResult, $deleteDirectoryResult];
    }

    /**
     * @param string $dirPath
     *
     * @return bool
     *
     * @throws \RuntimeException
     */
    private function checkRandomNameIsNotAlreadyUsed($dirPath)
    {
        if (is_dir($dirPath) || file_exists($dirPath)) {
            throw new \RuntimeException(sprintf('Test directory name is already used: %s', $dirPath));
        }

        return true;
    }
}
