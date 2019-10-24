<?php
/**
 * 2007-2019 PrestaShop SA and Contributors
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace LegacyTests\Endpoints;

use AppKernel;
use Cache;
use Context;
use Employee;
use PhpEncryption;
use PrestaShop\PrestaShop\Adapter\SymfonyContainer;
use PrestaShopBundle\Security\Admin\Employee as LoggedEmployee;
use ReflectionClass;
use Symfony\Component\Security\Core\Authentication\Token\AbstractToken;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Component\Security\Core\Role\Role;

abstract class AbstractEndpointAdminTest extends AbstractEndpointTest
{
    /**
     * @var TokenStorage
     */
    private $tokenStorageBackup;

    protected function setUp()
    {
        parent::setUp();
        $this->initContainerInstance();
        if (!defined('_PS_TAB_MODULE_LIST_URL_')) {
            define('_PS_TAB_MODULE_LIST_URL_', '');
        }
        Context::getContext()->employee = new Employee(1);

        // Backing up token storage because it will be replaced with a mock during testing
        $this->tokenStorageBackup = SymfonyContainer::getInstance()->get('security.token_storage');
    }

    protected function tearDown()
    {
        parent::tearDown();

        // Restoring the token storage backup
        SymfonyContainer::getInstance()->set('security.token_storage', $this->tokenStorageBackup);
    }

    protected function employeeLogin()
    {
        $cipherTool = new PhpEncryption(_NEW_COOKIE_KEY_);
        $cookieContent = 'id_employee|1¤';
        $cookieContent .= 'checksum|' . hash('sha256', _COOKIE_IV_ . $cookieContent);
        $cookieName = 'PrestaShop-' . md5(_PS_VERSION_ . 'psAdmin');
        $_COOKIE[$cookieName] = $cipherTool->encrypt($cookieContent);
        Cache::store('isLoggedBack' . 1, true);

        $this->symfonyLogIn();
    }

    /**
     * Emulates a real employee logged to the Back Office.
     */
    protected function symfonyLogIn()
    {
        $loggedEmployeeData = new \stdClass();
        $loggedEmployeeData->email = 'demo@prestashop.com';
        $loggedEmployeeData->id = 1;
        $loggedEmployeeData->passwd = '';
        $loggedEmployeeMock = new LoggedEmployee($loggedEmployeeData);

        $tokenMock = $this
            ->getMockBuilder(AbstractToken::class)
            ->setMethods([
                'getUser',
                'getRoles',
                'isAuthenticated',
            ])
            ->getMockForAbstractClass();

        $tokenMock->expects($this->any())
            ->method('getUser')
            ->willReturn($loggedEmployeeMock);

        $tokenMock->expects($this->any())
            ->method('getRoles')
            ->willReturn([new Role(LoggedEmployee::DEFAULT_EMPLOYEE_ROLE)]);

        $tokenMock->expects($this->any())
            ->method('isAuthenticated')
            ->willReturn(true);

        $tokenStorageMock = $this->getMockBuilder(TokenStorage::class)
            ->setMethods([
                'getToken',
            ])
            ->disableAutoload()
            ->disableOriginalConstructor()
            ->getMock();

        $tokenStorageMock->method('getToken')
            ->willReturn($tokenMock);

        SymfonyContainer::getInstance()->set('security.token_storage', $tokenStorageMock);
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
