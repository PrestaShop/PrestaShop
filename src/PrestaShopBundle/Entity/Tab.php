<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Tab.
 *
 * @ORM\Table()
 *
 * @ORM\Entity(repositoryClass="PrestaShopBundle\Entity\Repository\TabRepository")
 */
class Tab
{
    /**
     * @ORM\Id
     *
     * @ORM\Column(name="id_tab", type="integer")
     *
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private int $id;

    /**
     * @ORM\Column(name="id_parent", type="integer")
     */
    private int $idParent;

    /**
     * @ORM\Column(name="position", type="integer")
     */
    private int $position;

    /**
     * @ORM\Column(name="module", type="string", length=64, nullable=true)
     */
    private ?string $module;

    /**
     * @ORM\Column(name="class_name", type="string", length=64)
     */
    private string $className;

    /**
     * @ORM\Column(name="route_name", type="string", length=256, nullable=true)
     */
    private ?string $routeName;

    /**
     * @ORM\Column(name="active", type="boolean")
     */
    private bool $active;

    /**
     * @ORM\Column(name="enabled", type="boolean")
     */
    private bool $enabled = true;

    /**
     * @ORM\Column(name="icon", type="string", length=64, nullable=true)
     */
    private ?string $icon;

    /**
     * @ORM\Column(name="wording", type="string", length=255, nullable=true)
     */
    private ?string $wording;

    /**
     * @ORM\Column(name="wording_domain", type="string", length=255, nullable=true)
     */
    private ?string $wordingDomain;

    /**
     * @var Collection<TabLang>
     *
     * @ORM\OneToMany(targetEntity="PrestaShopBundle\Entity\TabLang", mappedBy="tab")
     */
    private Collection $tabLangs;

    public function __construct()
    {
        $this->tabLangs = new ArrayCollection();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getIdParent(): int
    {
        return $this->idParent;
    }

    public function setIdParent(int $idParent): static
    {
        $this->idParent = $idParent;

        return $this;
    }

    public function getPosition(): int
    {
        return $this->position;
    }

    public function setPosition(int $position): static
    {
        $this->position = $position;

        return $this;
    }

    public function getModule(): ?string
    {
        return $this->module;
    }

    public function setModule(?string $module): static
    {
        $this->module = $module;

        return $this;
    }

    public function getClassName(): string
    {
        return $this->className;
    }

    public function setClassName(string $className): static
    {
        $this->className = $className;

        return $this;
    }

    public function getActive(): bool
    {
        return $this->active;
    }

    public function setActive(bool $active): static
    {
        $this->active = $active;

        return $this;
    }

    public function getIcon(): ?string
    {
        return $this->icon;
    }

    public function setIcon(?string $icon): static
    {
        $this->icon = $icon;

        return $this;
    }

    /**
     * @return Collection<TabLang>
     */
    public function getTabLangs(): Collection
    {
        return $this->tabLangs;
    }

    public function getTabLangByLanguageId(int $languageId): ?TabLang
    {
        /** @var TabLang $tabLang */
        foreach ($this->getTabLangs() as $tabLang) {
            if ($tabLang->getLang()->getId() === $languageId) {
                return $tabLang;
            }
        }

        return null;
    }

    public function addTabLang(TabLang $tabLang): static
    {
        $this->tabLangs[] = $tabLang;

        $tabLang->setTab($this);

        return $this;
    }

    public function removeTabLang(TabLang $tabLang): void
    {
        $this->tabLangs->removeElement($tabLang);
    }

    public function getWording(): ?string
    {
        return $this->wording;
    }

    public function setWording(?string $wording): static
    {
        $this->wording = $wording;

        return $this;
    }

    public function getWordingDomain(): ?string
    {
        return $this->wordingDomain;
    }

    public function setWordingDomain(?string $wordingDomain): static
    {
        $this->wordingDomain = $wordingDomain;

        return $this;
    }

    public function getRouteName(): ?string
    {
        return $this->routeName;
    }

    public function setRouteName(?string $routeName): static
    {
        $this->routeName = $routeName;

        return $this;
    }

    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    public function setEnabled(bool $enabled): static
    {
        $this->enabled = $enabled;

        return $this;
    }
}
