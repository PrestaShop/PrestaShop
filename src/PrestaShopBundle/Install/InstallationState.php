<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\Install;

use Symfony\Component\Filesystem\Filesystem;

/**
 * Tells whether the shop is already installed: its settings file exists and no installation is running any more
 * (the lock file is written when the settings file is generated and removed once the installation is finalized).
 */
final class InstallationState
{
    public function __construct(
        private readonly string $settingsFile,
        private readonly string $lockFile,
        private readonly Filesystem $filesystem = new Filesystem(),
    ) {
    }

    public function isInstalled(): bool
    {
        return $this->filesystem->exists($this->settingsFile) && !$this->filesystem->exists($this->lockFile);
    }
}
