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

use PrestaShopBundle\Service\DataProvider\UserProvider;
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
     * @param UserProvider $userProvider
     */
    public function __construct(
        UserProvider $userProvider
    ) {
        $this->userProvider = $userProvider;
    }

    /**
     * @param int $id
     * @param string $controllerName
     * @param string $controllerNameLegacy
     * @param string $table
     *
     * @return ControllerConfiguration
     */
    public function create(
        int $id,
        string $controllerName,
        string $controllerNameLegacy,
        string $table
    ): ControllerConfiguration {
        $configuratorController = new ControllerConfiguration();

        $configuratorController->id = $id;
        $configuratorController->controllerName = $controllerName;
        $configuratorController->controllerNameLegacy = $controllerNameLegacy;
        $configuratorController->table = $table;
        $configuratorController->user = $this->userProvider->getUser();
        $configuratorController->folderTemplate = Tools::toUnderscoreCase(substr($configuratorController->controllerNameLegacy, 5)) . '/';

        return $configuratorController;
    }
}
