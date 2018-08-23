<?php
/**
 * 2007-2018 PrestaShop.
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
class PrestashopCouldNotInstallLatestVersionException extends \Exception
{
}

/**
 * Gateway, manager and DIC for install util classes: download, unzip ...
 */
class InstallManager
{
    /**
     * @var ConfigurationValidator
     */
    private $validator;

    /**
     * @var Download
     */
    private $download;

    /**
     * @var Unzip
     */
    private $unzip;

    /**
     * @var string
     */
    private $downloadDirectoryPath;

    /**
     * @var string
     */
    private $extractDirectoryPath;

    public function __construct()
    {
        $this->validator = new ConfigurationValidator();
        $this->download = new Download();
        $this->unzip = new Unzip();

        // @todo: be able to use fallback util directories
        $this->downloadDirectoryPath = __DIR__ . DIRECTORY_SEPARATOR . 'download';
        $this->extractDirectoryPath = __DIR__ . DIRECTORY_SEPARATOR . 'extracted';
    }

    /**
     * Check whether download of latest PS version can be carried out.
     *
     * @return string[] array of issues. Empty if download can be performed.
     */
    public function testDownloadCapabilities()
    {
        return $this->validator->testSystemCanPerformDownloadUnzipAndReplace();
    }

    /**
     * @return string
     *
     * @throws \RuntimeException
     */
    public function getLatestStableAvailableVersion()
    {
        return $this->download->getLatestStableAvailableVersion();
    }

    /**
     * @return bool
     *
     * @throws PrestashopCouldNotInstallLatestVersionException
     */
    public function downloadUnzipAndReplaceLatestPSVersion()
    {
        if (is_dir($this->downloadDirectoryPath) || file_exists($this->downloadDirectoryPath)) {
            throw new PrestashopCouldNotInstallLatestVersionException(sprintf('Directory %s already exists.', $this->downloadDirectoryPath));
        }

        // create 'download' directory
        $createDirectoryResult = @mkdir($this->downloadDirectoryPath);
        if (false === $createDirectoryResult) {
            throw new PrestashopCouldNotInstallLatestVersionException(sprintf('Could not create directory %s', $this->downloadDirectoryPath));
        }

        // download zip archive
        $destinationPath = realpath($this->downloadDirectoryPath) . DIRECTORY_SEPARATOR . 'prestashop-latest.zip';
        $link = $this->download->getLatestStableAvailableVersionLink();
        Download::copy($link, $destinationPath);

        if (false === is_file($destinationPath)) {
            throw new PrestashopCouldNotInstallLatestVersionException('Failed to download latest Prestashop release zip archive');
        }

        // @todo: validate checksum ?

        // unzip archive into 'extracted' directory
        $this->unzip->unzipArchive($destinationPath, $this->extractDirectoryPath);

        // test 3 extracted files are OK
        $this->verifyUnzipFile('Install_PrestaShop.html');
        $this->verifyUnzipFile('prestashop.zip');
        $this->verifyUnzipFile('index.php');

        // replace files
        $this->replaceInstallFile('Install_PrestaShop.html');
        $this->replaceInstallFile('prestashop.zip');
        $this->replaceInstallFile('index.php');

        // delete 2 util directories
        $this->deleteDirectoryWithItsContent($this->downloadDirectoryPath);
        $this->deleteDirectoryWithItsContent($this->extractDirectoryPath);

        $this->download->clearFileCache();

        return true;
    }

    /**
     * @param string $fileName
     *
     * @throws PrestashopCouldNotInstallLatestVersionException
     */
    private function verifyUnzipFile($fileName)
    {
        if (false === is_file($this->extractDirectoryPath . DIRECTORY_SEPARATOR . $fileName)) {
            throw new PrestashopCouldNotInstallLatestVersionException(sprintf('After unzip, missing %s file', $fileName));
        }
    }

    /**
     * @param string $fileName
     *
     * @throws PrestashopCouldNotInstallLatestVersionException
     */
    private function replaceInstallFile($fileName)
    {
        $replaceFileResult = rename(
            $this->extractDirectoryPath . DIRECTORY_SEPARATOR . $fileName,
            __DIR__ . DIRECTORY_SEPARATOR . $fileName
        );

        if (false === $replaceFileResult) {
            throw new PrestashopCouldNotInstallLatestVersionException(sprintf('Could not replace %s file', $fileName));
        }
    }

    /**
     * @param string $directoryPath
     *
     * @throws PrestashopCouldNotInstallLatestVersionException
     */
    private function deleteDirectoryWithItsContent($directoryPath)
    {
        $deleteDirectoryContentResult = array_map('unlink', glob($directoryPath . DIRECTORY_SEPARATOR . '*.*'));

        $deleteDirectoryResult = @rmdir($directoryPath);

        if ((false === $deleteDirectoryContentResult) || (false === $deleteDirectoryResult)) {
            throw new PrestashopCouldNotInstallLatestVersionException(sprintf('Cannot delete directory %s', $directoryPath));
        }
    }
}
