<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Security\Role;

use Symfony\Component\Security\Core\Role\RoleHierarchyInterface;

/**
 * Class DynamicRoleHierarchy is used for Symfony role hierarchy voter to load roles from database.
 */
class DynamicRoleHierarchy implements RoleHierarchyInterface
{
    /**
     * @param array<string> $roles An array of directly assigned roles
     *
     * @return string[] An array of all reachable roles
     */
    public function getReachableRoleNames(array $roles): array
    {
        return [];
    }
}
