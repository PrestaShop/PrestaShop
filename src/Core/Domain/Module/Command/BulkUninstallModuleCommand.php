<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Module\Command;

use PrestaShop\PrestaShop\Core\Domain\Module\ValueObject\ModuleTechnicalName;

/**
 * Bulk uninstall module
 */
class BulkUninstallModuleCommand
{
    /**
     * @var array<ModuleTechnicalName>
     */
    private array $modules;

    /**
     * @param array<string> $modules Array of technical names for modules
     * @param bool $deleteFiles Boolean for delete modules files
     */
    public function __construct(
        array $modules,
        private readonly bool $deleteFiles = false
    ) {
        $this->modules = array_map(fn (string $technicalName) => new ModuleTechnicalName($technicalName), $modules);
    }

    /**
     * @return array<ModuleTechnicalName>
     */
    public function getModules(): array
    {
        return $this->modules;
    }

    public function deleteFiles(): bool
    {
        return $this->deleteFiles;
    }
}
