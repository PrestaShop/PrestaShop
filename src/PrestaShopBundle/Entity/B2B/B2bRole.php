<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Entity\B2B;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * B2bRole.
 *
 * @ORM\Table(
 *     indexes={@ORM\Index(name="uniq_b2b_role", columns={"role"})}
 * )
 *
 * @ORM\Entity()
 */
class B2bRole
{
    /**
     * @ORM\Id
     *
     * @ORM\Column(name="id_role", type="integer", options={"unsigned"=true})
     *
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private int $id;

    /**
     * @ORM\Column(name="role", type="string", length=64)
     */
    private string $role;

    /**
     * @ORM\OneToMany(targetEntity="PrestaShopBundle\Entity\B2B\BusinessEntityCustomerB2b", mappedBy="b2bRole")
     */
    private Collection $businessEntityCustomerB2bs;

    /**
     * @ORM\OneToMany(targetEntity="PrestaShopBundle\Entity\B2B\B2bRoleAuthorizationRole", mappedBy="role")
     */
    private Collection $b2bRoleAuthorizationRoles;

    public function __construct()
    {
        $this->businessEntityCustomerB2bs = new ArrayCollection();
        $this->b2bRoleAuthorizationRoles = new ArrayCollection();
    }

    public function getIdRole(): int
    {
        return $this->id;
    }

    public function getRole(): string
    {
        return $this->role;
    }

    public function setRole(string $role): self
    {
        $this->role = $role;

        return $this;
    }

    public function getBusinessEntityCustomerB2bs(): Collection
    {
        return $this->businessEntityCustomerB2bs;
    }

    public function addBusinessEntityCustomerB2b(BusinessEntityCustomerB2b $businessEntityCustomerB2b): self
    {
        if (!$this->businessEntityCustomerB2bs->contains($businessEntityCustomerB2b)) {
            $this->businessEntityCustomerB2bs[] = $businessEntityCustomerB2b;
            $businessEntityCustomerB2b->setB2bRole($this);
        }

        return $this;
    }

    public function removeBusinessEntityCustomerB2b(BusinessEntityCustomerB2b $businessEntityCustomerB2b): self
    {
        $this->businessEntityCustomerB2bs->removeElement($businessEntityCustomerB2b);

        return $this;
    }

    public function getB2bRoleAuthorizationRoles(): Collection
    {
        return $this->b2bRoleAuthorizationRoles;
    }

    public function addB2bRoleAuthorizationRole(B2bRoleAuthorizationRole $b2bRoleAuthorizationRole): self
    {
        if (!$this->b2bRoleAuthorizationRoles->contains($b2bRoleAuthorizationRole)) {
            $this->b2bRoleAuthorizationRoles[] = $b2bRoleAuthorizationRole;
            $b2bRoleAuthorizationRole->setRole($this);
        }

        return $this;
    }

    public function removeB2bRoleAuthorizationRole(B2bRoleAuthorizationRole $b2bRoleAuthorizationRole): self
    {
        $this->b2bRoleAuthorizationRoles->removeElement($b2bRoleAuthorizationRole);

        return $this;
    }
}
