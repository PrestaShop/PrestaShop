<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Backup;

use PrestaShop\PrestaShop\Adapter\Entity\PrestaShopBackup;
use PrestaShop\PrestaShop\Core\Backup\BackupInterface;
use PrestaShop\PrestaShop\Core\Backup\Manager\BackupRemoverInterface;

/**
 * Class BackupRemover deletes given backup.
 *
 * @internal
 */
final class BackupRemover implements BackupRemoverInterface
{
    /**
     * {@inheritdoc}
     */
    public function remove(BackupInterface $backup): bool
    {
        $legacyBackup = new PrestaShopBackup($backup->getFileName());

        return $legacyBackup->delete();
    }
}
