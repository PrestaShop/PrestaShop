<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\Entity\B2B;

use Doctrine\ORM\Mapping as ORM;
use PrestaShopBundle\Entity\Lang;

/**
 * @ORM\Table()
 *
 * @ORM\Entity()
 */
class B2bRoleLang
{
    /**
     * @ORM\Id
     *
     * @ORM\ManyToOne(targetEntity=B2bRole::class, inversedBy="translations")
     *
     * @ORM\JoinColumn(name="id_role", referencedColumnName="id_role", nullable=false, options={"unsigned"=true})
     */
    private B2bRole $role;

    /**
     * @ORM\Id
     *
     * @ORM\ManyToOne(targetEntity=Lang::class)
     *
     * @ORM\JoinColumn(name="id_lang", referencedColumnName="id_lang", nullable=false)
     */
    private Lang $language;

    /**
     * @ORM\Column(type="string", length=128)
     */
    private string $name;

    public function __construct(Lang $language, string $name)
    {
        $this->language = $language;
        $this->setName($name);
    }

    public function getRole(): ?B2bRole
    {
        return $this->role ?? null;
    }

    public function getLanguage(): Lang
    {
        return $this->language;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @internal use {@see B2bRole::addTranslation()} instead
     */
    public function setRole(B2bRole $role): void
    {
        $this->role = $role;
    }
}
