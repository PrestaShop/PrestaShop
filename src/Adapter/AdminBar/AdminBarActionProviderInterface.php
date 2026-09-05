<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\AdminBar;

use PrestaShop\PrestaShop\Core\Security\AdminEmployeeContext;

interface AdminBarActionProviderInterface
{
    /** @return iterable<AdminBarAction> */
    public function getActions(AdminBarPageContext $pageContext, AdminEmployeeContext $employeeContext): iterable;
}
