<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use PrestaShop\PrestaShop\Core\Language\LanguageInterface;

/**
 * @ORM\Table()
 *
 * @ORM\Entity(repositoryClass="PrestaShopBundle\Entity\Repository\LangRepository")
 */
class Lang implements LanguageInterface
{
    /**
     * @ORM\Id
     *
     * @ORM\Column(name="id_lang", type="integer")
     *
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private int $id;

    /**
     * @ORM\Column(name="name", type="string", length=32)
     */
    private string $name;

    /**
     * @ORM\Column(name="active", type="boolean")
     */
    private bool $active;

    /**
     * @ORM\Column(name="iso_code", type="string", length=2)
     */
    private string $isoCode;

    /**
     * @ORM\Column(name="language_code", type="string", length=5)
     */
    private string $languageCode;

    /**
     * @ORM\Column(name="locale", type="string", length=5)
     */
    private string $locale;

    /**
     * Badly named, it's not really light. It's just the format for a date only.
     *
     * @ORM\Column(name="date_format_lite", type="string", length=32)
     */
    private string $dateFormatLite;

    /**
     * Badly named, it's not full. It's just the format for a date AND time.
     *
     * @ORM\Column(name="date_format_full", type="string", length=32)
     */
    private string $dateFormatFull;

    /**
     * @ORM\Column(name="is_rtl", type="boolean")
     */
    private bool $isRtl;

    /**
     * @ORM\OneToMany(targetEntity="Translation", mappedBy="lang")
     */
    private Collection $translations;

    /**
     * @ORM\ManyToMany(targetEntity="PrestaShopBundle\Entity\Shop", cascade={"remove", "persist"})
     *
     * @ORM\JoinTable(
     *      joinColumns={@ORM\JoinColumn(name="id_lang", referencedColumnName="id_lang", onDelete="CASCADE")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="id_shop", referencedColumnName="id_shop", onDelete="CASCADE")}
     * )
     */
    private Collection $shops;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->shops = new ArrayCollection();
        $this->translations = new ArrayCollection();
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

    public function setActive(bool $active): static
    {
        $this->active = $active;

        return $this;
    }

    public function getActive(): bool
    {
        return $this->active;
    }

    public function setIsoCode(string $isoCode): static
    {
        $this->isoCode = $isoCode;

        return $this;
    }

    public function getIsoCode(): string
    {
        return $this->isoCode;
    }

    public function setLanguageCode(string $languageCode): static
    {
        $this->languageCode = $languageCode;

        return $this;
    }

    public function getLanguageCode(): string
    {
        return $this->languageCode;
    }

    public function setDateFormatLite(string $dateFormatLite): static
    {
        $this->dateFormatLite = $dateFormatLite;

        return $this;
    }

    public function getDateFormatLite(): string
    {
        return $this->dateFormatLite;
    }

    public function getDateFormat(): string
    {
        return $this->dateFormatLite;
    }

    public function setDateFormatFull(string $dateFormatFull): static
    {
        $this->dateFormatFull = $dateFormatFull;

        return $this;
    }

    public function getDateFormatFull(): string
    {
        return $this->dateFormatFull;
    }

    public function getDateTimeFormat(): string
    {
        return $this->dateFormatFull;
    }

    public function setIsRtl(bool $isRtl): static
    {
        $this->isRtl = $isRtl;

        return $this;
    }

    public function getIsRtl(): bool
    {
        return $this->isRtl;
    }

    public function isRTL(): bool
    {
        return $this->getIsRtl();
    }

    public function getLocale(): string
    {
        return !empty($this->locale) ? $this->locale : $this->getLanguageCode();
    }

    public function setLocale($locale): static
    {
        $this->locale = $locale;

        return $this;
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

    public function getTranslations(): Collection
    {
        return $this->translations;
    }
}
