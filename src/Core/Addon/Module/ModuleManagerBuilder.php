<?php
/**
 * 2007-2015 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2015 PrestaShop SA
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */
namespace PrestaShop\PrestaShop\Core\Addon\Module;

use PrestaShop\PrestaShop\Adapter\Module\AdminModuleDataProvider;
use PrestaShop\PrestaShop\Adapter\Module\ModuleDataProvider;
use PrestaShop\PrestaShop\Adapter\Module\ModuleDataUpdater;
use PrestaShop\PrestaShop\Core\Addon\Module\ModuleManager;

class ModuleManagerBuilder
{
    /**
     * Singleton of ModuleManager
     * @var \PrestaShop\PrestaShop\Core\Addon\Module\ModuleManager
     */
    public static $modulesManager = null;
    /**
     * Singleton of ModuleRepository
     * @var \PrestaShop\PrestaShop\Core\Addon\Module\ModuleRepository
     */
    public static $modulesRepository = null;

    public function build()
    {
        /**
        * Return an instance of \PrestaShop\PrestaShop\Core\Addon\Module\ModuleManager
        * @global type $kernel
        * @return \PrestaShop\PrestaShop\Core\Addon\Module\ModuleManager
        */
        if (is_null(self::$modulesManager)) {
            global $kernel;

            self::$modulesManager = new ModuleManager(new AdminModuleDataProvider($kernel),
                new ModuleDataProvider(),
                new ModuleDataUpdater(),
                $this->buildRepository(),
                \Context::getContext()->employee);
        }
        return self::$modulesManager;
    }

    /**
     * Return an instance of \PrestaShop\PrestaShop\Core\Addon\Module\ModuleRepository
     * @global type $kernel
     * @return \PrestaShop\PrestaShop\Core\Addon\Module\ModuleRepository
     */
    public function buildRepository()
    {
        if (is_null(self::$modulesRepository)) {
            global $kernel;

            self::$modulesRepository = new ModuleRepository(
                new AdminModuleDataProvider($kernel),
                new ModuleDataProvider(),
                new ModuleDataUpdater()
            );
        }
        return self::$modulesRepository;
    }
}
