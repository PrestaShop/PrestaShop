<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Backup;

use DateTimeImmutable;
use PrestaShop\PrestaShop\Adapter\Entity\PrestaShopBackup;
use PrestaShop\PrestaShop\Core\Backup\BackupInterface;

/**
 * Class Backup represents single database backup.
 *
 * @internal
 */
final class Backup implements BackupInterface
{
    /**
     * @var PrestaShopBackup
     */
    private $legacyBackup;

    /**
     * @var string
     */
    private $fileName;

    /**
     * @param string $fileName Backup file name
     */
    public function __construct($fileName)
    {
        $this->fileName = $fileName;
        $this->legacyBackup = new PrestaShopBackup($fileName);
    }

    /**
     * {@inheritdoc}
     */
    public function getFileName()
    {
        return $this->fileName;
    }

    /**
     * {@inheritdoc}
     */
    public function getFilePath()
    {
        return $this->legacyBackup->getBackupPath() . $this->getFileName();
    }

    /**
     * {@inheritdoc}
     */
    public function getUrl()
    {
        return $this->legacyBackup->getBackupURL();
    }

    /**
     * {@inheritdoc}
     */
    public function getSize()
    {
        return filesize($this->legacyBackup->id);
    }

    /**
     * {@inheritdoc}
     */
    public function getAge()
    {
        return time() - $this->getDate()->getTimestamp();
    }

    /**
     * {@inheritdoc}
     */
    public function getDate()
    {
        [$timestamp] = explode('-', $this->fileName);

        return new DateTimeImmutable('@' . $timestamp);
    }
}
