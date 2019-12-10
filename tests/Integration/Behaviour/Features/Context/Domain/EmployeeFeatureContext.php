<?php

namespace Tests\Integration\Behaviour\Features\Context\Domain;

use PrestaShop\PrestaShop\Core\Domain\Employee\Query\GetEmployeeForEditing;
use Symfony\Component\DependencyInjection\Exception\ServiceCircularReferenceException;
use Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;

class EmployeeFeatureContext extends AbstractDomainFeatureContext
{
    /**
     * @Given employee with id :employeeId exists
     *
     * @param int $employeeId
     *
     * @throws ServiceCircularReferenceException
     * @throws ServiceNotFoundException
     */
    public function employeeWithIdExists(int $employeeId)
    {
        $this->getQueryBus()->handle(new GetEmployeeForEditing((int) $employeeId));
    }
}
