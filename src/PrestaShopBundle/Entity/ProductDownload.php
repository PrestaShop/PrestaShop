<?php
/**
 * 2007-2019 PrestaShop and Contributors
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShopBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table()
 * @ORM\Entity
 */
class ProductDownload
{
    /**
     * @var int
     *
     * @ORM\Id
     * @ORM\Column(name="id_product_download", type="integer", options={"unsigned"=true})
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var int
     *
     * @ORM\Column(name="id_product", type="integer", options={"unsigned"=true})
     */
    private $idProduct;

    /**
     * @var string
     *
     * @ORM\Column(name="display_filename", type="string", length=255, nullable=true)
     */
    private $displayFilename;

    /**
     * @var string
     *
     * @ORM\Column(name="filename", type="string", length=255, nullable=true)
     */
    private $filename;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_add", type="datetime")
     */
    private $dateAdd;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_expiration", type="datetime", nullable=true)
     */
    private $dateExpiration;

    /**
     * @var int
     *
     * @ORM\Column(name="nb_days_accessible", type="integer", nullable=true, options={"unsigned"=true})
     */
    private $nbDaysAccessible;

    /**
     * @var int
     *
     * @ORM\Column(name="nb_downloadable", type="integer", nullable=true, options={"default":1, "unsigned"=true})
     */
    private $nbDownloadable;

    /**
     * @var bool
     *
     * @ORM\Column(name="active", type="boolean", options={"default":1, "unsigned"=true})
     */
    private $active;

    /**
     * @var bool
     *
     * @ORM\Column(name="is_shareable", type="boolean", options={"default":0, "unsigned"=true})
     */
    private $isShareable;

    /**
     * Download ID, different from product ID.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Related product ID.
     *
     * @return int
     */
    public function getIdProduct()
    {
        return $this->idProduct;
    }

    /**
     * Virtual filename, used for display on download.
     *
     * @return string
     */
    public function getDisplayFilename()
    {
        return $this->displayFilename;
    }

    /**
     * Get actual filename on the shop filesystem.
     *
     * @return string
     */
    public function getFilename()
    {
        return $this->filename;
    }

    /**
     * Date when the download was added.
     *
     * @return \DateTime
     */
    public function getDateAdd()
    {
        return $this->dateAdd;
    }

    /**
     * Date until the product can be downloaded.
     *
     * @return string
     */
    public function getDateExpiration()
    {
        return $this->dateExpiration;
    }

    /**
     * Number of days (after order) the product can be downloaded.
     *
     * @return int
     */
    public function getNbDaysAccessible()
    {
        return $this->nbDaysAccessible;
    }

    /**
     * The number of downloads of a product can be limited.
     *
     * @return int
     */
    public function getNbDownloadable()
    {
        return $this->nbDownloadable;
    }

    /**
     * @return bool
     */
    public function getActive()
    {
        return $this->active;
    }

    public function getIsShareable()
    {
        return $this->isShareable;
    }

    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    public function setIdProduct($idProduct)
    {
        $this->idProduct = $idProduct;

        return $this;
    }

    public function setDisplayFilename($displayFilename)
    {
        $this->displayFilename = $displayFilename;

        return $this;
    }

    public function setFilename($filename)
    {
        $this->filename = $filename;

        return $this;
    }

    public function setDateAdd(\DateTime $dateAdd)
    {
        $this->dateAdd = $dateAdd;

        return $this;
    }

    public function setDateExpiration(\DateTime $dateExpiration)
    {
        $this->dateExpiration = $dateExpiration;

        return $this;
    }

    public function setNbDaysAccessible($nbDaysAccessible)
    {
        $this->nbDaysAccessible = $nbDaysAccessible;

        return $this;
    }

    public function setNbDownloadable($nbDownloadable)
    {
        $this->nbDownloadable = $nbDownloadable;

        return $this;
    }

    public function setActive($active)
    {
        $this->active = $active;

        return $this;
    }

    public function setIsShareable($isShareable)
    {
        $this->isShareable = $isShareable;

        return $this;
    }

    /**
     * Now we tell doctrine that before we persist or update we call the updateTimestamps() function.
     *
     * @ORM\PrePersist
     * @ORM\PreUpdate
     */
    public function updateTimestamps()
    {
        if ($this->getDateAdd() == null) {
            $this->setDateAdd(new DateTime());
        }
    }
}
