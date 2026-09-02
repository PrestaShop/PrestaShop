<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Backup;

use PrestaShop\PrestaShop\Adapter\Entity\PrestaShopBackup;
use PrestaShop\PrestaShop\Core\Backup\BackupCollection;
use PrestaShop\PrestaShop\Core\Backup\Repository\BackupRepositoryInterface;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

/**
 * Class BackupRepository is responsible for providing available backups.
 *
 * @internal
 */
final class BackupRepository implements BackupRepositoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function retrieveBackups()
    {
        $backupFinder = (new Finder())
            ->files()
            ->in(PrestaShopBackup::getBackupPath())
            ->name('/^([_a-zA-Z0-9\-]*[\d]+-[a-z\d]+)\.sql(\.gz|\.bz2)?$/')
            ->depth(0);

        $backups = new BackupCollection();

        /** @var SplFileInfo $file */
        foreach ($backupFinder as $file) {
            $backups->add(new Backup($file->getFilename()));
        }

        return $backups;
    }
}
