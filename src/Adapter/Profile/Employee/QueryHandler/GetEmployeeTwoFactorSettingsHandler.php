<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Profile\Employee\QueryHandler;

use PrestaShop\PrestaShop\Adapter\Employee\TwoFactor\EmployeeTwoFactorManager;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsQueryHandler;
use PrestaShop\PrestaShop\Core\Domain\Employee\Query\GetEmployeeTwoFactorSettings;
use PrestaShop\PrestaShop\Core\Domain\Employee\QueryHandler\GetEmployeeTwoFactorSettingsHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Employee\QueryResult\EmployeeTwoFactorSettings;

#[AsQueryHandler]
final class GetEmployeeTwoFactorSettingsHandler implements GetEmployeeTwoFactorSettingsHandlerInterface
{
    public function __construct(private readonly EmployeeTwoFactorManager $manager)
    {
    }

    public function handle(GetEmployeeTwoFactorSettings $query): EmployeeTwoFactorSettings
    {
        return $this->manager->getSettings($query->getEmployeeId()->getValue(), $query->getPreferredProvider());
    }
}
