<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace PrestaShopBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use InvalidArgumentException;

/**
 * AttributeGroup.
 *
 * @ORM\Table()
 *
 * @ORM\Entity(repositoryClass="PrestaShopBundle\Entity\Repository\AttributeGroupRepository")
 */
class AttributeGroup
{
    /**
     * @ORM\Id
     *
     * @ORM\Column(name="id_attribute_group", type="integer")
     *
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private int $id;

    /**
     * @ORM\Column(name="is_color_group", type="boolean")
     */
    private bool $isColorGroup;

    /**
     * @ORM\Column(name="group_type", type="string", length=255)
     */
    private string $groupType;

    /**
     * @ORM\Column(name="position", type="integer")
     */
    private int $position;

    /**
     * @ORM\OneToMany(targetEntity="PrestaShopBundle\Entity\Attribute", mappedBy="attributeGroup", orphanRemoval=true)
     */
    private Collection $attributes;

    /**
     * @ORM\ManyToMany(targetEntity="PrestaShopBundle\Entity\Shop", cascade={"persist"})
     *
     * @ORM\JoinTable(
     *      joinColumns={@ORM\JoinColumn(name="id_attribute_group", referencedColumnName="id_attribute_group")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="id_shop", referencedColumnName="id_shop", onDelete="CASCADE")}
     * )
     */
    private Collection $shops;

    /**
     * @var Collection<AttributeGroupLang>
     *
     * @ORM\OneToMany(targetEntity="PrestaShopBundle\Entity\AttributeGroupLang", mappedBy="attributeGroup", orphanRemoval=true)
     */
    private Collection $attributeGroupLangs;

    private $groupTypeAvailable = [
        'select',
        'radio',
        'color',
    ];

    public function __construct()
    {
        $this->groupType = 'select';
        $this->shops = new ArrayCollection();
        $this->attributes = new ArrayCollection();
        $this->attributeGroupLangs = new ArrayCollection();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setIsColorGroup(bool $isColorGroup): static
    {
        $this->isColorGroup = $isColorGroup;

        return $this;
    }

    public function getIsColorGroup(): bool
    {
        return $this->isColorGroup;
    }

    public function setGroupType(string $groupType): static
    {
        if (!in_array($groupType, $this->groupTypeAvailable)) {
            throw new InvalidArgumentException('Invalid group type');
        }

        $this->groupType = $groupType;

        return $this;
    }

    public function getGroupType(): string
    {
        return $this->groupType;
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

    /**
     * @return Collection<Attribute>
     */
    public function getAttributes(): Collection
    {
        return $this->attributes;
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

    public function getShops(): Collection
    {
        return $this->shops;
    }

    public function addAttributeGroupLang(AttributeGroupLang $attributeGroupLang): static
    {
        $this->attributeGroupLangs[] = $attributeGroupLang;

        $attributeGroupLang->setAttributeGroup($this);

        return $this;
    }

    public function removeAttributeGroupLang(AttributeGroupLang $attributeGroupLang): void
    {
        $this->attributeGroupLangs->removeElement($attributeGroupLang);
    }

    /**
     * @return Collection<AttributeGroupLang>
     */
    public function getAttributeGroupLangs(): Collection
    {
        return $this->attributeGroupLangs;
    }
}
