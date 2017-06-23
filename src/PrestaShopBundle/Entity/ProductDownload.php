<?php
/**
 * 2007-2017 PrestaShop
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
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2017 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */


namespace PrestaShopBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ProductDownload
 *
 * @ORM\Table(uniqueConstraints={@ORM\UniqueConstraint(name="id_product", columns={"id_product"})}, indexes={@ORM\Index(name="product_active", columns={"id_product", "active"})})
 * @ORM\Entity
 */
class ProductDownload
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id_product", type="integer", nullable=false)
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
     * @ORM\Column(name="date_add", type="datetime", nullable=false)
     */
    private $dateAdd;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_expiration", type="datetime", nullable=true)
     */
    private $dateExpiration;

    /**
     * @var integer
     *
     * @ORM\Column(name="nb_days_accessible", type="integer", nullable=true)
     */
    private $nbDaysAccessible;

    /**
     * @var integer
     *
     * @ORM\Column(name="nb_downloadable", type="integer", nullable=true)
     */
    private $nbDownloadable = '1';

    /**
     * @var boolean
     *
     * @ORM\Column(name="active", type="boolean", nullable=false)
     */
    private $active = '1';

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_shareable", type="boolean", nullable=false)
     */
    private $isShareable = '0';

    /**
     * @var integer
     *
     * @ORM\Column(name="id_product_download", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idProductDownload;



    /**
     * Set idProduct
     *
     * @param integer $idProduct
     *
     * @return ProductDownload
     */
    public function setIdProduct($idProduct)
    {
        $this->idProduct = $idProduct;

        return $this;
    }

    /**
     * Get idProduct
     *
     * @return integer
     */
    public function getIdProduct()
    {
        return $this->idProduct;
    }

    /**
     * Set displayFilename
     *
     * @param string $displayFilename
     *
     * @return ProductDownload
     */
    public function setDisplayFilename($displayFilename)
    {
        $this->displayFilename = $displayFilename;

        return $this;
    }

    /**
     * Get displayFilename
     *
     * @return string
     */
    public function getDisplayFilename()
    {
        return $this->displayFilename;
    }

    /**
     * Set filename
     *
     * @param string $filename
     *
     * @return ProductDownload
     */
    public function setFilename($filename)
    {
        $this->filename = $filename;

        return $this;
    }

    /**
     * Get filename
     *
     * @return string
     */
    public function getFilename()
    {
        return $this->filename;
    }

    /**
     * Set dateAdd
     *
     * @param \DateTime $dateAdd
     *
     * @return ProductDownload
     */
    public function setDateAdd($dateAdd)
    {
        $this->dateAdd = $dateAdd;

        return $this;
    }

    /**
     * Get dateAdd
     *
     * @return \DateTime
     */
    public function getDateAdd()
    {
        return $this->dateAdd;
    }

    /**
     * Set dateExpiration
     *
     * @param \DateTime $dateExpiration
     *
     * @return ProductDownload
     */
    public function setDateExpiration($dateExpiration)
    {
        $this->dateExpiration = $dateExpiration;

        return $this;
    }

    /**
     * Get dateExpiration
     *
     * @return \DateTime
     */
    public function getDateExpiration()
    {
        return $this->dateExpiration;
    }

    /**
     * Set nbDaysAccessible
     *
     * @param integer $nbDaysAccessible
     *
     * @return ProductDownload
     */
    public function setNbDaysAccessible($nbDaysAccessible)
    {
        $this->nbDaysAccessible = $nbDaysAccessible;

        return $this;
    }

    /**
     * Get nbDaysAccessible
     *
     * @return integer
     */
    public function getNbDaysAccessible()
    {
        return $this->nbDaysAccessible;
    }

    /**
     * Set nbDownloadable
     *
     * @param integer $nbDownloadable
     *
     * @return ProductDownload
     */
    public function setNbDownloadable($nbDownloadable)
    {
        $this->nbDownloadable = $nbDownloadable;

        return $this;
    }

    /**
     * Get nbDownloadable
     *
     * @return integer
     */
    public function getNbDownloadable()
    {
        return $this->nbDownloadable;
    }

    /**
     * Set active
     *
     * @param boolean $active
     *
     * @return ProductDownload
     */
    public function setActive($active)
    {
        $this->active = $active;

        return $this;
    }

    /**
     * Get active
     *
     * @return boolean
     */
    public function getActive()
    {
        return $this->active;
    }

    /**
     * Set isShareable
     *
     * @param boolean $isShareable
     *
     * @return ProductDownload
     */
    public function setIsShareable($isShareable)
    {
        $this->isShareable = $isShareable;

        return $this;
    }

    /**
     * Get isShareable
     *
     * @return boolean
     */
    public function getIsShareable()
    {
        return $this->isShareable;
    }

    /**
     * Get idProductDownload
     *
     * @return integer
     */
    public function getIdProductDownload()
    {
        return $this->idProductDownload;
    }
}
