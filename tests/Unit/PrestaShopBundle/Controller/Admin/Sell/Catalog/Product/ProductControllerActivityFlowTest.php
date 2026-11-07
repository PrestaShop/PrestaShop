<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace Tests\Unit\PrestaShopBundle\Controller\Admin\Sell\Catalog\Product;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\ActivityLog\AdminActivity;
use PrestaShop\PrestaShop\Core\ActivityLog\AdminActivityType;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\BulkProductException;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use PrestaShopBundle\Controller\Admin\Sell\Catalog\Product\ProductController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Throwable;

class ProductControllerActivityFlowTest extends TestCase
{
    #[DataProvider('getDeleteActions')]
    public function testDeleteLogsOnlyAfterSuccessfulCommand(string $method, array $arguments): void
    {
        $controller = new TestableProductActivityFlowController();
        $controller->setCommandResult(null);

        $controller->{$method}(...$arguments);

        $this->assertSame(['dispatch', 'log'], array_slice($controller->getEvents(), 0, 2));

        $activities = $controller->getLoggedActivities();
        $this->assertCount(1, $activities);
        $this->assertActivity(
            $activities[0],
            AdminActivityType::DELETE,
            42,
            42,
            null
        );
    }

    #[DataProvider('getDeleteActions')]
    public function testDeleteDoesNotLogWhenCommandFails(string $method, array $arguments): void
    {
        $controller = new TestableProductActivityFlowController();
        $controller->setCommandException(new BulkProductException('Delete failed'));

        $controller->{$method}(...$arguments);

        $this->assertSame(['dispatch'], array_slice($controller->getEvents(), 0, 1));
        $this->assertSame([], $controller->getLoggedActivities());
    }

    #[DataProvider('getDuplicateActions')]
    public function testDuplicateLogsSourceAndNewProductIdsAfterSuccessfulCommand(string $method, array $arguments): void
    {
        $controller = new TestableProductActivityFlowController();
        $controller->setCommandResult(new ProductId(142));

        $controller->{$method}(...$arguments);

        $this->assertSame(['dispatch', 'log'], array_slice($controller->getEvents(), 0, 2));

        $activities = $controller->getLoggedActivities();
        $this->assertCount(1, $activities);
        $this->assertActivity(
            $activities[0],
            AdminActivityType::DUPLICATE,
            42,
            0,
            142
        );
        $this->assertSame('admin_products_edit', $controller->getLastRedirectRoute());
        $this->assertSame(['productId' => 142], $controller->getLastRedirectParameters());
    }

    #[DataProvider('getDuplicateActions')]
    public function testDuplicateDoesNotLogWhenCommandFails(string $method, array $arguments): void
    {
        $controller = new TestableProductActivityFlowController();
        $controller->setCommandException(new BulkProductException('Duplicate failed'));

        $controller->{$method}(...$arguments);

        $this->assertSame(['dispatch'], array_slice($controller->getEvents(), 0, 1));
        $this->assertSame([], $controller->getLoggedActivities());
        $this->assertSame('admin_products_index', $controller->getLastRedirectRoute());
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

    private function assertActivity(
        AdminActivity $activity,
        AdminActivityType $type,
        int $objectId,
        int $logObjectId,
        ?int $newObjectId
    ): void {
        $this->assertSame($type, $activity->getType());
        $this->assertSame('Product', $activity->getObjectType());
        $this->assertSame($objectId, $activity->getObjectId());
        $this->assertSame($logObjectId, $activity->getLogObjectId());
        $this->assertSame($newObjectId, $activity->getNewObjectId());
        $this->assertFalse($activity->isBulk());
    }
}

class TestableProductActivityFlowController extends ProductController
{
    /**
     * @var AdminActivity[]
     */
    private array $loggedActivities = [];

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

    protected function logAdminActivity(AdminActivity $activity): void
    {
        $this->events[] = 'log';
        $this->loggedActivities[] = $activity;
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
     * @return AdminActivity[]
     */
    public function getLoggedActivities(): array
    {
        return $this->loggedActivities;
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
