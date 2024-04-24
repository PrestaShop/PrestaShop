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

/**
 * ShopGroup.
 *
 * @ORM\Table()
 *
 * @ORM\Entity(repositoryClass="PrestaShopBundle\Entity\Repository\ShopGroupRepository")
 */
class ShopGroup
{
    /**
     * @ORM\Id
     *
     * @ORM\Column(name="id_shop_group", type="integer")
     *
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private int $id;

    /**
     * @ORM\Column(name="name", type="string", length=64)
     */
    private string $name;

    /**
     * @ORM\Column(name="color", type="string", length=50)
     */
    private string $color;

    /**
     * @ORM\Column(name="share_customer", type="boolean")
     */
    private bool $shareCustomer;

    /**
     * @ORM\Column(name="share_order", type="boolean")
     */
    private bool $shareOrder;

    /**
     * @ORM\Column(name="share_stock", type="boolean")
     */
    private bool $shareStock;

    /**
     * @ORM\Column(name="active", type="boolean")
     */
    private bool $active;

    /**
     * @ORM\Column(name="deleted", type="boolean")
     */
    private bool $deleted;

    /**
     * @var Collection<Shop>
     *
     * One group shop has many shops. This is the inverse side.
     *
     * @ORM\OneToMany(targetEntity="PrestaShopBundle\Entity\Shop", mappedBy="shopGroup")
     */
    private Collection $shops;

    public function __construct()
    {
        $this->shops = new ArrayCollection();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getName(): string
    {
        return $this->name;
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

    public function setShareCustomer(bool $shareCustomer): static
    {
        $this->shareCustomer = $shareCustomer;

        return $this;
    }

    public function getShareCustomer(): bool
    {
        return $this->shareCustomer;
    }

    public function setShareOrder(bool $shareOrder): static
    {
        $this->shareOrder = $shareOrder;

        return $this;
    }

    public function getShareOrder(): bool
    {
        return $this->shareOrder;
    }

    public function setShareStock(bool $shareStock): static
    {
        $this->shareStock = $shareStock;

        return $this;
    }

    public function getShareStock(): bool
    {
        return $this->shareStock;
    }

    public function setActive(bool $active): static
    {
        $this->active = $active;

        return $this;
    }

    public function getActive(): bool
    {
        return $this->active;
    }

    public function setDeleted(bool $deleted): static
    {
        $this->deleted = $deleted;

        return $this;
    }

    public function getDeleted(): bool
    {
        return $this->deleted;
    }

    /**
     * @return Collection<Shop>
     */
    public function getShops(): Collection
    {
        return $this->shops;
    }
}
