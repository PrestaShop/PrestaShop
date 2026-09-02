<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Backup;

/**
 * Interface BackupCollectionInterface defines contract for backup collection.
 */
interface BackupCollectionInterface
{
    /**
     * Add backup to collection.
     *
     * @param BackupInterface $backup
     *
     * @return self
     */
    public function add(BackupInterface $backup);

    /**
     * Get all backups.
     *
     * @return BackupInterface[]
     */
    public function all();
}
