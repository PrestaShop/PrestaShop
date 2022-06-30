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

namespace PrestaShopBundle\Bridge\AdminController;

use PrestaShopBundle\Bridge\AdminController\Action\ActionInterface;
use PrestaShopBundle\Bridge\AdminController\Action\HeaderToolbarAction;
use PrestaShopBundle\Bridge\Exception\BridgeException;
use PrestaShopBundle\Bridge\Exception\NotAllowedGenericActionTypeException;
use Tab;

/**
 * Contains reusable methods for horizontally migrated controllers
 */
trait AdminControllerTrait
{
    /**
     * @var LegacyControllerBridge
     */
    private $legacyControllerBridge;

    /**
     * This method add action for the page.
     *
     * @param ActionInterface $action
     *
     * @return void
     */
    public function addAction(ActionInterface $action): void
    {
        if ($action instanceof HeaderToolbarAction) {
            $this->legacyControllerBridge->getControllerConfiguration()->pageHeaderToolbarButton[$action->getLabel()] = $action->getConfig();

            return;
        }

        throw new NotAllowedGenericActionTypeException(sprintf('This action %s doesn\'t exist', get_class($action)));
    }

    /**
     * @return LegacyControllerBridgeInterface
     */
    public function initLegacyControllerBridge(): LegacyControllerBridgeInterface
    {
        $legacyControllerName = $this->getLegacyControllerName();
        $tabId = Tab::getIdFromClassName($legacyControllerName);

        if (!$tabId) {
            throw new BridgeException(sprintf(
                'Tab not found by className "%s". Make sure legacyControllerName is correct',
                $legacyControllerName
            ));
        }

        $controllerConfiguration = $this->getControllerConfigurationFactory()->create(
            $tabId,
            get_class($this),
            $legacyControllerName,
            $this->getTableName()
        );

        $this->legacyControllerBridge = new LegacyControllerBridge($this->container, $controllerConfiguration);

        return $this->legacyControllerBridge;
    }

    private function getControllerConfigurationFactory(): ControllerConfigurationFactory
    {
        /** @var ControllerConfigurationFactory $factory */
        $factory = $this->container->get('prestashop.core.bridge.controller_configuration_factory');

        return $factory;
    }
}
