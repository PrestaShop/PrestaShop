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
 *  @author     PrestaShop SA <contact@prestashop.com>
 *  @copyright  2007-2015 PrestaShop SA
 *  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */
namespace PrestaShop\PrestaShop\Adapter\Module;

use PrestaShop\PrestaShop\Adapter\Admin\AbstractAdminQueryBuilder;
use PrestaShopBundle\Service\DataProvider\Admin\ModuleInterface;

/**
 * Data provider for new Architecture, about Module object model.
 *
 * This class will provide data from DB / ORM about Modules for the Admin interface.
 * This is an Adapter that works with the Legacy code and persistence behaviors.
 *
 * FIXME: rewrite persistence of filter parameters -> into DB
 */
class AdminModuleDataProvider extends AbstractAdminQueryBuilder implements ModuleInterface
{
    private $is_employee_addons_logged = false;

    public function __construct()
    {
        $context = \Context::getContext();
        if (isset($context->cookie->username_addons)
            && isset($context->cookie->password_addons)
            && !empty($context->cookie->username_addons)
            && !empty($context->cookie->password_addons)) {
            $this->is_employee_addons_logged = true;
        }
    }

    public function getAllModules()
    {
        return \Module::getModulesOnDisk(true, (bool)$this->is_employee_addons_logged, (int)\Context::getContext()->employee->id);
    }
}
