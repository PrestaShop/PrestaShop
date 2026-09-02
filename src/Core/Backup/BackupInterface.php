<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Backup;

use DateTimeInterface;

/**
 * Interface BackupInterface defines contract for backup.
 */
interface BackupInterface
{
    /**
     * Get backup filename.
     *
     * @return string
     */
    public function getFileName();

    /**
     * Get complete path to the backup file.
     *
     * @return string
     */
    public function getFilePath();

    /**
     * Get URL to backup.
     *
     * @return string
     */
    public function getUrl();

    /**
     * Get backup file size in bytes.
     *
     * @return int
     */
    public function getSize();

    /**
     * Get backup age in seconds.
     *
     * @return int
     */
    public function getAge();

    /**
     * Get backup creation date.
     *
     * @return DateTimeInterface
     */
    public function getDate();
}
