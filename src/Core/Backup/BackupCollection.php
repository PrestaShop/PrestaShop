<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Backup;

final class BackupCollection implements BackupCollectionInterface
{
    /**
     * @var BackupInterface[]
     */
    private $backups = [];

    /**
     * {@inheritdoc}
     */
    public function add(BackupInterface $backup)
    {
        $this->backups[] = $backup;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function all()
    {
        return $this->backups;
    }
}
