<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

/**
 * In charge of unzipping the latest Prestashop Version.
 *
 * Most methods are copied from https://github.com/PrestaShop/autoupgrade/blob/master/classes/TaskRunner/Upgrade/Unzip.php
 */
class Unzip
{
    /**
     * @param string $zipFilepath
     * @param string $pathWhereToExtract
     *
     * @return bool
     *
     * @throws PrestashopCouldNotUnzipLatestVersionException
     */
    public function unzipArchive($zipFilepath, $pathWhereToExtract)
    {
        if ((is_dir($pathWhereToExtract) || is_file($pathWhereToExtract))) {
            throw new PrestashopCouldNotUnzipLatestVersionException(sprintf(
                'Destination folder %s already exists',
                $pathWhereToExtract
            ));
        }

        $this->extract($zipFilepath, $pathWhereToExtract);

        return @unlink($zipFilepath);
    }

    /**
     * @param string $fromFile
     * @param string $toDir
     *
     * @return bool
     *
     * @throws PrestashopCouldNotUnzipLatestVersionException
     */
    private function extract($fromFile, $toDir)
    {
        if (false === is_file($fromFile)) {
            throw new PrestashopCouldNotUnzipLatestVersionException('Given zip file is not a file');
        }

        if (false === file_exists($toDir)) {
            if (false === mkdir($toDir)) {
                throw new PrestashopCouldNotUnzipLatestVersionException('Unzip destination folder cannot be used');
            }
            chmod($toDir, 0775);
        }

        $this->extractWithZipArchive($fromFile, $toDir);

        return true;
    }

    /**
     * @param string $fromFile
     * @param string $toDir
     *
     * @return bool
     *
     * @throws PrestashopCouldNotUnzipLatestVersionException
     */
    private function extractWithZipArchive($fromFile, $toDir)
    {
        $zip = $this->openWithZipArchive($fromFile);

        if (false === $zip->extractTo($toDir)) {
            throw new PrestashopCouldNotUnzipLatestVersionException(sprintf(
                'zip->extractTo(): unable to use %s as extract destination.',
                $toDir
            ));
        }

        return $zip->close();
    }

    /**
     * @param string $zipFile
     *
     * @return ZipArchive
     *
     * @throws PrestashopCouldNotUnzipLatestVersionException
     */
    private function openWithZipArchive($zipFile)
    {
        $zip = new ZipArchive();

        if ($zip->open($zipFile) !== true || empty($zip->filename)) {
            throw new PrestashopCouldNotUnzipLatestVersionException('Failed to open zip archive');
        }

        return $zip;
    }
}
