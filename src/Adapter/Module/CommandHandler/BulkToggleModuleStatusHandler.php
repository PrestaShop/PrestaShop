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

namespace PrestaShop\PrestaShop\Adapter\Module\CommandHandler;

use Module;
use PrestaShop\PrestaShop\Core\Domain\Module\Command\BulkToggleModuleStatusCommand;
use PrestaShop\PrestaShop\Core\Domain\Module\CommandHandler\BulkToggleModuleStatusHandlerInterface;
use PrestaShop\PrestaShop\Core\Module\ModuleManager;
use Psr\Log\LoggerInterface;

/**
 * Bulk toggles Module status
 */
class BulkToggleModuleStatusHandler implements BulkToggleModuleStatusHandlerInterface
{
    /**
     * @return ModuleManager
     */
    protected $moduleManager;
    /**
     * @return LoggerInterface
     */
    protected $logger;

    /**
     * @param ModuleManager $moduleManager
     */
    public function __construct(ModuleManager $moduleManager, LoggerInterface $logger)
    {
        $this->moduleManager = $moduleManager;
        $this->logger = $logger;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(BulkToggleModuleStatusCommand $command): void
    {
        foreach ($command->getModules() as $moduleName) {
            $module = Module::getInstanceByName($moduleName);
            if (!$module) {
                continue;
            }

            if ($command->getExpectedStatus()) {
                if ($this->moduleManager->enable($moduleName)) {
                    $this->logger->warning(
                        sprintf(
                            'The module %s has been enabled',
                            $moduleName
                        )
                    );
                }
            } else {
                if ($this->moduleManager->disable($moduleName)) {
                    $this->logger->warning(
                        sprintf(
                            'The module %s has been disabled',
                            $moduleName
                        )
                    );
                }
            }
        }
    }
}
