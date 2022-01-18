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
use PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints as PrestaShopAssert;
use PrestaShopBundle\Entity\Repository\StoreRepository;

/**
 * @ORM\Table()
 * @ORM\Entity(repositoryClass=StoreRepository::class)
 */
class Store
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(name="id_store", type="integer", options={"unsigned":true})
     */
    private $id;

    /**
     * @ORM\Column(name="id_country", type="integer", options={"unsigned"=true})
     */
    private $countryId;

    /**
     * @ORM\Column(name="id_state", type="integer", nullable=true, options={"unsigned"=true})
     */
    private $stateId;

    /**
     * @PrestaShopAssert\TypedRegex(type="post_code")
     * @ORM\Column(type="string", length=12)
     */
    private $postcode;

    /**
     * @PrestaShopAssert\TypedRegex(type="city_name")
     * @ORM\Column(type="string", length=64)
     */
    private $city;

    /**
     * @PrestaShopAssert\TypedRegex(type="coordinate")
     * @ORM\Column(type="float", nullable=true)
     */
    private $latitude;

    /**
     * @PrestaShopAssert\TypedRegex(type="coordinate")
     * @ORM\Column(type="float", nullable=true)
     */
    private $longitude;

    /**
     * @PrestaShopAssert\TypedRegex(type="phone_number")
     * @ORM\Column(type="string", length=16, nullable=true)
     */
    private $phone;

    /**
     * @PrestaShopAssert\TypedRegex(type="phone_number")
     * @ORM\Column(type="string", length=16, nullable=true)
     */
    private $fax;

    /**
     * @PrestaShopAssert\Email
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $email;

    /**
     * @ORM\Column(type="boolean")
     */
    private $active;

    /**
     * @ORM\Column(type="datetime")
     */
    private $dateAdd;

    /**
     * @ORM\Column(type="datetime")
     */
    private $dateUpd;

    /**
     * @ORM\ManyToMany(targetEntity="PrestaShopBundle\Entity\Shop", cascade={"persist"})
     * @ORM\JoinTable(
     *      joinColumns={@ORM\JoinColumn(name="id_store", referencedColumnName="id_store")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="id_shop", referencedColumnName="id_shop", onDelete="CASCADE")}
     * )
     */
    private $shops;

    /**
     * @ORM\OneToMany(targetEntity="PrestaShopBundle\Entity\StoreLang", mappedBy="store")
     */
    private $storeLangs;

    public function __construct()
    {
        $this->shops = new ArrayCollection();
        $this->storeLangs = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCountryId(): ?int
    {
        return $this->countryId;
    }

    public function setCountryId(int $countryId): self
    {
        $this->countryId = $countryId;

        return $this;
    }

    public function getStateId(): ?int
    {
        return $this->stateId;
    }

    public function setStateId(?int $stateId): self
    {
        $this->stateId = $stateId;

        return $this;
    }

    public function getPostcode(): ?string
    {
        return $this->postcode;
    }

    public function setPostcode(string $postcode): self
    {
        $this->postcode = $postcode;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(string $city): self
    {
        $this->city = $city;

        return $this;
    }

    public function getLatitude(): ?float
    {
        return $this->latitude;
    }

    public function setLatitude(?float $latitude): self
    {
        $this->latitude = $latitude;

        return $this;
    }

    public function getLongitude(): ?float
    {
        return $this->longitude;
    }

    public function setLongitude(?float $longitude): self
    {
        $this->longitude = $longitude;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    public function getFax(): ?string
    {
        return $this->fax;
    }

    public function setFax(?string $fax): self
    {
        $this->fax = $fax;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getActive(): ?bool
    {
        return $this->active;
    }

    public function setActive(bool $active): self
    {
        $this->active = $active;

        return $this;
    }

    public function getDateAdd(): ?\DateTimeInterface
    {
        return $this->dateAdd;
    }

    public function setDateAdd(\DateTimeInterface $dateAdd): self
    {
        $this->dateAdd = $dateAdd;

        return $this;
    }

    public function getDateUpd(): ?\DateTimeInterface
    {
        return $this->dateUpd;
    }

    public function setDateUpd(\DateTimeInterface $dateUpd): self
    {
        $this->dateUpd = $dateUpd;

        return $this;
    }

    public function addShop(Shop $shop): self
    {
        $this->shops[] = $shop;

        return $this;
    }

    public function removeShop(Shop $shop): self
    {
        $this->shops->removeElement($shop);

        return $this;
    }

    public function getShops(): Collection
    {
        return $this->shops;
    }

    public function addStoreLang(StoreLang $storeLang)
    {
        $this->storeLangs[] = $storeLang;

        $storeLang->setStore($this);

        return $this;
    }

    public function removeStoreLang(StoreLang $storeLang)
    {
        $this->storeLangs->removeElement($storeLang);
    }

    public function getStoreLangs()
    {
        return $this->storeLangs;
    }
}
