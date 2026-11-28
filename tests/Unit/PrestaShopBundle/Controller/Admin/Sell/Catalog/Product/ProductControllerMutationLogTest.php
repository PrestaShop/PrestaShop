<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace Tests\Unit\PrestaShopBundle\Controller\Admin\Sell\Catalog\Product;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;
use PrestaShopBundle\Controller\Admin\Sell\Catalog\Product\ProductController;
use PrestaShopBundle\Entity\MutationAction;
use ReflectionMethod;

class ProductControllerMutationLogTest extends TestCase
{
    #[DataProvider('mutations')]
    public function testRecordsExpectedMutationsOnce(string $method, array $arguments, array $expected): void
    {
        $controller = new TestableProductController();
        $reflection = new ReflectionMethod(ProductController::class, $method);
        $reflection->invokeArgs($controller, $arguments);
        $this->assertSame($expected, $controller->getLoggedMutations());
    }

    public static function mutations(): iterable
    {
        yield 'bulk delete' => ['recordBulkDeleteMutations', [[42, 84]], [
            ['product', 42, MutationAction::DELETE, ['bulk' => true]],
            ['product', 84, MutationAction::DELETE, ['bulk' => true]],
        ]];
        yield 'bulk duplicate' => ['recordBulkDuplicateMutations', [[42 => new ProductId(142), 84 => new ProductId(184)]], [
            ['product', 142, MutationAction::CREATE, ['source_id' => 42, 'bulk' => true]],
            ['product', 184, MutationAction::CREATE, ['source_id' => 84, 'bulk' => true]],
        ]];
        foreach ([true, false] as $active) {
            yield ['recordProductStatusMutation', [42, $active], [
                ['product', 42, MutationAction::UPDATE, ['active' => $active]],
            ]];
            yield ['recordBulkProductStatusMutations', [[42, 84], $active], [
                ['product', 42, MutationAction::UPDATE, ['active' => $active, 'bulk' => true]],
                ['product', 84, MutationAction::UPDATE, ['active' => $active, 'bulk' => true]],
            ]];
        }
    }
}

class TestableProductController extends ProductController
{
    private array $loggedMutations = [];

    protected function recordMutation(string $table, int $id, MutationAction $action, array $details = []): void
    {
        $this->loggedMutations[] = [$table, $id, $action, $details];
    }

    public function getLoggedMutations(): array
    {
        return $this->loggedMutations;
    }
}
