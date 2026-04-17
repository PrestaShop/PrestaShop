<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\PrestaShopBundle\EventListener\Admin\Context;

use PHPUnit\Framework\MockObject\MockObject;
use PrestaShop\PrestaShop\Adapter\ContextStateManager;
use PrestaShop\PrestaShop\Adapter\Language\Repository\LanguageRepository as ObjectModelLanguageRepository;
use PrestaShop\PrestaShop\Core\Context\Employee;
use PrestaShop\PrestaShop\Core\Context\EmployeeContext;
use PrestaShop\PrestaShop\Core\Context\LanguageContextBuilder;
use PrestaShop\PrestaShop\Core\Language\LanguageRepositoryInterface;
use PrestaShop\PrestaShop\Core\Localization\Locale\RepositoryInterface;
use PrestaShopBundle\EventListener\Admin\Context\LanguageContextSubscriber;
use Symfony\Component\HttpFoundation\Request;
use Tests\Unit\PrestaShopBundle\EventListener\ContextEventListenerTestCase;

class LanguageContextSubscriberTest extends ContextEventListenerTestCase
{
    private const EMPLOYEE_CONTEXT_LANGUAGE_ID = 42;
    private const DEFAULT_CONFIGURATION_LANGUAGE_ID = 99;

    public function testContextEmployeeLanguage(): void
    {
        $languageContextBuilder = new LanguageContextBuilder(
            $this->createMock(LanguageRepositoryInterface::class),
            $this->createMock(RepositoryInterface::class),
            $this->createMock(ContextStateManager::class),
            $this->createMock(ObjectModelLanguageRepository::class)
        );
        $listener = new LanguageContextSubscriber(
            $languageContextBuilder,
            $this->mockEmployeeContext(self::EMPLOYEE_CONTEXT_LANGUAGE_ID),
            $this->mockConfiguration(),
        );

        $event = $this->createRequestEvent(new Request());
        $listener->initLanguageContext($event);
        $this->assertEquals(self::EMPLOYEE_CONTEXT_LANGUAGE_ID, $this->getPrivateField($languageContextBuilder, 'languageId'));
    }

    public function testDefaultConfigurationLanguage(): void
    {
        $languageContextBuilder = new LanguageContextBuilder(
            $this->createMock(LanguageRepositoryInterface::class),
            $this->createMock(RepositoryInterface::class),
            $this->createMock(ContextStateManager::class),
            $this->createMock(ObjectModelLanguageRepository::class)
        );
        $listener = new LanguageContextSubscriber(
            $languageContextBuilder,
            $this->createMock(EmployeeContext::class),
            $this->mockConfiguration(['PS_LANG_DEFAULT' => self::DEFAULT_CONFIGURATION_LANGUAGE_ID]),
        );

        $event = $this->createRequestEvent(new Request());
        $listener->initDefaultLanguageContext($event);
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
