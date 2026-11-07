<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Tests\Integration\Adapter\Order\QueryHandler;

use Context;
use Currency;
use Employee;
use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\Domain\Order\Query\GetOrderProductsForViewing;
use PrestaShop\PrestaShop\Core\Domain\Order\QueryResult\OrderProductForViewing;
use PrestaShop\PrestaShop\Core\Domain\Order\QueryResult\OrderProductsForViewing;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class GetOrderProductsForViewingHandlerTest extends KernelTestCase
{
    /**
     * Order from the test fixtures with two lines
     */
    private const ORDER_ID = 1;

    protected function setUp(): void
    {
        self::bootKernel();
        $context = Context::getContext();
        $context->currency = new Currency(1);
        $context->employee = new Employee(1);
    }

    public function testPaginatedReturnsTheRequestedPageOfProducts(): void
    {
        $allProducts = $this->getProducts(GetOrderProductsForViewing::all(self::ORDER_ID));
        self::assertCount(2, $allProducts);

        $firstPage = $this->getProducts(GetOrderProductsForViewing::paginated(self::ORDER_ID, 0, 1));
        $secondPage = $this->getProducts(GetOrderProductsForViewing::paginated(self::ORDER_ID, 1, 1));

        self::assertCount(1, $firstPage);
        self::assertCount(1, $secondPage);
        self::assertContainsOnlyInstancesOf(OrderProductForViewing::class, array_merge($firstPage, $secondPage));
        self::assertSame($allProducts[0]->getOrderDetailId(), $firstPage[0]->getOrderDetailId());
        self::assertSame($allProducts[1]->getOrderDetailId(), $secondPage[0]->getOrderDetailId());
    }

    /**
     * @return OrderProductForViewing[]
     */
    private function getProducts(GetOrderProductsForViewing $query): array
    {
        /** @var CommandBusInterface $queryBus */
        $queryBus = self::getContainer()->get('prestashop.core.query_bus');
        /** @var OrderProductsForViewing $result */
        $result = $queryBus->handle($query);

        return $result->getProducts();
    }
}
