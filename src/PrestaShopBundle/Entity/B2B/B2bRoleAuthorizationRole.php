<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Entity\B2B;

use Doctrine\ORM\Mapping as ORM;
use PrestaShopBundle\Entity\Employee\AuthorizationRole;

/**
 * B2bRoleAuthorizationRole.
 *
 * @ORM\Table(
 *     indexes={
 *
 *         @ORM\Index(name="b2b_role_authorization_role_role_idx", columns={"id_role"}),
 *         @ORM\Index(name="b2b_role_authorization_role_auth_role_idx", columns={"id_authorization_role"})
 *     }
 *  )
 *
 * @ORM\Entity()
 */
class B2bRoleAuthorizationRole
{
    /**
     * @ORM\Id
     *
     * @ORM\ManyToOne(targetEntity="PrestaShopBundle\Entity\B2B\B2bRole", inversedBy="b2bRoleAuthorizationRoles")
     *
     * @ORM\JoinColumn(name="id_role", referencedColumnName="id_role", nullable=false)
     */
    private B2bRole $role;

    /**
     * @ORM\Id
     *
     * @ORM\ManyToOne(targetEntity="PrestaShopBundle\Entity\Employee\AuthorizationRole")
     *
     * @ORM\JoinColumn(name="id_authorization_role", referencedColumnName="id_authorization_role", nullable=false)
     */
    private AuthorizationRole $authorizationRole;

    public function getRole(): B2bRole
    {
        return $this->role;
    }

    public function setRole(B2bRole $role): self
    {
        $this->role = $role;

        return $this;
    }

    public function getAuthorizationRole(): AuthorizationRole
    {
        return $this->authorizationRole;
    }

    public function setAuthorizationRole(AuthorizationRole $authorizationRole): self
    {
        $this->authorizationRole = $authorizationRole;

        return $this;
    }
}
