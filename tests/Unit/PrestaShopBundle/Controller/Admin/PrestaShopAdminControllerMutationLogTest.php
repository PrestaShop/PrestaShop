<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace Tests\Unit\PrestaShopBundle\Controller\Admin;

use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Context\Employee;
use PrestaShop\PrestaShop\Core\Context\EmployeeContext;
use PrestaShop\PrestaShop\Core\Mutation\BackOfficeMutationLogger;
use PrestaShop\PrestaShop\Core\Mutation\MutationTracker;
use PrestaShopBundle\Controller\Admin\PrestaShopAdminController;
use PrestaShopBundle\Entity\MutationAction;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\Compiler\ServiceLocatorTagPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Contracts\Translation\TranslatorInterface;

class PrestaShopAdminControllerMutationLogTest extends TestCase
{
    public function testDelegateUsesSubscribedServiceAndPreservesOrder(): void
    {
        $this->assertSame(BackOfficeMutationLogger::class, PrestaShopAdminController::getSubscribedServices()[BackOfficeMutationLogger::class]);
        $tracker = $this->createMock(MutationTracker::class);
        $recorded = [];
        $tracker->expects($this->exactly(2))->method('addMutationForEmployee')
            ->willReturnCallback(static function (...$arguments) use (&$recorded): void { $recorded[] = $arguments; });
        $legacyLogger = $this->createMock(LoggerInterface::class);
        $legacyLogger->expects($this->exactly(2))->method('info');
        $technicalLogger = $this->createMock(LoggerInterface::class);
        $technicalLogger->expects($this->never())->method('error');
        $translator = $this->createMock(TranslatorInterface::class);
        $translator->method('trans')->willReturnArgument(0);
        $manager = $this->createMock(EntityManagerInterface::class);
        $manager->method('isOpen')->willReturn(true);
        $request = new Request();
        $request->attributes->set(BackOfficeMutationLogger::REQUEST_ATTRIBUTE, true);
        $stack = new RequestStack();
        $stack->push($request);
        $logger = new BackOfficeMutationLogger($tracker, new EmployeeContext($this->createMock(Employee::class), []), $stack, $translator, $legacyLogger, $technicalLogger, $manager);

        $container = new ContainerBuilder();
        $container->setDefinition(BackOfficeMutationLogger::class, (new Definition(BackOfficeMutationLogger::class))->setSynthetic(true));
        $locator = ServiceLocatorTagPass::register($container, [BackOfficeMutationLogger::class => new Reference(BackOfficeMutationLogger::class)]);
        $container->setAlias('controller_locator', (string) $locator)->setPublic(true);
        $container->compile();
        $container->set(BackOfficeMutationLogger::class, $logger);
        $controller = new class() extends PrestaShopAdminController {
            public function record(string $table, int $id, MutationAction $action, array $details = []): void
            {
                $this->recordMutation($table, $id, $action, $details);
            }
        };
        $controller->setContainer($container->get('controller_locator'));
        $controller->record('product', 42, MutationAction::DELETE);
        $controller->record('product', 142, MutationAction::CREATE, ['source_id' => 42, 'bulk' => true]);
        $this->assertSame([
            ['product', 42, MutationAction::DELETE, ''],
            ['product', 142, MutationAction::CREATE, '{"source_id":42,"bulk":true}'],
        ], $recorded);
    }
}
