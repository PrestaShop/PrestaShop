<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
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
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

declare(strict_types=1);

namespace Tests\Resources;

use Symfony\Component\Filesystem\Filesystem;

/**
 * Backups and reverts testable resources to its initial state.
 */
class ResourceResetter
{
    /**
     * Name for directory of test images in system tmp dir
     */
    public const BACKUP_TEST_IMG_DIR = 'ps_backup_test_img';
    public const BACKUP_TEST_DOWNLOADS_DIR = 'ps_backup_test_download';

    /**
     * @var Filesystem|null
     */
    private $filesystem;

    /**
     * @var string|null
     */
    private $backupRootDir;

    /**
     * @param Filesystem|null $filesystem
     * @param string|null $backupRootDir
     */
    public function __construct(
        ?Filesystem $filesystem = null,
        ?string $backupRootDir = null
    ) {
        $this->filesystem = $filesystem ?: new Filesystem();
        $this->backupRootDir = $backupRootDir ?: sys_get_temp_dir();
    }

    /**
     * Backs up test images directory to allow resetting their original state later in tests
     */
    public function backupImages(): void
    {
        $this->filesystem->mirror(_PS_IMG_DIR_, $this->getBackupTestImgDir());
    }

    /**
     * Backs up test downloads directory to allow resetting their original state later in tests
     */
    public function backupDownloads(): void
    {
        $this->filesystem->mirror(_PS_DOWNLOAD_DIR_, $this->getBackupTestDownloadsDir());
    }

    /**
     * Resets test images directory to initial state
     */
    public function resetImages(): void
    {
        $this->filesystem->remove(_PS_IMG_DIR_);
        $this->filesystem->mirror($this->getBackupTestImgDir(), _PS_IMG_DIR_);
    }

    /**
     * Resets test images directory to initial state
     */
    public function resetDownloads(): void
    {
        $this->filesystem->remove(_PS_DOWNLOAD_DIR_);
        $this->filesystem->mirror($this->getBackupTestDownloadsDir(), _PS_DOWNLOAD_DIR_);
    }

    /**
     * Provide test img directory path, in which initial dummy images state should be saved
     *
     * @return string
     */
    public function getBackupTestImgDir(): string
    {
        return $this->backupRootDir . DIRECTORY_SEPARATOR . self::BACKUP_TEST_IMG_DIR;
    }

    /**
     * Provide test downloads directory path, in which initial downloads state should be saved
     *
     * @return string
     */
    public function getBackupTestDownloadsDir(): string
    {
        return $this->backupRootDir . DIRECTORY_SEPARATOR . self::BACKUP_TEST_DOWNLOADS_DIR;
    }
}
