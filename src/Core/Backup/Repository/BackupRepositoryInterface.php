<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Backup\Repository;

use PrestaShop\PrestaShop\Core\Backup\BackupCollectionInterface;

/**
 * Interface BackupRepositoryInterface defines contract for backup repository.
 */
interface BackupRepositoryInterface
{
    /**
     * Get available backups.
     *
     * @return BackupCollectionInterface
     */
    public function retrieveBackups();
}
