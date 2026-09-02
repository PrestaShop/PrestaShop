<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
    public const BACKUP_TEST_MODULES_DIR = 'ps_backup_test_modules';

    public const TEST_MODULES_DIR = _PS_ROOT_DIR_ . '/tests/Resources/modules/';

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
        $this->filesystem->remove($this->getBackupTestImgDir());
        $this->filesystem->mirror(_PS_IMG_DIR_, $this->getBackupTestImgDir(), null, ['delete' => true]);
    }

    /**
     * Backs up test downloads directory to allow resetting their original state later in tests
     */
    public function backupDownloads(): void
    {
        $this->filesystem->remove($this->getBackupTestDownloadsDir());
        $this->filesystem->mirror(_PS_DOWNLOAD_DIR_, $this->getBackupTestDownloadsDir(), null, ['delete' => true]);
    }

    /**
     * Backs up test modules directory to allow resetting their original state later in tests
     */
    public function backupTestModules(): void
    {
        $this->filesystem->remove($this->getBackupTestModulesDir());
        $this->filesystem->mirror(static::TEST_MODULES_DIR, $this->getBackupTestModulesDir(), null, ['delete' => true]);
    }

    /**
     * Resets test images directory to initial state
     */
    public function resetImages(): void
    {
        $this->filesystem->remove(_PS_IMG_DIR_);
        $this->filesystem->mirror($this->getBackupTestImgDir(), _PS_IMG_DIR_, null, ['delete' => true]);
    }

    /**
     * Resets test downloads directory to initial state
     */
    public function resetDownloads(): void
    {
        $this->filesystem->remove(_PS_DOWNLOAD_DIR_);
        $this->filesystem->mirror($this->getBackupTestDownloadsDir(), _PS_DOWNLOAD_DIR_, null, ['delete' => true]);
    }

    /**
     * Resets test modules directory to initial state
     */
    public function resetTestModules(): void
    {
        $this->filesystem->remove(static::TEST_MODULES_DIR);
        $this->filesystem->mirror($this->getBackupTestModulesDir(), static::TEST_MODULES_DIR, null, ['delete' => true]);
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

    /**
     * Provide test modules directory path, in which initial test modules state should be saved
     *
     * @return string
     */
    public function getBackupTestModulesDir(): string
    {
        return $this->backupRootDir . DIRECTORY_SEPARATOR . self::BACKUP_TEST_MODULES_DIR;
    }
}
