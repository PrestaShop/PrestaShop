<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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

    public static function getDummyFilePath(string $dummyFilename): string
    {
        return static::getDummyFilesPath() . $dummyFilename;
    }

    /**
     * @return string
     */
    private static function createTempFilename(): string
    {
        return tempnam(sys_get_temp_dir(), static::UPLOADED_TMP_FILE_PREFIX);
    }
}
