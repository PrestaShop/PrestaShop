<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Entity\Employee;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 *
 * @ORM\Table
 */
class Profile
{
    public const ADMIN_PROFILE_ID = 1;

    /**
     * @ORM\Id
     *
     * @ORM\Column(name="id_profile", type="integer", options={"unsigned": true})
     *
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private ?int $id = null;

    /**
     * @ORM\ManyToMany(targetEntity="PrestaShopBundle\Entity\Employee\AuthorizationRole")
     *
     * @ORM\JoinTable(
     *     options={"ps_table"="access"},
     *     joinColumns={@ORM\JoinColumn(name="id_profile", referencedColumnName="id_profile")},
     *     inverseJoinColumns={@ORM\JoinColumn(name="id_authorization_role", referencedColumnName="id_authorization_role")}
     *  )
     */
    private Collection $authorizationRoles;

    public function __construct(?int $id = null)
    {
        $this->id = $id;
        $this->authorizationRoles = new ArrayCollection();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function isAdmin(): bool
    {
        return $this->id === self::ADMIN_PROFILE_ID;
    }

    public function getAuthorizationRoles(): Collection
    {
        return $this->authorizationRoles;
    }
}
