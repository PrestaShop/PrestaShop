<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace Tests\Unit\PrestaShopBundle\Controller\Admin\Sell\Catalog\Product;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\BulkProductException;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\CannotDeleteProductException;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use PrestaShopBundle\Controller\Admin\Sell\Catalog\Product\ProductController;
use PrestaShopBundle\Entity\MutationAction;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Throwable;

class ProductControllerMutationFlowTest extends TestCase
{
    #[DataProvider('getDeleteActions')]
    public function testDeleteLogsOnlyAfterSuccessfulCommand(string $method, array $arguments): void
    {
        $controller = new TestableProductMutationFlowController();
        $controller->setCommandResult(null);

        $controller->{$method}(...$arguments);

        $this->assertSame(['dispatch', 'log'], array_slice($controller->getEvents(), 0, 2));

        $activities = $controller->getLoggedMutations();
        $this->assertCount(1, $activities);
        $this->assertSame(['product', 42, MutationAction::DELETE, []], $activities[0]);
    }

    #[DataProvider('getDeleteActions')]
    public function testDeleteDoesNotLogWhenCommandFails(string $method, array $arguments): void
    {
        $controller = new TestableProductMutationFlowController();
        $controller->setCommandException(new BulkProductException('Delete failed'));

        $controller->{$method}(...$arguments);

        $this->assertSame(['dispatch'], array_slice($controller->getEvents(), 0, 1));
        $this->assertSame([], $controller->getLoggedMutations());
    }

    #[DataProvider('getDuplicateActions')]
    public function testDuplicateLogsSourceAndNewProductIdsAfterSuccessfulCommand(string $method, array $arguments): void
    {
        $controller = new TestableProductMutationFlowController();
        $controller->setCommandResult(new ProductId(142));

        $controller->{$method}(...$arguments);

        $this->assertSame(['dispatch', 'log'], array_slice($controller->getEvents(), 0, 2));

        $activities = $controller->getLoggedMutations();
        $this->assertCount(1, $activities);
        $this->assertSame(['product', 142, MutationAction::CREATE, ['source_id' => 42]], $activities[0]);
        $this->assertSame('admin_products_edit', $controller->getLastRedirectRoute());
        $this->assertSame(['productId' => 142], $controller->getLastRedirectParameters());
    }

    #[DataProvider('getDuplicateActions')]
    public function testDuplicateDoesNotLogWhenCommandFails(string $method, array $arguments): void
    {
        $controller = new TestableProductMutationFlowController();
        $controller->setCommandException(new BulkProductException('Duplicate failed'));

        $controller->{$method}(...$arguments);

        $this->assertSame(['dispatch'], array_slice($controller->getEvents(), 0, 1));
        $this->assertSame([], $controller->getLoggedMutations());
        $this->assertSame('admin_products_index', $controller->getLastRedirectRoute());
    }

    #[DataProvider('bulkActions')]
    public function testBulkLogsOnlySuccessfulProducts(string $method, bool $duplicate, ?bool $active, bool $partial): void
    {
        $controller = new TestableProductMutationFlowController();
        if ($partial) {
            $exception = new BulkProductException('Partial failure');
            $exception->addException(new ProductId(84), new CannotDeleteProductException('Failed'));
            $exception->setSuccessfulResults([42 => new ProductId(142)]);
            $controller->setCommandException($exception);
        } else {
            $controller->setCommandResult($duplicate ? [42 => new ProductId(142), 84 => new ProductId(184)] : null);
        }
        $response = $controller->{$method}(new Request([], ['product_bulk' => [42, 84]]));
        $expected = [];
        foreach ($partial ? [42] : [42, 84] as $id) {
            $expected[] = $duplicate
                ? ['product', $id + 100, MutationAction::CREATE, ['source_id' => $id, 'bulk' => true]]
                : ['product', $id, null === $active ? MutationAction::DELETE : MutationAction::UPDATE,
                    null === $active ? ['bulk' => true] : ['active' => $active, 'bulk' => true]];
        }
        $this->assertSame($expected, $controller->getLoggedMutations());
        $this->assertSame('dispatch', $controller->getEvents()[0]);
        $this->assertSame($partial ? 400 : 200, $response->getStatusCode());
    }

    #[DataProvider('failedBulkActions')]
    public function testFullyFailedBulkDoesNotLog(string $method): void
    {
        $controller = new TestableProductMutationFlowController();
        $exception = new BulkProductException('All failed');
        foreach ([42, 84] as $id) {
            $exception->addException(new ProductId($id), new CannotDeleteProductException('Failed'));
        }
        $controller->setCommandException($exception);
        $response = $controller->{$method}(new Request([], ['product_bulk' => [42, 84]]));
        $this->assertSame([], $controller->getLoggedMutations());
        $this->assertSame(400, $response->getStatusCode());
    }

    public static function failedBulkActions(): iterable
    {
        foreach (['bulkDeleteFromAllShopsAction', 'bulkDuplicateAllShopsAction', 'bulkEnableAllShopsAction', 'bulkDisableAllShopsAction'] as $method) {
            yield [$method];
        }
    }

    public static function bulkActions(): iterable
    {
        foreach ([false, true] as $partial) {
            yield ['bulkDeleteFromAllShopsAction', false, null, $partial];
            yield ['bulkDuplicateAllShopsAction', true, null, $partial];
            yield ['bulkEnableAllShopsAction', false, true, $partial];
            yield ['bulkDisableAllShopsAction', false, false, $partial];
        }
    }

    #[DataProvider('statusActions')]
    public function testStatusLogsOnlyAfterSuccess(string $method, array $arguments, bool $active, bool $failure): void
    {
        $controller = new TestableProductMutationFlowController();
        if ($failure) {
            $controller->setCommandException(new BulkProductException('Status failed'));
        }
        $controller->{$method}(...$arguments);
        $this->assertSame($failure ? [] : [['product', 42, MutationAction::UPDATE, ['active' => $active]]], $controller->getLoggedMutations());
    }

    public static function statusActions(): iterable
    {
        foreach ([false, true] as $failure) {
            yield ['enableForAllShopsAction', [42], true, $failure];
            yield ['disableForAllShopsAction', [42], false, $failure];
            yield ['enableForShopGroupAction', [42, 3], true, $failure];
            yield ['disableForShopGroupAction', [42, 3], false, $failure];
        }
    }

    public static function getDeleteActions(): iterable
    {
        yield 'all shops' => [
            'deleteFromAllShopsAction',
            [42],
        ];

        yield 'single shop' => [
            'deleteFromShopAction',
            [42, 2],
        ];

        yield 'shop group' => [
            'deleteFromShopGroupAction',
            [42, 3],
        ];
    }

    public static function getDuplicateActions(): iterable
    {
        yield 'all shops' => [
            'duplicateAllShopsAction',
            [42],
        ];

        yield 'single shop' => [
            'duplicateShopAction',
            [42, 2],
        ];

        yield 'shop group' => [
            'duplicateShopGroupAction',
            [42, 3],
        ];
    }
}

class TestableProductMutationFlowController extends ProductController
{
    /**
     * @var array[]
     */
    private array $loggedMutations = [];

    /**
     * @var string[]
     */
    private array $events = [];

    private mixed $commandResult = null;
    private ?Throwable $commandException = null;
    private ?string $lastRedirectRoute = null;

    /**
     * @var array<string, mixed>
     */
    private array $lastRedirectParameters = [];

    public function setCommandResult(mixed $commandResult): void
    {
        $this->commandResult = $commandResult;
        $this->commandException = null;
    }

    public function setCommandException(Throwable $commandException): void
    {
        $this->commandException = $commandException;
    }

    protected function dispatchCommand(mixed $command): mixed
    {
        $this->events[] = 'dispatch';

        if (null !== $this->commandException) {
            throw $this->commandException;
        }

        return $this->commandResult;
    }

    protected function recordMutation(string $table, int $id, MutationAction $action, array $details = []): void
    {
        $this->events[] = 'log';
        $this->loggedMutations[] = [$table, $id, $action, $details];
    }

    protected function json(mixed $data, int $status = 200, array $headers = [], array $context = []): JsonResponse
    {
        return new JsonResponse($data, $status, $headers);
    }

    protected function hasAuthorizationByShopConstraint(ShopConstraint $shopConstraint): bool
    {
        return true;
    }

    protected function trans(string $id, array $parameters = [], ?string $domain = null, ?string $locale = null): string
    {
        return $id;
    }

    protected function getErrorMessageForException(Throwable $e, array $messages = []): string
    {
        return $e->getMessage();
    }

    protected function addFlash(string $type, mixed $message): void
    {
    }

    protected function redirectToRoute(string $route, array $parameters = [], int $status = 302): RedirectResponse
    {
        $this->lastRedirectRoute = $route;
        $this->lastRedirectParameters = $parameters;

        return new RedirectResponse('/' . $route, $status);
    }

    /**
     * @return array[]
     */
    public function getLoggedMutations(): array
    {
        return $this->loggedMutations;
    }

    /**
     * @return string[]
     */
    public function getEvents(): array
    {
        return $this->events;
    }

    public function getLastRedirectRoute(): ?string
    {
        return $this->lastRedirectRoute;
    }

    /**
     * @return array<string, mixed>
     */
    public function getLastRedirectParameters(): array
    {
        return $this->lastRedirectParameters;
    }
}
