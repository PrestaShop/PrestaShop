<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Backup\Comparator;

use PrestaShop\PrestaShop\Core\Backup\BackupInterface;

/**
 * Class BackupByDateComparator compares 2 backups by creation date.
 */
final class BackupByDateComparator implements BackupComparatorInterface
{
    /**
     * {@inheritdoc}
     */
    public function compare(BackupInterface $backup1, BackupInterface $backup2)
    {
        return $backup2->getDate()->getTimestamp() - $backup1->getDate()->getTimestamp();
    }
}
