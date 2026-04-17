<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Resources\Context;

use PrestaShop\PrestaShop\Core\Context\Employee;
use PrestaShop\PrestaShop\Core\Context\EmployeeContext;

/**
 * This decorator is used for test environment only it allows it makes the context mutable and allows
 * to vary its value in test scenarios. Not to use in prod code as the contexts are meant to be immutable.
 */
class EmployeeContextDecorator extends EmployeeContext
{
    private EmployeeContext $decoratedEmployeeContext;

    private ?Employee $overriddenEmployee = null;

    private bool $useOverriddenValue = false;

    public function __construct(EmployeeContext $decoratedEmployeeContext)
    {
        $this->decoratedEmployeeContext = $decoratedEmployeeContext;
        parent::__construct($decoratedEmployeeContext->getEmployee(), []);
    }

    public function getEmployee(): ?Employee
    {
        if ($this->useOverriddenValue) {
            return $this->overriddenEmployee;
        }

        return $this->decoratedEmployeeContext->getEmployee();
    }

    /**
     * Once the value has been overridden it will we used instead of the initial one (even if it's null),
     * to disable this permanent override you can use resetOverriddenEmployee
     *
     * @param Employee|null $overriddenEmployee
     */
    public function setOverriddenEmployee(?Employee $overriddenEmployee): void
    {
        $this->useOverriddenValue = true;
        $this->overriddenEmployee = $overriddenEmployee;
    }

    /**
     * This method resets the override values, thus the decorator keeps acting as a simple proxy without impacting
     * the decorated service.
     */
    public function resetOverriddenEmployee(): void
    {
        $this->useOverriddenValue = false;
        $this->overriddenEmployee = null;
    }
}
