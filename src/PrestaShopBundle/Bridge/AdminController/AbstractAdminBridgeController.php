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
use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use Tab;
use Tools;

/**
 * Abstract controller for all Horizontal migration controllers
 */
abstract class AbstractAdminBridgeController extends FrameworkBundleAdminController implements LegacyControllerBridgeInterface
{
    /**
     * This parameter is needed by legacy hook, so we can't remove it.
     *
     * @var string
     */
    public $php_self;

    /**
     * This parameter is needed by legacy helper shop, so we can't remove it.
     *
     * @var bool
     */
    public $multishop_context_group = true;

    /**
     * This parameter is needed by legacy helper shop, we can't remove it.
     *
     * @var int
     */
    public $multishop_context;

    /**
     * @var ControllerConfiguration|null
     */
    protected $bridgeControllerConfiguration;

    /**
     * Name of database table containing related object model data
     *
     * @return string
     */
    abstract protected function getTableName(): string;

    /**
     * Name of class name of related object model
     *
     * @return string
     */
    abstract protected function getClassName(): string;

    /**
     * Related legacy controller name
     *
     * @return string
     */
    abstract protected function getLegacyControllerName(): string;

    /**
     * Initializes Bridge controller configuration
     *
     * @return void
     *
     * @throws BridgeException
     */
    public function initBridgeConfiguration(): void
    {
        $legacyControllerName = $this->getLegacyControllerName();
        $tabId = Tab::getIdFromClassName($legacyControllerName);

        if (!$tabId) {
            throw new BridgeException(sprintf(
                'Tab id not found by className "%s". Make sure you have provided valid legacy controller name.',
                $legacyControllerName
            ));
        }

        $this->bridgeControllerConfiguration = $this->getConfigurationFactory()->create(
            $tabId,
            get_class($this),
            $this->getLegacyControllerName(),
            $this->getTableName()
        );

        $this->multishop_context = $this->bridgeControllerConfiguration->multishopContext;
        $this->setLegacyCurrentIndex();
        $this->initToken();
    }

    /**
     * @return ControllerConfigurationFactory
     */
    protected function getConfigurationFactory(): ControllerConfigurationFactory
    {
        /** @var ControllerConfigurationFactory $factory */
        $factory = $this->get('prestashop.core.bridge.controller_configuration_factory');

        return $factory;
    }

    /**
     * @return void
     */
    protected function setLegacyCurrentIndex(): void
    {
        if (!$this->bridgeControllerConfiguration) {
            throw new BridgeException('Controller configuration must be initialized first');
        }

        $legacyCurrentIndex = 'index.php' . '?controller=' . $this->getLegacyControllerName();
        if ($back = Tools::getValue('back')) {
            $legacyCurrentIndex .= '&back=' . urlencode($back);
        }

        $this->bridgeControllerConfiguration->legacyCurrentIndex = $legacyCurrentIndex;
    }

    /**
     * @return void
     */
    protected function initToken(): void
    {
        $controllerConfiguration = $this->bridgeControllerConfiguration;

        if (!$controllerConfiguration) {
            throw new BridgeException('Controller configuration must be initialized first');
        }

        $controllerConfiguration->token = Tools::getAdminToken(
            $controllerConfiguration->controllerNameLegacy .
            (int) $controllerConfiguration->id .
            (int) $controllerConfiguration->user->getData()->id
        );
    }
}
