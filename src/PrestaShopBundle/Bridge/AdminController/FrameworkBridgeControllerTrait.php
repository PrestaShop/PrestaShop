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

use PrestaShopBundle\Bridge\Exception\BridgeException;

/* @phpstan-ignore-next-line */

/**
 * Contains reusable methods for horizontally migrated controllers, this trait is used to help implement the
 *
 * @see FrameworkBridgeControllerInterface that is used for horizontal controllers and is required to be used by the
 * @see InitFrameworkBridgeControllerListener which is responsible for initializing horizontal controllers.
 */
trait FrameworkBridgeControllerTrait
{
    /**
     * @var ControllerConfiguration|null
     */
    private $controllerConfiguration;

    /**
     * @var LegacyControllerBridgeInterface|null
     */
    private $legacyControllerBridge;

    /**
     * @return LegacyControllerBridgeInterface
     */
    public function getLegacyControllerBridge(): LegacyControllerBridgeInterface
    {
        if ($this->legacyControllerBridge) {
            return $this->legacyControllerBridge;
        }

        $this->legacyControllerBridge = $this->get('prestashop.bridge.admin_controller.legacy_controller_bridge_factory')
            ->create($this->getControllerConfiguration())
        ;

        return $this->legacyControllerBridge;
    }

    /**
     * @param string $tableName
     * @param string $objectModelClassName
     * @param string $legacyControllerName
     *
     * @return ControllerConfiguration
     *
     * @throws BridgeException
     */
    protected function buildControllerConfiguration(
        string $tableName,
        string $objectModelClassName,
        string $legacyControllerName
    ): ControllerConfiguration {
        if ($this->controllerConfiguration) {
            return $this->controllerConfiguration;
        }

        $this->controllerConfiguration = $this
            ->get(ControllerConfigurationFactory::class)
            ->create($legacyControllerName, $objectModelClassName, $tableName)
        ;

        return $this->controllerConfiguration;
    }
}
