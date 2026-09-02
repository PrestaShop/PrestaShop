<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Backup\Comparator;

use PrestaShop\PrestaShop\Core\Backup\BackupInterface;

/**
 * Interface BackupComparatorInterface defines contract for backups comparator.
 */
interface BackupComparatorInterface
{
    /**
     * Compare 2 backups.
     *
     * @param BackupInterface $backup1
     * @param BackupInterface $backup2
     *
     * @return int
     */
    public function compare(BackupInterface $backup1, BackupInterface $backup2);
}
