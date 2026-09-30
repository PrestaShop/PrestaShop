<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\PrestaShopBundle\Service\Log;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\ActivityLog\AdminActivity;
use PrestaShop\PrestaShop\Core\ActivityLog\AdminActivityType;
use PrestaShopBundle\Service\Log\AdminActivityLogger;
use PrestaShopBundle\Service\Log\AdminActivityScope;
use Psr\Log\LoggerInterface;
use RuntimeException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Contracts\Translation\TranslatorInterface;

final class AdminActivityLoggerTest extends TestCase
{
    #[DataProvider('getLoggableActivities')]
    public function testLogWritesExpectedMessageAndContext(
        AdminActivity $activity,
        string $translationId,
        string $expectedMessage,
        array $expectedContext
    ): void {
        $logger = $this->createMock(LoggerInterface::class);
        $translator = $this->createMock(TranslatorInterface::class);

        $translator
            ->expects(self::once())
            ->method('trans')
            ->with($translationId, [], 'Admin.Advparameters.Feature')
            ->willReturn($translationId)
        ;

        $logger
            ->expects(self::once())
            ->method('info')
            ->with($expectedMessage, $expectedContext)
        ;

        $activityLogger = new AdminActivityLogger(
            $logger,
            $this->createEnabledScope(),
            $translator
        );

        $activityLogger->log($activity);
    }

    public function testLogDoesNothingWhenScopeIsDisabled(): void
    {
        $logger = $this->createMock(LoggerInterface::class);
        $translator = $this->createMock(TranslatorInterface::class);

        $logger->expects(self::never())->method('info');
        $translator->expects(self::never())->method('trans');

        $activityLogger = new AdminActivityLogger(
            $logger,
            new AdminActivityScope(new RequestStack()),
            $translator
        );

        $activityLogger->log(
            new AdminActivity(
                AdminActivityType::CREATE,
                'Product',
                42,
                42
            )
        );
    }

    #[DataProvider('getIncompleteActivities')]
    public function testLogDoesNothingWhenActivityCannotBuildMessage(AdminActivity $activity): void
    {
        $logger = $this->createMock(LoggerInterface::class);
        $translator = $this->createMock(TranslatorInterface::class);

        $logger->expects(self::never())->method('info');
        $translator->expects(self::never())->method('trans');

        $activityLogger = new AdminActivityLogger(
            $logger,
            $this->createEnabledScope(),
            $translator
        );

        $activityLogger->log($activity);
    }

    public function testScopeFailureDoesNotPropagate(): void
    {
        $logger = $this->createMock(LoggerInterface::class);
        $translator = $this->createMock(TranslatorInterface::class);
        $requestStack = $this->createMock(RequestStack::class);

        $requestStack
            ->expects(self::once())
            ->method('getMainRequest')
            ->willThrowException(new RuntimeException('Scope failure'))
        ;
        $logger->expects(self::never())->method('info');
        $translator->expects(self::never())->method('trans');

        $activityLogger = new AdminActivityLogger(
            $logger,
            new AdminActivityScope($requestStack),
            $translator
        );

        $activityLogger->log(
            new AdminActivity(
                AdminActivityType::CREATE,
                'Product',
                42,
                42
            )
        );
    }

    public function testTranslationFailureDoesNotPropagate(): void
    {
        $logger = $this->createMock(LoggerInterface::class);
        $translator = $this->createMock(TranslatorInterface::class);

        $translator
            ->expects(self::once())
            ->method('trans')
            ->willThrowException(new RuntimeException('Translation failure'))
        ;
        $logger->expects(self::never())->method('info');

        $activityLogger = new AdminActivityLogger(
            $logger,
            $this->createEnabledScope(),
            $translator
        );

        $activityLogger->log(
            new AdminActivity(
                AdminActivityType::CREATE,
                'Product',
                42,
                42
            )
        );
    }

    public function testLoggerFailureDoesNotPropagate(): void
    {
        $logger = $this->createMock(LoggerInterface::class);
        $translator = $this->createMock(TranslatorInterface::class);

        $translator
            ->expects(self::once())
            ->method('trans')
            ->willReturn('%s addition')
        ;
        $logger
            ->expects(self::once())
            ->method('info')
            ->willThrowException(new RuntimeException('Logger failure'))
        ;

        $activityLogger = new AdminActivityLogger(
            $logger,
            $this->createEnabledScope(),
            $translator
        );

        $activityLogger->log(
            new AdminActivity(
                AdminActivityType::CREATE,
                'Product',
                42,
                42
            )
        );
    }

    public static function getLoggableActivities(): iterable
    {
        yield 'create' => [
            new AdminActivity(AdminActivityType::CREATE, 'Product', 42, 42),
            '%s addition',
            'Product addition',
            [
                'object_type' => 'Product',
                'object_id' => 42,
                'allow_duplicate' => true,
            ],
        ];

        yield 'update' => [
            new AdminActivity(AdminActivityType::UPDATE, 'Product', 42, 42),
            '%s modification',
            'Product modification',
            [
                'object_type' => 'Product',
                'object_id' => 42,
                'allow_duplicate' => true,
            ],
        ];

        yield 'delete' => [
            new AdminActivity(AdminActivityType::DELETE, 'Product', 42, 42),
            '%s deletion',
            'Product deletion',
            [
                'object_type' => 'Product',
                'object_id' => 42,
                'allow_duplicate' => true,
            ],
        ];

        yield 'activate' => [
            new AdminActivity(AdminActivityType::ACTIVATE, 'Product', 42, 42),
            '%s activated: %d',
            'Product activated: 42',
            [
                'object_type' => 'Product',
                'object_id' => 42,
                'allow_duplicate' => true,
            ],
        ];

        yield 'deactivate' => [
            new AdminActivity(AdminActivityType::DEACTIVATE, 'Product', 42, 42),
            '%s deactivated: %d',
            'Product deactivated: 42',
            [
                'object_type' => 'Product',
                'object_id' => 42,
                'allow_duplicate' => true,
            ],
        ];

        yield 'duplicate' => [
            new AdminActivity(AdminActivityType::DUPLICATE, 'Product', 42, 0, 84),
            '%s duplicated: (from %d to %d).',
            'Product duplicated: (from 42 to 84).',
            [
                'object_type' => 'Product',
                'object_id' => 0,
                'allow_duplicate' => true,
            ],
        ];
    }

    public static function getIncompleteActivities(): iterable
    {
        yield 'activate without object id' => [
            new AdminActivity(AdminActivityType::ACTIVATE, 'Product', null, null),
        ];

        yield 'deactivate without object id' => [
            new AdminActivity(AdminActivityType::DEACTIVATE, 'Product', null, null),
        ];

        yield 'duplicate without source id' => [
            new AdminActivity(AdminActivityType::DUPLICATE, 'Product', null, 0, 84),
        ];

        yield 'duplicate without new id' => [
            new AdminActivity(AdminActivityType::DUPLICATE, 'Product', 42, 0),
        ];
    }

    private function createEnabledScope(): AdminActivityScope
    {
        $request = new Request();
        $request->attributes->set(AdminActivityScope::REQUEST_ATTRIBUTE, true);

        $requestStack = new RequestStack();
        $requestStack->push($request);

        return new AdminActivityScope($requestStack);
    }
}
