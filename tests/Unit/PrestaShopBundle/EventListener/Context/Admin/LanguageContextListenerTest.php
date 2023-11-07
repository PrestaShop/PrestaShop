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

namespace PrestaShopBundle\EventListener\Context\Admin;

use Employee;
use PHPUnit\Framework\MockObject\MockObject;
use PrestaShop\PrestaShop\Adapter\Employee\EmployeeRepository;
use PrestaShop\PrestaShop\Core\Context\LanguageContextBuilder;
use PrestaShop\PrestaShop\Core\Language\LanguageRepositoryInterface;
use PrestaShop\PrestaShop\Core\Localization\Locale\Repository;
use Symfony\Component\HttpFoundation\Request;
use Tests\Unit\PrestaShopBundle\EventListener\Context\ContextEventListenerTestCase;

class LanguageContextListenerTest extends ContextEventListenerTestCase
{
    private const CONTEXT_EMPLOYEE_LANGUAGE_ID = 42;
    private const COOKIE_EMPLOYEE_LANGUAGE_ID = 51;
    private const COOKIE_EMPLOYEE_ID = 69;
    private const DEFAULT_CONFIGURATION_LANGUAGE_ID = 99;

    public function testContextEmployeeLanguage(): void
    {
        $languageContextBuilder = new LanguageContextBuilder(
            $this->createMock(LanguageRepositoryInterface::class),
            $this->createMock(Repository::class),
        );
        $listener = new LanguageContextListener(
            $languageContextBuilder,
            $this->mockLegacyContext([], [
                'employee' => $this->mockEmployee(self::CONTEXT_EMPLOYEE_LANGUAGE_ID),
            ]),
            $this->mockEmployeeRepository(),
            $this->mockConfiguration(),
        );

        $event = $this->createRequestEvent(new Request());
        $listener->onKernelRequest($event);
        $this->assertEquals(self::CONTEXT_EMPLOYEE_LANGUAGE_ID, $this->getPrivateField($languageContextBuilder, 'languageId'));
    }

    public function testCookieEmployeeLanguage(): void
    {
        $languageContextBuilder = new LanguageContextBuilder(
            $this->createMock(LanguageRepositoryInterface::class),
            $this->createMock(Repository::class),
        );
        $listener = new LanguageContextListener(
            $languageContextBuilder,
            $this->mockLegacyContext(['id_employee' => self::COOKIE_EMPLOYEE_ID]),
            $this->mockEmployeeRepository($this->mockEmployee(self::COOKIE_EMPLOYEE_LANGUAGE_ID), self::COOKIE_EMPLOYEE_ID),
            $this->mockConfiguration(),
        );

        $event = $this->createRequestEvent(new Request());
        $listener->onKernelRequest($event);
        $this->assertEquals(self::COOKIE_EMPLOYEE_LANGUAGE_ID, $this->getPrivateField($languageContextBuilder, 'languageId'));
    }

    public function testDefaultConfigurationLanguage(): void
    {
        $languageContextBuilder = new LanguageContextBuilder(
            $this->createMock(LanguageRepositoryInterface::class),
            $this->createMock(Repository::class),
        );
        $listener = new LanguageContextListener(
            $languageContextBuilder,
            $this->mockLegacyContext(),
            $this->mockEmployeeRepository(),
            $this->mockConfiguration(['PS_LANG_DEFAULT' => self::DEFAULT_CONFIGURATION_LANGUAGE_ID]),
        );

        $event = $this->createRequestEvent(new Request());
        $listener->onKernelRequest($event);
        $this->assertEquals(self::DEFAULT_CONFIGURATION_LANGUAGE_ID, $this->getPrivateField($languageContextBuilder, 'languageId'));
    }

    private function mockEmployee(int $languageId): Employee|MockObject
    {
        $employee = $this->createMock(Employee::class);
        $employee->id_lang = $languageId;
        $employee
            ->method('isLoggedBack')
            ->willReturn(true)
        ;

        return $employee;
    }

    private function mockEmployeeRepository(?Employee $employee = null, ?int $expectedEmployeeId = null): EmployeeRepository|MockObject
    {
        $repository = $this->createMock(EmployeeRepository::class);
        if ($employee) {
            $repository
                ->method('get')
                ->with($expectedEmployeeId)
                ->willReturn($employee)
            ;
        }

        return $repository;
    }
}
