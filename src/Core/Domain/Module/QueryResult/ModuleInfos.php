<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Module\QueryResult;

class ModuleInfos
{
    public function __construct(
        private readonly ?int $moduleId,
        private readonly string $technicalName,
        private readonly string $moduleVersion,
        private readonly ?string $installedVersion,
        private readonly bool $enabled,
        private readonly bool $installed,
    ) {
    }

    public function getModuleId(): ?int
    {
        return $this->moduleId;
    }

    public function getTechnicalName(): string
    {
        return $this->technicalName;
    }

    public function getModuleVersion(): string
    {
        return $this->moduleVersion;
    }

    public function getInstalledVersion(): ?string
    {
        return $this->installedVersion;
    }

    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    public function isInstalled(): bool
    {
        return $this->installed;
    }
}
