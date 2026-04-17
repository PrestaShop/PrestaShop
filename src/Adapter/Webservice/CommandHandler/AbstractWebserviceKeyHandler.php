<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Webservice\CommandHandler;

use PrestaShop\PrestaShop\Adapter\Domain\AbstractObjectModelHandler;
use Tools;
use WebserviceKey;

/**
 * Encapsulates common legacy behavior for adding/editing WebserviceKey
 *
 * @internal
 */
abstract class AbstractWebserviceKeyHandler extends AbstractObjectModelHandler
{
    /**
     * @param WebserviceKey $webserviceKey
     * @param array $permissions
     */
    protected function setPermissionsForWebserviceKey(WebserviceKey $webserviceKey, array $permissions)
    {
        Tools::generateHtaccess();

        $legacyPermissionsStructure = [];

        foreach ($permissions as $permission => $resources) {
            foreach ($resources as $resource) {
                $legacyPermissionsStructure[$resource][$permission] = 'on';
            }
        }

        WebserviceKey::setPermissionForAccount($webserviceKey->id, $legacyPermissionsStructure);
    }
}
