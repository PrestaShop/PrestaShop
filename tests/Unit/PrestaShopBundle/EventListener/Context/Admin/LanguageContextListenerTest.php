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

use PHPUnit\Framework\MockObject\MockObject;
use PrestaShop\PrestaShop\Core\Context\Employee;
use PrestaShop\PrestaShop\Core\Context\EmployeeContext;
use PrestaShop\PrestaShop\Core\Context\LanguageContextBuilder;
use PrestaShop\PrestaShop\Core\Language\LanguageRepositoryInterface;
use PrestaShop\PrestaShop\Core\Localization\Locale\Repository;
use Symfony\Component\HttpFoundation\Request;
use Tests\Unit\PrestaShopBundle\EventListener\Context\ContextEventListenerTestCase;

class LanguageContextListenerTest extends ContextEventListenerTestCase
{
    private const EMPLOYEE_CONTEXT_LANGUAGE_ID = 42;
    private const DEFAULT_CONFIGURATION_LANGUAGE_ID = 99;

    public function testContextEmployeeLanguage(): void
    {
        $languageContextBuilder = new LanguageContextBuilder(
            $this->createMock(LanguageRepositoryInterface::class),
            $this->createMock(Repository::class),
        );
        $listener = new LanguageContextListener(
            $languageContextBuilder,
            $this->mockEmployeeContext(self::EMPLOYEE_CONTEXT_LANGUAGE_ID),
            $this->mockConfiguration(),
        );

        $event = $this->createRequestEvent(new Request());
        $listener->onKernelRequest($event);
        $this->assertEquals(self::EMPLOYEE_CONTEXT_LANGUAGE_ID, $this->getPrivateField($languageContextBuilder, 'languageId'));
    }

    public function testDefaultConfigurationLanguage(): void
    {
        $languageContextBuilder = new LanguageContextBuilder(
            $this->createMock(LanguageRepositoryInterface::class),
            $this->createMock(Repository::class),
        );
        $listener = new LanguageContextListener(
            $languageContextBuilder,
            $this->mockEmployeeContext(null),
            $this->mockConfiguration(['PS_LANG_DEFAULT' => self::DEFAULT_CONFIGURATION_LANGUAGE_ID]),
        );

        $event = $this->createRequestEvent(new Request());
        $listener->onKernelRequest($event);
        $this->assertEquals(self::DEFAULT_CONFIGURATION_LANGUAGE_ID, $this->getPrivateField($languageContextBuilder, 'languageId'));
    }

    private function mockEmployeeContext(?int $languageId): EmployeeContext|MockObject
    {
        $employeeContext = $this->createMock(EmployeeContext::class);

        if ($languageId) {
            $employee = $this->createMock(Employee::class);
            $employee
                ->method('getLanguageId')
                ->willReturn($languageId)
            ;
            $employeeContext
                ->method('getEmployee')
                ->willReturn($employee)
            ;
        } else {
            $employeeContext
                ->method('getEmployee')
                ->willReturn(null)
            ;
        }

        return $employeeContext;
    }
}
