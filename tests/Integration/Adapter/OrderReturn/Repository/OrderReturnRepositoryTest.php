<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Adapter\OrderReturn\Repository;

use PrestaShop\PrestaShop\Adapter\OrderReturn\Repository\OrderReturnRepository;
use PrestaShop\PrestaShop\Core\Domain\OrderReturn\Exception\OrderReturnNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\OrderReturn\ValueObject\OrderReturnId;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class OrderReturnRepositoryTest extends KernelTestCase
{
    /**
     * @var OrderReturnRepository
     */
    private $orderReturnRepository;

    protected function setUp(): void
    {
        self::bootKernel();

        $this->orderReturnRepository = self::getContainer()->get('prestashop.adapter.order_return.repository.order_return_repository');
    }

    public function testGetThrowsException(): void
    {
        $this->expectException(OrderReturnNotFoundException::class);
        $this->orderReturnRepository->get(new OrderReturnId(9999));
    }
}
