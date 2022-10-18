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
use PrestaShopBundle\Security\Admin\Employee;
use PrestaShopBundle\Service\DataProvider\UserProvider;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Tools;

/**
 * Create an instance of the controller configuration object.
 */
class ControllerConfigurationFactory
{
    /**
     * @var UserProvider
     */
    private $userProvider;

    /**
     * @var FlashBagInterface
     */
    private $flashBag;

    /**
     * @param UserProvider $userProvider
     * @param FlashBagInterface $flashBag
     */
    public function __construct(
        UserProvider $userProvider,
        FlashBagInterface $flashBag
    ) {
        $this->userProvider = $userProvider;
        $this->flashBag = $flashBag;
    }

    /**
     * @param int $tabId
     * @param string $objectModelClassName
     * @param string $legacyControllerName
     * @param string $tableName
     *
     * @return ControllerConfiguration
     */
    public function create(
        int $tabId,
        string $objectModelClassName,
        string $legacyControllerName,
        string $tableName
    ): ControllerConfiguration {
        $employee = $this->userProvider->getUser();
        if (!$employee instanceof Employee) {
            throw new BridgeException(
                sprintf(
                    'Unexpected user type. Expected "%s", got "%s',
                    Employee::class,
                    get_class($employee))
            );
        }

        $controllerConfiguration = new ControllerConfiguration(
            $employee,
            $tabId,
            $objectModelClassName,
            $legacyControllerName,
            $tableName,
            Tools::toUnderscoreCase(substr($legacyControllerName, 5)) . '/'
        );

        $this->setLegacyCurrentIndex($controllerConfiguration);
        $this->setToken($controllerConfiguration);
        $this->setFlashes($controllerConfiguration);

        return $controllerConfiguration;
    }

    /**
     * @param ControllerConfiguration $controllerConfiguration
     *
     * @return void
     */
    private function setLegacyCurrentIndex(ControllerConfiguration $controllerConfiguration): void
    {
        // We do not use \Link->getAdminLink() because it produces wrong legacy currentIndex.
        // Even though we could solve issues with "view" and "edit" links by using AdminLinkBuilder, the same issues appears
        // with the "deletion", "duplicate", "enable" links and as it gets more complicated, it seems to be safer/easier to build the index url manually.
        $legacyCurrentIndex = 'index.php' . '?controller=' . $controllerConfiguration->legacyControllerName;

        if ($back = Tools::getValue('back')) {
            $legacyCurrentIndex .= '&back=' . urlencode($back);
        }

        $controllerConfiguration->legacyCurrentIndex = $legacyCurrentIndex;
    }

    /**
     * @return void
     */
    private function setToken(ControllerConfiguration $controllerConfiguration): void
    {
        $controllerConfiguration->token = Tools::getAdminToken(
            $controllerConfiguration->legacyControllerName .
            (int) $controllerConfiguration->tabId .
            (int) $controllerConfiguration->getUser()->getData()->id
        );
    }

    /**
     * @param ControllerConfiguration $controllerConfiguration
     */
    private function setFlashes(ControllerConfiguration $controllerConfiguration): void
    {
        $controllerConfiguration->confirmations = $this->flashBag->get('success');
        $controllerConfiguration->warnings = $this->flashBag->get('warning');
        $controllerConfiguration->errors = $this->flashBag->get('error');
        $controllerConfiguration->informations = $this->flashBag->get('info');
    }
}
