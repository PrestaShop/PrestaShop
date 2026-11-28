<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Adapter\Order\Repository;

use Customer;
use Db;
use Order;
use PrestaShop\PrestaShop\Adapter\Order\Repository\OrderRepository;
use PrestaShop\PrestaShop\Core\Domain\Order\Exception\OrderNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Order\ValueObject\OrderId;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Tests\Resources\DatabaseDump;

class OrderRepositoryTest extends KernelTestCase
{
    private const ORDER_ID = 1;

    /**
     * @var OrderRepository
     */
    private $orderRepository;

    public static function tearDownAfterClass(): void
    {
        parent::tearDownAfterClass();
        DatabaseDump::restoreTables(['group']);
    }

    protected function setUp(): void
    {
        self::bootKernel();

        $this->orderRepository = self::getContainer()->get('prestashop.adapter.order.repository.order_repository');
    }

    public function testGetThrowsException(): void
    {
        $this->expectException(OrderNotFoundException::class);
        $this->orderRepository->get(new OrderId(9999));
    }

    public function testGetPriceDisplaySettingsFollowsTheDefaultGroupOfTheCustomer(): void
    {
        $order = new Order(self::ORDER_ID);
        $groupId = (int) (new Customer((int) $order->id_customer))->id_default_group;

        foreach ([PS_TAX_INC => true, PS_TAX_EXC => false] as $priceDisplayMethod => $isTaxIncluded) {
            Db::getInstance()->update('group', ['price_display_method' => $priceDisplayMethod], 'id_group = ' . $groupId);

            $this->assertSame(
                ['currency_id' => (int) $order->id_currency, 'is_tax_included' => $isTaxIncluded],
                $this->orderRepository->getPriceDisplaySettings(new OrderId(self::ORDER_ID))
            );
        }
    }

    public function testGetPriceDisplaySettingsThrowsExceptionWhenOrderIsNotFound(): void
    {
        $this->expectException(OrderNotFoundException::class);
        $this->orderRepository->getPriceDisplaySettings(new OrderId(9999));
    }
}
