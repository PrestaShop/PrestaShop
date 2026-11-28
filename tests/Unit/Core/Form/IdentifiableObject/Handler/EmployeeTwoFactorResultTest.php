<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Core\Form\IdentifiableObject\Handler;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Domain\Employee\ValueObject\GeneratedEmployeeBackupCodes;
use PrestaShop\PrestaShop\Core\ExtraProperty\Form\ExtraPropertiesFormDataPersister;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\DataHandler\FormDataHandlerInterface;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\Handler\FormHandler;
use PrestaShop\PrestaShop\Core\Hook\HookDispatcherInterface;
use RuntimeException;
use Symfony\Component\Form\FormConfigInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\ResolvedFormTypeInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class EmployeeTwoFactorResultTest extends TestCase
{
    #[DataProvider('failures')]
    public function testDtoPreservesHooksAndExtraPropertiesAndIsNotReturnedOnFailure(string $failure): void
    {
        $dto = new GeneratedEmployeeBackupCodes(42, ['private-new-code']);
        $dataHandler = $this->createMock(FormDataHandlerInterface::class);
        $dataHandler->expects(self::once())->method('update')->with(42, ['lastname' => 'Lovelace'])->willReturn($dto);
        $type = $this->createMock(ResolvedFormTypeInterface::class);
        $type->method('getBlockPrefix')->willReturn('employee');
        $config = $this->createMock(FormConfigInterface::class);
        $config->method('getType')->willReturn($type);
        $form = $this->createMock(FormInterface::class);
        $form->method('isSubmitted')->willReturn(true);
        $form->method('isValid')->willReturn(true);
        $form->method('getName')->willReturn('employee');
        $form->method('getConfig')->willReturn($config);
        $form->method('getData')->willReturn(['lastname' => 'Lovelace']);
        $events = [];
        $hooks = $this->createMock(HookDispatcherInterface::class);
        $hooks->method('dispatchWithParameters')->willReturnCallback(static function (string $name, array $parameters) use (&$events, $failure): void {
            self::assertSame(42, $parameters['id']);
            self::assertSame(['lastname' => 'Lovelace'], $parameters['form_data']);
            $events[] = $name;
            if ('hook' === $failure && str_starts_with($name, 'actionAfter')) {
                throw new RuntimeException('After-update hook failed');
            }
        });
        $persister = $this->createMock(ExtraPropertiesFormDataPersister::class);
        $persister->expects(self::once())->method('persist')->with($form, 'employee', 42)
            ->willReturnCallback(static function () use (&$events, $failure): void {
                $events[] = 'extra properties';
                if ('extra properties' === $failure) {
                    throw new RuntimeException('Extra-property write failed');
                }
            });
        $handler = new FormHandler($dataHandler, $hooks, $this->createMock(TranslatorInterface::class), false, $persister);
        if ('' !== $failure) {
            $this->expectException(RuntimeException::class);
        }
        $result = $handler->handleFor(42, $form);
        self::assertSame($dto, $result->getIdentifiableObjectId());
        self::assertSame(['actionBeforeUpdateEmployeeFormHandler', 'extra properties', 'actionAfterUpdateEmployeeFormHandler'], $events);
    }

    public static function failures(): iterable
    {
        yield 'success' => [''];
        yield 'extra-property failure' => ['extra properties'];
        yield 'after-update hook failure' => ['hook'];
    }
}
