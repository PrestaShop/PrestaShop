<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Entity\Employee;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 *
 * @ORM\Table(uniqueConstraints={@ORM\UniqueConstraint(name="slug", columns={"slug"})})
 */
class AuthorizationRole
{
    /**
     * @ORM\Id
     *
     * @ORM\Column(name="id_authorization_role", type="integer", options={"unsigned": true})
     *
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private int $id;

    /**
     * @ORM\Column(name="slug", type="string", nullable=false, unique=true, length=191)
     */
    private string $slug;

    public function getId(): int
    {
        return $this->id;
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): static
    {
        $this->slug = $slug;

        return $this;
    }
}
