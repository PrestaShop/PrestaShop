<?php
/**
 * 2007-2018 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
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
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Adapter\Profile\Permission\QueryHandler;

use Context;
use PrestaShop\PrestaShop\Core\Domain\Profile\Permission\Query\GetPermissionsForConfiguration;
use PrestaShop\PrestaShop\Core\Domain\Profile\Permission\QueryHandler\GetPermissionsForConfigurationHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Profile\Permission\QueryResult\ConfigurablePermissions;
use Profile;
use Tab;

/**
 * Get configuratble permissions
 *
 * @internal
 */
final class GetPermissionsForConfigurationHandler implements GetPermissionsForConfigurationHandlerInterface
{
    /**
     * {@inheritdoc}
     */
    public function handle(GetPermissionsForConfiguration $query)
    {
        $nonConfigurableTabs = $this->getNonConfigurableTabs();

        $profiles = Profile::getProfiles(Context::getContext()->language->id);

        return new ConfigurablePermissions($profiles);
    }

    /**
     * @return int[] IDs of non configurable tabs
     */
    private function getNonConfigurableTabs()
    {
        return [
            Tab::getIdFromClassName('AdminLogin'),
        ];
    }
}
