<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Module;

use Module as LegacyModule;
use PrestaShop\PrestaShop\Core\Module\ModuleManagerInterface;

final class ModuleHtmlAuthorizationChecker
{
    private array $moduleNames = [];

    public function __construct(
        private readonly Module $moduleAdapter,
        private readonly ModuleManagerInterface $moduleManager
    ) {
    }

    public function isModuleHtmlAllowed(int $moduleId): bool
    {
        if ($moduleId <= 0) {
            return false;
        }

        if (!array_key_exists($moduleId, $this->moduleNames)) {
            /** @var LegacyModule|false $module */
            $module = $this->moduleAdapter->getInstanceById($moduleId);
            $this->moduleNames[$moduleId] = false === $module ? null : $module->name;
        }

        return !empty($this->moduleNames[$moduleId])
            && $this->moduleManager->isEnabled($this->moduleNames[$moduleId]);
    }
}
