<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Backup\Manager;

use PrestaShop\PrestaShop\Core\Backup\BackupInterface;

/**
 * Interface BackupCreatorInterface defines contract for backup creator.
 */
interface BackupCreatorInterface
{
    /**
     * Create new backup.
     *
     * @return BackupInterface
     */
    public function createBackup(): BackupInterface;
}
