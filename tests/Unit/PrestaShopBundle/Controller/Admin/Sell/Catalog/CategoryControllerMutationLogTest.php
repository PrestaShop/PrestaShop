<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace Tests\Unit\PrestaShopBundle\Controller\Admin\Sell\Catalog;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\ConfigurationInterface;
use PrestaShop\PrestaShop\Core\Domain\Category\Exception\CannotDeleteRootCategoryForShopException;
use PrestaShop\PrestaShop\Core\Domain\Category\Exception\CategoryException;
use PrestaShop\PrestaShop\Core\Domain\Category\Exception\CategoryNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Category\Exception\FailedToDeleteCategoryException;
use PrestaShop\PrestaShop\Core\Domain\Category\ValueObject\CategoryId;
use PrestaShopBundle\Controller\Admin\Sell\Catalog\CategoryController;
use PrestaShopBundle\Entity\MutationAction;
use ReflectionMethod;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

class CategoryControllerMutationLogTest extends TestCase
{
    public function testBulkDeleteBuildsOneActivityPerCategory(): void
    {
        $controller = new TestableCategoryController();

        $this->invokePrivateMethod(
            $controller,
            'recordBulkCategoryDeleteMutations',
            [[10, 20]]
        );

        $activities = $controller->getLoggedMutations();

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

    #[DataProvider('deleteFlows')]
    public function testDeleteFlowRecordsOnlySuccessfulCategories(bool $bulk, ?int $failedId, array $successfulIds): void
    {
        $form = $this->createMock(FormInterface::class);
        $form->method('handleRequest')->willReturnSelf();
        $form->method('isSubmitted')->willReturn(true);
        $form->method('getData')->willReturn([
            'categories_to_delete_parent' => 2,
            'categories_to_delete' => $bulk ? [10, 20, 30, 40] : [10],
            'delete_mode' => 'associate_only',
        ]);
        $configuration = $this->createMock(ConfigurationInterface::class);
        $configuration->method('get')->willReturn(2);
        $controller = $this->getMockBuilder(CategoryController::class)->onlyMethods([
            'createForm', 'getConfiguration', 'dispatchCommand', 'recordMutation',
            'trans', 'addFlash', 'redirectToRoute', 'getErrorMessageForException',
        ])->getMock();
        $controller->method('createForm')->willReturn($form);
        $controller->method('getConfiguration')->willReturn($configuration);
        $controller->method('trans')->willReturnArgument(0);
        $controller->method('redirectToRoute')->willReturn(new RedirectResponse('/categories'));
        $controller->method('getErrorMessageForException')->willReturn('Delete failed');
        if (null !== $failedId) {
            $exception = 0 === $failedId
                ? new class('Unknown failure') extends CategoryException {}
            : new CategoryNotFoundException(new CategoryId($failedId), 'Not found');
            $controller->expects($this->once())->method('dispatchCommand')->willThrowException($exception);
        } else {
            $controller->expects($this->once())->method('dispatchCommand');
        }
        $recorded = [];
        $controller->expects($this->exactly(count($successfulIds)))->method('recordMutation')
            ->willReturnCallback(static function (...$arguments) use (&$recorded): void { $recorded[] = $arguments; });
        $controller->{$bulk ? 'bulkDeleteAction' : 'deleteAction'}(new Request());
        $expected = [];
        foreach ($successfulIds as $id) {
            $expected[] = $bulk
                ? ['category', $id, MutationAction::DELETE, ['bulk' => true]]
                : ['category', $id, MutationAction::DELETE, []];
        }
        $this->assertSame($expected, $recorded);
    }

    public static function deleteFlows(): iterable
    {
        yield 'single success' => [false, null, [10]];
        yield 'single failure' => [false, 10, []];
        yield 'bulk success' => [true, null, [10, 20, 30, 40]];
        yield 'bulk partial success' => [true, 30, [10, 20]];
        yield 'bulk first failure' => [true, 10, []];
        yield 'bulk unknown failure' => [true, 0, []];
    }

    private function invokePrivateMethod(
        CategoryController $controller,
        string $methodName,
        array $arguments
    ): mixed {
        $method = new ReflectionMethod(CategoryController::class, $methodName);

        return $method->invokeArgs($controller, $arguments);
    }

    private function assertActivity(array $mutation, int $categoryId): void
    {
        $this->assertSame(['category', $categoryId, MutationAction::DELETE, ['bulk' => true]], $mutation);
    }
}

class TestableCategoryController extends CategoryController
{
    /**
     * @var array[]
     */
    private array $loggedMutations = [];

    protected function recordMutation(string $table, int $id, MutationAction $action, array $details = []): void
    {
        $this->loggedMutations[] = [$table, $id, $action, $details];
    }

    /**
     * @return array[]
     */
    public function getLoggedMutations(): array
    {
        return $this->loggedMutations;
    }
}
