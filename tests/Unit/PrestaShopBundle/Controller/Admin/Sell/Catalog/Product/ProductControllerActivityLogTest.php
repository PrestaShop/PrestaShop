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
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;
use PrestaShopBundle\Controller\Admin\Sell\Catalog\Product\ProductController;
use ReflectionMethod;

class ProductControllerActivityLogTest extends TestCase
{
    public function testBulkDeleteBuildsOneDeleteActivityPerProduct(): void
    {
        $controller = new TestableProductController();

        $this->invokePrivateMethod(
            $controller,
            'logBulkDeleteActivities',
            [[42, 84]]
        );

        $activities = $controller->getLoggedActivities();

        $this->assertCount(2, $activities);
        $this->assertActivity(
            $activities[0],
            AdminActivityType::DELETE,
            42,
            42,
            null,
            true
        );
        $this->assertActivity(
            $activities[1],
            AdminActivityType::DELETE,
            84,
            84,
            null,
            true
        );
    }

    public function testBulkDuplicatePreservesSourceAndNewProductIds(): void
    {
        $controller = new TestableProductController();

        $this->invokePrivateMethod(
            $controller,
            'logBulkDuplicateActivities',
            [[
                42 => new ProductId(142),
                84 => new ProductId(184),
            ]]
        );

        $activities = $controller->getLoggedActivities();

        $this->assertCount(2, $activities);
        $this->assertActivity(
            $activities[0],
            AdminActivityType::DUPLICATE,
            42,
            0,
            142,
            true
        );
        $this->assertActivity(
            $activities[1],
            AdminActivityType::DUPLICATE,
            84,
            0,
            184,
            true
        );
    }

    #[DataProvider('getStatusActivities')]
    public function testProductStatusBuildsExpectedActivity(
        bool $isEnabled,
        AdminActivityType $expectedType
    ): void {
        $controller = new TestableProductController();

        $this->invokePrivateMethod(
            $controller,
            'logProductStatusActivity',
            [42, $isEnabled]
        );

        $activities = $controller->getLoggedActivities();

        $this->assertCount(1, $activities);
        $this->assertActivity(
            $activities[0],
            $expectedType,
            42,
            42,
            null,
            false
        );
    }

    #[DataProvider('getStatusActivities')]
    public function testBulkProductStatusBuildsOneActivityPerProduct(
        bool $isEnabled,
        AdminActivityType $expectedType
    ): void {
        $controller = new TestableProductController();

        $this->invokePrivateMethod(
            $controller,
            'logBulkProductStatusActivities',
            [[42, 84], $isEnabled]
        );

        $activities = $controller->getLoggedActivities();

        $this->assertCount(2, $activities);
        $this->assertActivity(
            $activities[0],
            $expectedType,
            42,
            42,
            null,
            true
        );
        $this->assertActivity(
            $activities[1],
            $expectedType,
            84,
            84,
            null,
            true
        );
    }

    public static function getStatusActivities(): iterable
    {
        yield 'activate' => [
            true,
            AdminActivityType::ACTIVATE,
        ];

        yield 'deactivate' => [
            false,
            AdminActivityType::DEACTIVATE,
        ];
    }

    private function invokePrivateMethod(
        ProductController $controller,
        string $methodName,
        array $arguments
    ): void {
        $method = new ReflectionMethod(ProductController::class, $methodName);
        $method->setAccessible(true);
        $method->invokeArgs($controller, $arguments);
    }

    private function assertActivity(
        AdminActivity $activity,
        AdminActivityType $type,
        int $objectId,
        int $logObjectId,
        ?int $newObjectId,
        bool $bulk
    ): void {
        $this->assertSame($type, $activity->getType());
        $this->assertSame('Product', $activity->getObjectType());
        $this->assertSame($objectId, $activity->getObjectId());
        $this->assertSame($logObjectId, $activity->getLogObjectId());
        $this->assertSame($newObjectId, $activity->getNewObjectId());
        $this->assertSame($bulk, $activity->isBulk());
    }
}

class TestableProductController extends ProductController
{
    /**
     * @var AdminActivity[]
     */
    private array $loggedActivities = [];

    protected function logAdminActivity(AdminActivity $activity): void
    {
        $this->loggedActivities[] = $activity;
    }

    /**
     * @param AdminActivity[] $activities
     */
    protected function logAdminActivities(array $activities): void
    {
        foreach ($activities as $activity) {
            $this->loggedActivities[] = $activity;
        }
    }

    /**
     * @return AdminActivity[]
     */
    public function getLoggedActivities(): array
    {
        return $this->loggedActivities;
    }
}
