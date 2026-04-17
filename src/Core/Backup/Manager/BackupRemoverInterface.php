<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Backup\Manager;

use PrestaShop\PrestaShop\Core\Backup\BackupInterface;

/**
 * Interface BackupRemoverInterface defines contract for backup remover.
 */
interface BackupRemoverInterface
{
    /**
     * @param BackupInterface $backup
     *
     * @return bool True if backup were removed successfully or False otherwise
     */
    public function remove(BackupInterface $backup): bool;
}
