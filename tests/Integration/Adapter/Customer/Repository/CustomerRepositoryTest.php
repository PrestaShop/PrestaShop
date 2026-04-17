<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Adapter\Customer\Repository;

use PrestaShop\PrestaShop\Adapter\Customer\Repository\CustomerRepository;
use PrestaShop\PrestaShop\Core\Domain\Customer\Exception\CustomerNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Customer\ValueObject\CustomerId;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class CustomerRepositoryTest extends KernelTestCase
{
    /**
     * @var CustomerRepository
     */
    private $customerRepository;

    protected function setUp(): void
    {
        self::bootKernel();

        $this->customerRepository = self::getContainer()->get(CustomerRepository::class);
    }

    public function testGetThrowsException(): void
    {
        $this->expectException(CustomerNotFoundException::class);
        $this->customerRepository->get(new CustomerId(9999));
    }
}
