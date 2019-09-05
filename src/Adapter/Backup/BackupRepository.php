<?php
/**
 * 2007-2019 PrestaShop and Contributors
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
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
