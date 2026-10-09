<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Customer\QueryHandler;

use PrestaShop\PrestaShop\Adapter\Customer\Repository\CustomerRepository;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsQueryHandler;
use PrestaShop\PrestaShop\Core\Domain\Customer\Query\GetCustomerShopId;
use PrestaShop\PrestaShop\Core\Domain\Customer\QueryHandler\GetCustomerShopIdHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopId;

#[AsQueryHandler]
final class GetCustomerShopIdHandler implements GetCustomerShopIdHandlerInterface
{
    public function __construct(
        private readonly CustomerRepository $customerRepository,
    ) {
    }

    public function handle(GetCustomerShopId $query): ShopId
    {
        $customer = $this->customerRepository->get($query->getCustomerId());

        return new ShopId((int) $customer->id_shop);
    }
}
