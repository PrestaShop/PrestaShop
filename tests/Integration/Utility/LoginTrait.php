<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
