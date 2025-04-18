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

namespace Tests\Integration\Utility;

use Doctrine\ORM\EntityManagerInterface;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use PrestaShopBundle\Entity\Employee\Employee;
use PrestaShopBundle\Entity\Employee\EmployeeSession;
use PrestaShopBundle\Security\Admin\EmployeeProvider;
use PrestaShopBundle\Security\Admin\TokenAttributes;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;

trait LoginTrait
{
    protected static function loginUser(KernelBrowser $kernelBrowser, ?ShopConstraint $shopConstraint = null): void
    {
        /** @var EmployeeProvider $employeeProvider */
        $employeeProvider = $kernelBrowser->getContainer()->get(EmployeeProvider::class);
        /** @var Employee $employee */
        $employee = $employeeProvider->loadUserByIdentifier('test@prestashop.com');

        if ($employee->getSessions()->isEmpty()) {
            $employeeSession = new EmployeeSession();
            $employeeSession->setToken('fake_token');
            $employee->addSession($employeeSession);
            $entityManager = $kernelBrowser->getContainer()->get(EntityManagerInterface::class);
            $entityManager->persist($employeeSession);
            $entityManager->flush();
        } else {
            $employeeSession = $employee->getSessions()->first();
        }

        // The employee session and the shop constraint are stored as token attributes
        $kernelBrowser->loginUser($employee, 'main', [
            TokenAttributes::EMPLOYEE_SESSION => $employeeSession,
            TokenAttributes::SHOP_CONSTRAINT => $shopConstraint,
            // Simulate local IP address
            TokenAttributes::IP_ADDRESS => '127.0.0.1',
        ]);
    }
}
