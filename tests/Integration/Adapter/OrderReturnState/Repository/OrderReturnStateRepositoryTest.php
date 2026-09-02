<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Adapter\OrderReturnState\Repository;

use PrestaShop\PrestaShop\Adapter\OrderReturnState\Repository\OrderReturnStateRepository;
use PrestaShop\PrestaShop\Core\Domain\OrderReturnState\Exception\OrderReturnStateNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\OrderReturnState\ValueObject\OrderReturnStateId;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class OrderReturnStateRepositoryTest extends KernelTestCase
{
    /**
     * @var OrderReturnStateRepository
     */
    private $orderReturnStateRepository;

    protected function setUp(): void
    {
        self::bootKernel();

        $this->orderReturnStateRepository = self::getContainer()->get('prestashop.adapter.order_return_state.repository.order_return_state_repository');
    }

    public function testGetThrowsException(): void
    {
        $this->expectException(OrderReturnStateNotFoundException::class);
        $this->orderReturnStateRepository->get(new OrderReturnStateId(9999));
    }
}
