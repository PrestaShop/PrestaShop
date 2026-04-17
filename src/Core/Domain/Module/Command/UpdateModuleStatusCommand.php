<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Module\Command;

use PrestaShop\PrestaShop\Core\Domain\Module\ValueObject\ModuleTechnicalName;

class UpdateModuleStatusCommand
{
    protected ModuleTechnicalName $technicalName;

    public function __construct(
        string $technicalName,
        protected bool $enabled,
    ) {
        $this->technicalName = new ModuleTechnicalName($technicalName);
    }

    public function getTechnicalName(): ModuleTechnicalName
    {
        return $this->technicalName;
    }

    public function isEnabled(): bool
    {
        return $this->enabled;
    }
}
