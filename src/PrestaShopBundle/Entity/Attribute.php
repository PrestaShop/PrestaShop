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
 * Attribute.
 *
 * @ORM\Table(
 *     indexes={@ORM\Index(name="attribute_group", columns={"id_attribute_group"})}
 * )
 *
 * @ORM\Entity(repositoryClass="PrestaShopBundle\Entity\Repository\AttributeRepository")
 */
class Attribute
{
    /**
     * @ORM\Id
     *
     * @ORM\Column(name="id_attribute", type="integer")
     *
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private int $id;

    /**
     * @ORM\ManyToOne(targetEntity="PrestaShopBundle\Entity\AttributeGroup", inversedBy="attributes")
     *
     * @ORM\JoinColumn(name="id_attribute_group", referencedColumnName="id_attribute_group", nullable=false)
     */
    private AttributeGroup $attributeGroup;

    /**
     * @ORM\Column(name="color", type="string", length=32)
     */
    private string $color;

    /**
     * @ORM\Column(name="position", type="integer")
     */
    private int $position;

    /**
     * @ORM\ManyToMany(targetEntity="PrestaShopBundle\Entity\Shop", cascade={"persist"})
     *
     * @ORM\JoinTable(
     *      joinColumns={@ORM\JoinColumn(name="id_attribute", referencedColumnName="id_attribute")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="id_shop", referencedColumnName="id_shop", onDelete="CASCADE")}
     * )
     */
    private Collection $shops;

    /**
     * @ORM\OneToMany(targetEntity="PrestaShopBundle\Entity\AttributeLang", mappedBy="attribute")
     */
    private Collection $attributeLangs;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->shops = new ArrayCollection();
        $this->attributeLangs = new ArrayCollection();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setColor(string $color): static
    {
        $this->color = $color;

        return $this;
    }

    public function getColor(): string
    {
        return $this->color;
    }

    public function setPosition(int $position): static
    {
        $this->position = $position;

        return $this;
    }

    public function getPosition(): int
    {
        return $this->position;
    }

    public function setAttributeGroup(AttributeGroup $attributeGroup): static
    {
        $this->attributeGroup = $attributeGroup;

        return $this;
    }

    public function getAttributeGroup(): AttributeGroup
    {
        return $this->attributeGroup;
    }

    public function addShop(Shop $shop): static
    {
        $this->shops[] = $shop;

        return $this;
    }

    public function removeShop(Shop $shop): void
    {
        $this->shops->removeElement($shop);
    }

    /**
     * Get shops.
     *
     * @return Collection<Shop>
     */
    public function getShops(): Collection
    {
        return $this->shops;
    }

    public function addAttributeLang(AttributeLang $attributeLang): static
    {
        $this->attributeLangs[] = $attributeLang;

        $attributeLang->setAttribute($this);

        return $this;
    }

    public function removeAttributeLang(AttributeLang $attributeLang): void
    {
        $this->attributeLangs->removeElement($attributeLang);
    }

    /**
     * @return Collection<AttributeLang>
     */
    public function getAttributeLangs(): Collection
    {
        return $this->attributeLangs;
    }
}
