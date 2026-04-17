<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Profile\Permission\QueryHandler;

use PrestaShop\PrestaShop\Core\Domain\Profile\Permission\Query\GetPermissionsForConfiguration;
use PrestaShop\PrestaShop\Core\Domain\Profile\Permission\QueryResult\ConfigurablePermissions;

/**
 * Interface for service that gets permissions data for configuration
 */
interface GetPermissionsForConfigurationHandlerInterface
{
    /**
     * @param GetPermissionsForConfiguration $query
     *
     * @return ConfigurablePermissions
     */
    public function handle(GetPermissionsForConfiguration $query): ConfigurablePermissions;
}
