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

namespace PrestaShopBundle\EventListener\Context\Admin;

use PrestaShop\PrestaShop\Core\Context\EmployeeContextBuilder;
use Symfony\Component\HttpFoundation\Request;
use Tests\Unit\PrestaShopBundle\EventListener\Context\ContextEventListenerTestCase;

class EmployeeContextListenerTest extends ContextEventListenerTestCase
{
    public function testFindEmployee(): void
    {
        $employeeBuilder = new EmployeeContextBuilder();
        $listener = new EmployeeContextListener(
            $employeeBuilder,
            $this->mockLegacyContext(['id_employee' => 42])
        );

        $event = $this->createRequestEvent(new Request());
        $listener->onKernelRequest($event);
        $this->assertEquals(42, $this->getPrivateField($employeeBuilder, 'employeeId'));
    }

    public function testEmployeeNotFound(): void
    {
        $event = $this->createRequestEvent(new Request());
        $employeeBuilder = new EmployeeContextBuilder();
        $listener = new EmployeeContextListener(
            $employeeBuilder,
            $this->mockLegacyContext(['id_employee' => null])
        );
        $listener->onKernelRequest($event);
        $this->assertEquals(null, $this->getPrivateField($employeeBuilder, 'employeeId'));
    }
}
