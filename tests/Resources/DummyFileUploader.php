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

declare(strict_types=1);

namespace Tests\Resources;

use RuntimeException;

/**
 * Mimics user file uploads
 */
class DummyFileUploader
{
    /**
     * Prefix for temporary files that are used as a replacement for http upload
     */
    public const UPLOADED_TMP_FILE_PREFIX = 'ps_upload_test_';

    /**
     * Uploads dummy file to temporary dir to mimic http file upload
     *
     * @param string $dummyFilename
     *
     * @return string destination pathname
     */
    public static function upload(string $dummyFilename): string
    {
        $source = static::getDummyFilesPath() . $dummyFilename;

        if (!is_file($source)) {
            throw new RuntimeException('file "%s" not found', $source);
        }

        $destination = static::createTempFilename();
        copy($source, $destination);

        return $destination;
    }

    /**
     * @return string
     */
    public static function getDummyFilesPath(): string
    {
        return __DIR__ . '/dummyFile/';
    }

    /**
     * @return string
     */
    private static function createTempFilename(): string
    {
        return tempnam(sys_get_temp_dir(), static::UPLOADED_TMP_FILE_PREFIX);
    }
}
