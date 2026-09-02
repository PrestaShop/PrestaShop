<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Backup;

use PrestaShop\PrestaShop\Adapter\Entity\PrestaShopBackup;
use PrestaShop\PrestaShop\Core\Backup\BackupInterface;
use PrestaShop\PrestaShop\Core\Backup\Exception\BackupException;
use PrestaShop\PrestaShop\Core\Backup\Exception\DirectoryIsNotWritableException;
use PrestaShop\PrestaShop\Core\Backup\Manager\BackupCreatorInterface;

/**
 * Class DatabaseBackupCreator is responsible for creating database backups.
 *
 * @internal
 */
final class DatabaseBackupCreator implements BackupCreatorInterface
{
    /**
     * {@inheritdoc}
     */
    public function createBackup(): BackupInterface
    {
        ini_set('max_execution_time', '0');

        if (!is_writable(PrestaShopBackup::getBackupPath())) {
            throw new DirectoryIsNotWritableException('To create backup, its directory must be writable');
        }

        $legacyBackup = new PrestaShopBackup();
        if (!$legacyBackup->add()) {
            throw new BackupException('Failed to create backup');
        }

        $backupFilePathParts = explode(DIRECTORY_SEPARATOR, $legacyBackup->id);

        return new Backup(end($backupFilePathParts));
    }
}
