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

use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 *
 * @ORM\Table(
 *     indexes={@ORM\Index(name="product_active", columns={"id_product", "active"})},
 *     uniqueConstraints={@ORM\UniqueConstraint(name="id_product", columns={"id_product"})}
 * )
 */
class ProductDownload
{
    /**
     * @ORM\Id
     *
     * @ORM\Column(name="id_product_download", type="integer", options={"unsigned"=true})
     *
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private int $id;

    /**
     * @ORM\Column(name="id_product", type="integer", options={"unsigned"=true})
     */
    private int $idProduct;

    /**
     * @ORM\Column(name="display_filename", type="string", length=255, nullable=true)
     */
    private ?string $displayFilename;

    /**
     * @ORM\Column(name="filename", type="string", length=255, nullable=true)
     */
    private ?string $filename;

    /**
     * @ORM\Column(name="date_add", type="datetime")
     */
    private DateTime $dateAdd;

    /**
     * @ORM\Column(name="date_expiration", type="datetime", nullable=true)
     */
    private ?DateTime $dateExpiration;

    /**
     * @ORM\Column(name="nb_days_accessible", type="integer", nullable=true, options={"unsigned"=true})
     */
    private ?int $nbDaysAccessible;

    /**
     * @ORM\Column(name="nb_downloadable", type="integer", nullable=true, options={"default":1, "unsigned"=true})
     */
    private ?int $nbDownloadable;

    /**
     * @ORM\Column(name="active", type="boolean", options={"default":1, "unsigned"=true})
     */
    private bool $active;

    /**
     * @ORM\Column(name="is_shareable", type="boolean", options={"default":0, "unsigned"=true})
     */
    private bool $isShareable;

    /**
     * Download ID, different from product ID.
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * Related product ID.
     */
    public function getIdProduct(): int
    {
        return $this->idProduct;
    }

    /**
     * Virtual filename, used for display on download.
     */
    public function getDisplayFilename(): ?string
    {
        return $this->displayFilename;
    }

    /**
     * Get actual filename on the shop filesystem.
     */
    public function getFilename(): ?string
    {
        return $this->filename;
    }

    /**
     * Date when the download was added.
     */
    public function getDateAdd(): DateTime
    {
        return $this->dateAdd;
    }

    /**
     * Date until the product can be downloaded.
     */
    public function getDateExpiration(): ?DateTime
    {
        return $this->dateExpiration;
    }

    /**
     * Number of days (after order) the product can be downloaded.
     */
    public function getNbDaysAccessible(): ?int
    {
        return $this->nbDaysAccessible;
    }

    /**
     * The number of downloads of a product can be limited.
     */
    public function getNbDownloadable(): int
    {
        return $this->nbDownloadable;
    }

    public function getActive(): bool
    {
        return $this->active;
    }

    public function getIsShareable(): bool
    {
        return $this->isShareable;
    }

    public function setIdProduct(int $idProduct): static
    {
        $this->idProduct = $idProduct;

        return $this;
    }

    public function setDisplayFilename(?string $displayFilename): static
    {
        $this->displayFilename = $displayFilename;

        return $this;
    }

    public function setFilename(?string $filename): static
    {
        $this->filename = $filename;

        return $this;
    }

    public function setDateAdd(DateTime $dateAdd): static
    {
        $this->dateAdd = $dateAdd;

        return $this;
    }

    public function setDateExpiration(?DateTime $dateExpiration): static
    {
        $this->dateExpiration = $dateExpiration;

        return $this;
    }

    public function setNbDaysAccessible(?int $nbDaysAccessible): static
    {
        $this->nbDaysAccessible = $nbDaysAccessible;

        return $this;
    }

    public function setNbDownloadable(?int $nbDownloadable): static
    {
        $this->nbDownloadable = $nbDownloadable;

        return $this;
    }

    public function setActive(bool $active): static
    {
        $this->active = $active;

        return $this;
    }

    public function setIsShareable(bool $isShareable): static
    {
        $this->isShareable = $isShareable;

        return $this;
    }

    /**
     * Now we tell doctrine that before we persist or update we call the updateTimestamps() function.
     *
     * @ORM\PrePersist
     *
     * @ORM\PreUpdate
     */
    public function updateTimestamps(): void
    {
        if (!isset($this->dateAdd)) {
            $this->dateAdd = new DateTime();
        }
    }
}
