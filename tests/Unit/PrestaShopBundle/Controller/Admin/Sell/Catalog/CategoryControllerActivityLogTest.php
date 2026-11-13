<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace Tests\Unit\PrestaShopBundle\Controller\Admin\Sell\Catalog;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\ActivityLog\AdminActivity;
use PrestaShop\PrestaShop\Core\ActivityLog\AdminActivityType;
use PrestaShop\PrestaShop\Core\Domain\Category\Exception\CannotDeleteRootCategoryForShopException;
use PrestaShop\PrestaShop\Core\Domain\Category\Exception\CategoryException;
use PrestaShop\PrestaShop\Core\Domain\Category\Exception\CategoryNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Category\Exception\FailedToDeleteCategoryException;
use PrestaShop\PrestaShop\Core\Domain\Category\ValueObject\CategoryId;
use PrestaShopBundle\Controller\Admin\Sell\Catalog\CategoryController;
use ReflectionMethod;

class CategoryControllerActivityLogTest extends TestCase
{
    public function testBulkDeleteBuildsOneActivityPerCategory(): void
    {
        $controller = new TestableCategoryController();

        $this->invokePrivateMethod(
            $controller,
            'logBulkCategoryDeleteActivities',
            [[10, 20]]
        );

        $activities = $controller->getLoggedActivities();

        $this->assertCount(2, $activities);
        $this->assertActivity($activities[0], 10);
        $this->assertActivity($activities[1], 20);
    }

    #[DataProvider('getCategoryFailures')]
    public function testSuccessfulCategoryIdsBeforeFailureArePreserved(
        CategoryException $exception
    ): void {
        $controller = new TestableCategoryController();

        $successfulIds = $this->invokePrivateMethod(
            $controller,
            'getSuccessfulCategoryIdsBeforeFailure',
            [[10, 20, 30, 40], $exception]
        );

        $this->assertSame([10, 20], $successfulIds);
    }

    public function testNoSuccessfulCategoryIdsAreInferredWhenFailureIdIsUnknown(): void
    {
        $controller = new TestableCategoryController();
        $exception = new class('Unknown category failure') extends CategoryException {
        };

        $successfulIds = $this->invokePrivateMethod(
            $controller,
            'getSuccessfulCategoryIdsBeforeFailure',
            [[10, 20, 30, 40], $exception]
        );

        $this->assertSame([], $successfulIds);
    }

    public function testNoSuccessfulCategoryIdsAreInferredWhenFailedIdIsNotSelected(): void
    {
        $controller = new TestableCategoryController();
        $exception = new CategoryNotFoundException(
            new CategoryId(99),
            'Category not found'
        );

        $successfulIds = $this->invokePrivateMethod(
            $controller,
            'getSuccessfulCategoryIdsBeforeFailure',
            [[10, 20, 30, 40], $exception]
        );

        $this->assertSame([], $successfulIds);
    }

    public static function getCategoryFailures(): iterable
    {
        $categoryId = new CategoryId(30);

        yield 'not found' => [
            new CategoryNotFoundException(
                $categoryId,
                'Category not found'
            ),
        ];

        yield 'root category' => [
            new CannotDeleteRootCategoryForShopException(
                'Cannot delete root category',
                0,
                null,
                $categoryId
            ),
        ];

        yield 'delete failed' => [
            new FailedToDeleteCategoryException(
                'Category delete failed',
                0,
                null,
                $categoryId
            ),
        ];
    }

    private function invokePrivateMethod(
        CategoryController $controller,
        string $methodName,
        array $arguments
    ): mixed {
        $method = new ReflectionMethod(CategoryController::class, $methodName);
        $method->setAccessible(true);

        return $method->invokeArgs($controller, $arguments);
    }

    private function assertActivity(AdminActivity $activity, int $categoryId): void
    {
        $this->assertSame(AdminActivityType::DELETE, $activity->getType());
        $this->assertSame('Category', $activity->getObjectType());
        $this->assertSame($categoryId, $activity->getObjectId());
        $this->assertSame($categoryId, $activity->getLogObjectId());
        $this->assertNull($activity->getNewObjectId());
        $this->assertTrue($activity->isBulk());
    }
}

class TestableCategoryController extends CategoryController
{
    /**
     * @var AdminActivity[]
     */
    private array $loggedActivities = [];

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
