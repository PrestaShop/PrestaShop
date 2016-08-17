<?php

namespace PrestaShopBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Lang
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="PrestaShopBundle\Entity\LangRepository")
 */
class Lang
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id_lang", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=32)
     */
    private $name;

    /**
     * @var integer
     *
     * @ORM\Column(name="active", type="integer")
     */
    private $active;

    /**
     * @var string
     *
     * @ORM\Column(name="iso_code", type="string", length=2)
     */
    private $isoCode;

    /**
     * @var string
     *
     * @ORM\Column(name="language_code", type="string", length=5)
     */
    private $languageCode;

    /**
     * @var string
     *
     * @ORM\Column(name="locale", type="string", length=5)
     */
    private $locale;

    /**
     * @var string
     *
     * @ORM\Column(name="date_format_lite", type="string", length=32)
     */
    private $dateFormatLite;

    /**
     * @var string
     *
     * @ORM\Column(name="date_format_full", type="string", length=32)
     */
    private $dateFormatFull;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_rtl", type="boolean")
     */
    private $isRtl;

    /**
     * @ORM\OneToMany(targetEntity="Translation", mappedBy="lang")
     */
    private $translations;

    /**
     * @ORM\ManyToMany(targetEntity="PrestaShopBundle\Entity\Shop", cascade={"remove", "persist"})
     * @ORM\JoinTable(
     *      joinColumns={@ORM\JoinColumn(name="id_lang", referencedColumnName="id_lang", onDelete="CASCADE")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="id_shop", referencedColumnName="id_shop")}
     * )
     *
     */
    private $shops;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->shops = new ArrayCollection();
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return Lang
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set active
     *
     * @param integer $active
     *
     * @return Lang
     */
    public function setActive($active)
    {
        $this->active = $active;

        return $this;
    }

    /**
     * Get active
     *
     * @return integer
     */
    public function getActive()
    {
        return $this->active;
    }

    /**
     * Set isoCode
     *
     * @param string $isoCode
     *
     * @return Lang
     */
    public function setIsoCode($isoCode)
    {
        $this->isoCode = $isoCode;

        return $this;
    }

    /**
     * Get isoCode
     *
     * @return string
     */
    public function getIsoCode()
    {
        return $this->isoCode;
    }

    /**
     * Set languageCode
     *
     * @param string $languageCode
     *
     * @return Lang
     */
    public function setLanguageCode($languageCode)
    {
        $this->languageCode = $languageCode;

        return $this;
    }

    /**
     * Get languageCode
     *
     * @return string
     */
    public function getLanguageCode()
    {
        return $this->languageCode;
    }

    /**
     * Set dateFormatLite
     *
     * @param string $dateFormatLite
     *
     * @return Lang
     */
    public function setDateFormatLite($dateFormatLite)
    {
        $this->dateFormatLite = $dateFormatLite;

        return $this;
    }

    /**
     * Get dateFormatLite
     *
     * @return string
     */
    public function getDateFormatLite()
    {
        return $this->dateFormatLite;
    }

    /**
     * Set dateFormatFull
     *
     * @param string $dateFormatFull
     *
     * @return Lang
     */
    public function setDateFormatFull($dateFormatFull)
    {
        $this->dateFormatFull = $dateFormatFull;

        return $this;
    }

    /**
     * Get dateFormatFull
     *
     * @return string
     */
    public function getDateFormatFull()
    {
        return $this->dateFormatFull;
    }

    /**
     * Set isRtl
     *
     * @param boolean $isRtl
     *
     * @return Lang
     */
    public function setIsRtl($isRtl)
    {
        $this->isRtl = $isRtl;

        return $this;
    }

    /**
     * Get isRtl
     *
     * @return boolean
     */
    public function getIsRtl()
    {
        return $this->isRtl;
    }

    /**
     *
     * @return string
     */
    public function getLocale()
    {
        return $this->locale;
    }

    /**
     *
     * @param string $locale
     * @return Lang
     */
    public function setLocale($locale)
    {
        $this->locale = $locale;

        return $this;
    }

    /**
     * Add shop
     *
     * @param \PrestaShopBundle\Entity\Shop $shop
     *
     * @return Attribute
     */
    public function addShop(\PrestaShopBundle\Entity\Shop $shop)
    {
        $this->shops[] = $shop;

        return $this;
    }

    /**
     * Remove shop
     *
     * @param \PrestaShopBundle\Entity\Shop $shop
     */
    public function removeShop(\PrestaShopBundle\Entity\Shop $shop)
    {
        $this->shops->removeElement($shop);
    }

    /**
     * Get shops
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getShops()
    {
        return $this->shops;
    }
}
