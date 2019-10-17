<?php

namespace LegacyTests\Services;

use Context;
use PrestaShopBundle\Security\Admin\Employee as LoggedEmployee;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Role\Role;

trait EmployeeLoginTrait
{
    /**
     * Emulates a real employee logged to the Back Office.
     * Must be used inside a TestCase
     */
    protected function logIn()
    {
        $loggedEmployeeData = new \Employee(1);
        Context::getContext()->employee = $loggedEmployeeData;
        $loggedEmployeeMock = new LoggedEmployee($loggedEmployeeData);

        $token = new UsernamePasswordToken(
            $loggedEmployeeMock,
            null,
            'admin',
            [new Role(LoggedEmployee::DEFAULT_EMPLOYEE_ROLE)]
        );

        $tokenStorageMock = $this->getMockBuilder(TokenStorage::class)
            ->setMethods([
                'getToken',
            ])
            ->getMock();

        $tokenStorageMock->method('getToken')
            ->willReturn($token);

        self::$kernel->getContainer()->set('security.token_storage', $tokenStorageMock);
    }
}
