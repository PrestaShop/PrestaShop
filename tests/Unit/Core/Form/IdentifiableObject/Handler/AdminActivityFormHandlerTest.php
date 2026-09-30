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
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\Handler\FormHandler;
use PrestaShop\PrestaShop\Core\Hook\HookDispatcherInterface;
use RuntimeException;
use Symfony\Component\Form\FormConfigInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\ResolvedFormTypeInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class AdminActivityFormHandlerTest extends TestCase
{
    private $dataHandler;
    private $hookDispatcher;
    private $translator;
    private $extraPropertiesFormDataPersister;
    private $adminActivityLogger;

    public function setUp(): void
    {
        $this->dataHandler = $this->createMock(FormDataHandlerInterface::class);
        $this->hookDispatcher = $this->createMock(HookDispatcherInterface::class);
        $this->translator = $this->createMock(TranslatorInterface::class);
        $this->extraPropertiesFormDataPersister = $this->createMock(ExtraPropertiesFormDataPersister::class);
        $this->adminActivityLogger = $this->createMock(AdminActivityLoggerInterface::class);
    }

    public function testCreateLogsAdminActivityWhenObjectTypeIsConfigured(): void
    {
        $form = $this->createValidForm();

        $this->dataHandler
            ->expects($this->once())
            ->method('create')
            ->with(['name' => 'Test'])
            ->willReturn(42);

        $this->adminActivityLogger
            ->expects($this->once())
            ->method('log')
            ->with($this->callback(function (AdminActivity $activity): bool {
                $this->assertSame(AdminActivityType::CREATE, $activity->getType());
                $this->assertSame('Product', $activity->getObjectType());
                $this->assertSame(42, $activity->getObjectId());
                $this->assertSame(42, $activity->getLogObjectId());
                $this->assertNull($activity->getNewObjectId());
                $this->assertFalse($activity->isBulk());

                return true;
            }));

        $result = $this->createHandler('Product')->handle($form);

        $this->assertSame(42, $result->getIdentifiableObjectId());
    }

    public function testUpdateLogsAdminActivityWithOriginalObjectId(): void
    {
        $form = $this->createValidForm();

        $this->dataHandler
            ->expects($this->once())
            ->method('update')
            ->with(42, ['name' => 'Test'])
            ->willReturn(84);

        $this->adminActivityLogger
            ->expects($this->once())
            ->method('log')
            ->with($this->callback(function (AdminActivity $activity): bool {
                $this->assertSame(AdminActivityType::UPDATE, $activity->getType());
                $this->assertSame('Product', $activity->getObjectType());
                $this->assertSame(42, $activity->getObjectId());
                $this->assertSame(42, $activity->getLogObjectId());
                $this->assertNull($activity->getNewObjectId());
                $this->assertFalse($activity->isBulk());

                return true;
            }));

        $result = $this->createHandler('Product')->handleFor(42, $form);

        $this->assertSame(84, $result->getIdentifiableObjectId());
    }

    public function testCreateDoesNotLogWhenObjectTypeIsNotConfigured(): void
    {
        $form = $this->createValidForm();

        $this->dataHandler
            ->expects($this->once())
            ->method('create')
            ->willReturn(42);

        $this->adminActivityLogger
            ->expects($this->never())
            ->method('log');

        $this->createHandler()->handle($form);
    }

    public function testUpdateDoesNotLogWhenObjectTypeIsNotConfigured(): void
    {
        $form = $this->createValidForm();

        $this->dataHandler
            ->expects($this->once())
            ->method('update')
            ->with(42, ['name' => 'Test'])
            ->willReturn(null);

        $this->adminActivityLogger
            ->expects($this->never())
            ->method('log');

        $this->createHandler()->handleFor(42, $form);
    }

    public function testCreateDoesNotLogWhenFinalHookFails(): void
    {
        $form = $this->createValidForm();

        $this->dataHandler
            ->expects($this->once())
            ->method('create')
            ->willReturn(42);

        $this->hookDispatcher
            ->method('dispatchWithParameters')
            ->willReturnCallback(function (string $hookName): void {
                if ('actionAfterCreateProductFormHandler' === $hookName) {
                    throw new RuntimeException('After create hook failed');
                }
            });

        $this->adminActivityLogger
            ->expects($this->never())
            ->method('log');

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('After create hook failed');

        $this->createHandler('Product')->handle($form);
    }

    public function testUpdateDoesNotLogWhenFinalHookFails(): void
    {
        $form = $this->createValidForm();

        $this->dataHandler
            ->expects($this->once())
            ->method('update')
            ->with(42, ['name' => 'Test'])
            ->willReturn(null);

        $this->hookDispatcher
            ->method('dispatchWithParameters')
            ->willReturnCallback(function (string $hookName): void {
                if ('actionAfterUpdateProductFormHandler' === $hookName) {
                    throw new RuntimeException('After update hook failed');
                }
            });

        $this->adminActivityLogger
            ->expects($this->never())
            ->method('log');

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('After update hook failed');

        $this->createHandler('Product')->handleFor(42, $form);
    }

    private function createHandler(?string $activityLogObjectType = null): FormHandler
    {
        return new FormHandler(
            $this->dataHandler,
            $this->hookDispatcher,
            $this->translator,
            false,
            $this->extraPropertiesFormDataPersister,
            $this->adminActivityLogger,
            $activityLogObjectType
        );
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
