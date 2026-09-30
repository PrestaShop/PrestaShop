<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace Tests\Unit\Adapter\Product\CommandHandler;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Adapter\Product\CommandHandler\AbstractBulkHandler;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\BulkProductException;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\ProductException;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;

class AbstractBulkHandlerTest extends TestCase
{
    public function testSuccessfulResultsAreReturned(): void
    {
        $firstResult = new ProductId(101);
        $secondResult = new ProductId(102);

        $handler = $this->createHandler(
            [
                1 => $firstResult,
                2 => $secondResult,
            ]
        );

        $result = $handler->execute([
            new ProductId(1),
            new ProductId(2),
        ]);

        $this->assertSame([
            1 => $firstResult,
            2 => $secondResult,
        ], $result);
    }

    public function testSuccessfulResultsArePreservedWhenBulkPartiallyFails(): void
    {
        $firstResult = new ProductId(101);
        $thirdResult = new ProductId(103);
        $failure = new class('Product 2 failed') extends ProductException {
        };

        $handler = $this->createHandler(
            [
                1 => $firstResult,
                3 => $thirdResult,
            ],
            [
                2 => $failure,
            ]
        );

        try {
            $handler->execute([
                new ProductId(1),
                new ProductId(2),
                new ProductId(3),
            ]);

            $this->fail('A BulkProductException was expected.');
        } catch (BulkProductException $e) {
            $this->assertSame([
                1 => $firstResult,
                3 => $thirdResult,
            ], $e->getSuccessfulResults());

            $this->assertSame(
                [2 => $failure],
                $e->getBulkExceptions()
            );
        }
    }

    /**
     * @param array<int, mixed> $results
     * @param array<int, ProductException> $failures
     */
    private function createHandler(array $results, array $failures = []): TestableBulkHandler
    {
        return new TestableBulkHandler($results, $failures);
    }
}

final class TestableBulkHandler extends AbstractBulkHandler
{
    /**
     * @param array<int, mixed> $results
     * @param array<int, ProductException> $failures
     */
    public function __construct(
        private array $results,
        private array $failures
    ) {
    }

    /**
     * @param ProductId[] $productIds
     *
     * @return array<int, mixed>
     */
    public function execute(array $productIds): array
    {
        return $this->handleBulkAction($productIds);
    }

    protected function handleSingleAction(ProductId $productId, $command = null)
    {
        $id = $productId->getValue();

        if (isset($this->failures[$id])) {
            throw $this->failures[$id];
        }

        return $this->results[$id] ?? null;
    }
}
