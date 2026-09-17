<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace Tests\Unit\Core\Form\IdentifiableObject\Handler;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\ActivityLog\AdminActivity;
use PrestaShop\PrestaShop\Core\ActivityLog\AdminActivityLoggerInterface;
use PrestaShop\PrestaShop\Core\ActivityLog\AdminActivityType;
use PrestaShop\PrestaShop\Core\ExtraProperty\Form\ExtraPropertiesFormDataPersister;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\DataHandler\FormDataHandlerInterface;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\Handler\FormHandlerFactory;
use PrestaShop\PrestaShop\Core\Hook\HookDispatcherInterface;
use Symfony\Component\Form\FormConfigInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\ResolvedFormTypeInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class FormHandlerFactoryActivityLogTest extends TestCase
{
    public function testFactoryForwardsActivityLogObjectTypeToCreatedHandler(): void
    {
        $hookDispatcher = $this->createMock(HookDispatcherInterface::class);
        $translator = $this->createMock(TranslatorInterface::class);
        $persister = $this->createMock(ExtraPropertiesFormDataPersister::class);
        $activityLogger = $this->createMock(AdminActivityLoggerInterface::class);
        $dataHandler = $this->createMock(FormDataHandlerInterface::class);

        $dataHandler
            ->expects($this->once())
            ->method('create')
            ->with(['name' => 'Test'])
            ->willReturn(42);

        $activityLogger
            ->expects($this->once())
            ->method('log')
            ->with($this->callback(function (AdminActivity $activity): bool {
                $this->assertSame(AdminActivityType::CREATE, $activity->getType());
                $this->assertSame('Product', $activity->getObjectType());
                $this->assertSame(42, $activity->getObjectId());
                $this->assertSame(42, $activity->getLogObjectId());

                return true;
            }));

        $factory = new FormHandlerFactory(
            $hookDispatcher,
            $translator,
            false,
            $persister,
            $activityLogger
        );

        $handler = $factory->create($dataHandler, 'Product');
        $handler->handle($this->createValidForm());
    }

    private function createValidForm(): FormInterface
    {
        $resolvedType = $this->createMock(ResolvedFormTypeInterface::class);
        $resolvedType
            ->method('getBlockPrefix')
            ->willReturn('product');

        $config = $this->createMock(FormConfigInterface::class);
        $config
            ->method('getType')
            ->willReturn($resolvedType);

        $form = $this->createMock(FormInterface::class);
        $form
            ->method('isSubmitted')
            ->willReturn(true);
        $form
            ->method('isValid')
            ->willReturn(true);
        $form
            ->method('getName')
            ->willReturn('product');
        $form
            ->method('getData')
            ->willReturn(['name' => 'Test']);
        $form
            ->method('getConfig')
            ->willReturn($config);

        return $form;
    }
}
