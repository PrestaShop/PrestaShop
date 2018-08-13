<?php
/**
 * 2007-2018 PrestaShop
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
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Adapter\Backup;

use DateTimeImmutable;
use PrestaShop\PrestaShop\Adapter\Entity\PrestaShopBackup;
use PrestaShop\PrestaShop\Core\Backup\BackupInterface;

/**
 * Class Backup
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
        list($timestamp) = explode('-', $this->fileName);

        return new DateTimeImmutable('@'.$timestamp, new \DateTimeZone('Europe/London'));
    }
}
