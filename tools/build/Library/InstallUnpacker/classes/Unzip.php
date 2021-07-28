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
