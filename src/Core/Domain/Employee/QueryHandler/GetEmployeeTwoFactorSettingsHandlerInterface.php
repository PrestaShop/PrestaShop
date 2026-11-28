<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Employee\QueryHandler;

use PrestaShop\PrestaShop\Core\Domain\Employee\Query\GetEmployeeTwoFactorSettings;
use PrestaShop\PrestaShop\Core\Domain\Employee\QueryResult\EmployeeTwoFactorSettings;

interface GetEmployeeTwoFactorSettingsHandlerInterface
{
    public function handle(GetEmployeeTwoFactorSettings $query): EmployeeTwoFactorSettings;
}
