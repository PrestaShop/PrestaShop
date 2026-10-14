<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Shop\QueryHandler;

use PrestaShop\PrestaShop\Adapter\Shop\Repository\ShopGroupRepository;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsQueryHandler;
use PrestaShop\PrestaShop\Core\Context\EmployeeContext;
use PrestaShop\PrestaShop\Core\Domain\Shop\Query\GetShopTree;
use PrestaShop\PrestaShop\Core\Domain\Shop\QueryHandler\GetShopTreeHandlerInterface;

#[AsQueryHandler]
final class GetShopTreeHandler implements GetShopTreeHandlerInterface
{
    public function __construct(
        private readonly ShopGroupRepository $repository,
        private readonly EmployeeContext $employeeContext,
    ) {
    }

    public function handle(GetShopTree $query): array
    {
        $employee = $this->employeeContext->getEmployee();
        if (null === $employee || $this->employeeContext->isSuperAdmin()) {
            return $this->repository->getShopTree();
        }

        return $this->repository->getShopTree($employee->getAssociatedShopIds());
    }
}
