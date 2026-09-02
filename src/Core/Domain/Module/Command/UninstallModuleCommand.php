<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Module\Command;

use PrestaShop\PrestaShop\Core\Domain\Module\ValueObject\ModuleTechnicalName;

/**
 * Uninstall module
 */
class UninstallModuleCommand
{
    private ModuleTechnicalName $technicalName;

    /**
     * @param string $technicalName Array of technical names for modules
     * @param bool $deleteFiles Boolean for delete module files
     */
    public function __construct(
        string $technicalName,
        private readonly bool $deleteFiles = false
    ) {
        $this->technicalName = new ModuleTechnicalName($technicalName);
    }

    /**
     * @return ModuleTechnicalName
     */
    public function getTechnicalName(): ModuleTechnicalName
    {
        return $this->technicalName;
    }

    /**
     * @return bool
     */
    public function deleteFiles(): bool
    {
        return $this->deleteFiles;
    }
}
