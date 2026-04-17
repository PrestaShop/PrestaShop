<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Adapter\Order\Repository;

use PrestaShop\PrestaShop\Adapter\Order\Repository\OrderRepository;
use PrestaShop\PrestaShop\Core\Domain\Order\Exception\OrderNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Order\ValueObject\OrderId;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class OrderRepositoryTest extends KernelTestCase
{
    /**
     * @var OrderRepository
     */
    private $orderRepository;

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
}
