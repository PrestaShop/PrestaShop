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
use Doctrine\ORM\Mapping as ORM;
use PrestaShop\PrestaShop\Core\Language\LanguageInterface;

/**
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="PrestaShopBundle\Entity\Repository\LangRepository")
 */
class Lang implements LanguageInterface
{
    /**
     * @var int
     *
     * @ORM\Id
     * @ORM\Column(name="id_lang", type="integer")
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
     * @var int
     *
     * @ORM\Column(name="active", type="boolean")
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
     * @var bool
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
     *      inverseJoinColumns={@ORM\JoinColumn(name="id_shop", referencedColumnName="id_shop", onDelete="CASCADE")}
     * )
     */
    private $shops;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->shops = new ArrayCollection();
    }

    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name.
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
     * Get name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set active.
     *
     * @param int $active
     *
     * @return Lang
     */
    public function setActive($active)
    {
        $this->active = $active;

        return $this;
    }

    /**
     * Get active.
     *
     * @return int
     */
    public function getActive()
    {
        return $this->active;
    }

    /**
     * Set isoCode.
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
     * Get isoCode.
     *
     * @return string
     */
    public function getIsoCode()
    {
        return $this->isoCode;
    }

    /**
     * Set languageCode.
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
     * Get languageCode.
     *
     * @return string
     */
    public function getLanguageCode()
    {
        return $this->languageCode;
    }

    /**
     * Set dateFormatLite.
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
     * Get dateFormatLite.
     *
     * @return string
     */
    public function getDateFormatLite()
    {
        return $this->dateFormatLite;
    }

    /**
     * Set dateFormatFull.
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
     * Get dateFormatFull.
     *
     * @return string
     */
    public function getDateFormatFull()
    {
        return $this->dateFormatFull;
    }

    /**
     * Set isRtl.
     *
     * @param bool $isRtl
     *
     * @return Lang
     */
    public function setIsRtl($isRtl)
    {
        $this->isRtl = $isRtl;

        return $this;
    }

    /**
     * Get isRtl.
     *
     * @return bool
     */
    public function getIsRtl()
    {
        return $this->isRtl;
    }

    /**
     * {@inheritdoc}
     */
    public function isRTL()
    {
        return $this->getIsRtl();
    }

    /**
     * @return string
     */
    public function getLocale()
    {
        return !empty($this->locale) ? $this->locale : $this->getLanguageCode();
    }

    /**
     * @param string $locale
     *
     * @return Lang
     */
    public function setLocale($locale)
    {
        $this->locale = $locale;

        return $this;
    }

    /**
     * Add shop.
     *
     * @param \PrestaShopBundle\Entity\Shop $shop
     *
     * @return Lang
     */
    public function addShop(Shop $shop)
    {
        $this->shops[] = $shop;

        return $this;
    }

    /**
     * Remove shop.
     *
     * @param \PrestaShopBundle\Entity\Shop $shop
     */
    public function removeShop(Shop $shop)
    {
        $this->shops->removeElement($shop);
    }

    /**
     * Get shops.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getShops()
    {
        return $this->shops;
    }
}
