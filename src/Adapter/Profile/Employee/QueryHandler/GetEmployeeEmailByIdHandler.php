<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Profile\Employee\QueryHandler;

use PrestaShop\PrestaShop\Adapter\Profile\Employee\AbstractEmployeeHandler;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsQueryHandler;
use PrestaShop\PrestaShop\Core\Domain\Employee\Query\GetEmployeeEmailById;
use PrestaShop\PrestaShop\Core\Domain\Employee\QueryHandler\GetEmployeeEmailByIdHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\ValueObject\Email;

#[AsQueryHandler]
final class GetEmployeeEmailByIdHandler extends AbstractEmployeeHandler implements GetEmployeeEmailByIdHandlerInterface
{
    /**
     * {@inheritdoc}
     */
    public function handle(GetEmployeeEmailById $query): Email
    {
        $employee = $this->getEmployee($query->getEmployeeId());

        return new Email($employee->email);
    }
}
