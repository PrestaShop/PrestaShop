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

namespace LegacyTests\Endpoints;

use Tools;
use AppKernel;
use Cache;
use Context;
use Employee;
use EmployeeSession;
use Shop;
use PhpEncryption;
use PrestaShop\PrestaShop\Adapter\SymfonyContainer;
use ReflectionClass;

abstract class AbstractEndpointAdminTest extends AbstractEndpointTest
{
    protected function setUp(): void
    {
        $_SERVER['REQUEST_URI'] = $_SERVER['SCRIPT_NAME'];

        parent::setUp();

        $this->initContainerInstance();

        if (!defined('_PS_TAB_MODULE_LIST_URL_')) {
            define('_PS_TAB_MODULE_LIST_URL_', '');
        }

        Shop::initialize();
        Context::getContext()->employee = new Employee(1);
    }

    protected function employeeLogin()
    {
        $cipherTool = new PhpEncryption(_NEW_COOKIE_KEY_);

        // Create Employee Session to be sure the user is connected
        $session = new EmployeeSession();
        $session->setUserId(1);
        $session->setToken(sha1(time() . uniqid()));
        $session->add();

        $cookieContent = 'id_employee|1¤session_id|' . $session->getId() . '¤session_token|' . $session->getToken() . '¤';
        $cookieContent .= 'checksum|' . hash('sha256', _COOKIE_IV_ . $cookieContent);

        // Reproduce Cookie::getDomain behavior
        $httpHost = Tools::getHttpHost(false, false);
        if (!strstr($httpHost, '.')) {
            $httpHost = false;
        }

        $cookieName = 'PrestaShop-' . md5(_PS_VERSION_ . 'psAdmin' . $httpHost);
        $_COOKIE[$cookieName] = $cipherTool->encrypt($cookieContent);

        Cache::store('isLoggedBack' . 1, true);
    }

    /**
     * Force the static property SymfonyContainer::instance so that the Link class
     * has access to the router
     *
     * @throws \ReflectionException
     */
    private function initContainerInstance()
    {
        $kernel = new AppKernel('test', true);
        $kernel->boot();

        $reflectedClass = new ReflectionClass(SymfonyContainer::class);
        $instanceProperty = $reflectedClass->getProperty('instance');
        $instanceProperty->setAccessible(true);
        $instanceProperty->setValue($kernel->getContainer());
        $instanceProperty->setAccessible(false);
    }
}
