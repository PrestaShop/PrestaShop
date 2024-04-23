<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
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
        parent::__construct($decoratedEmployeeContext->getEmployee());
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
