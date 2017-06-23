<?php

namespace PrestaShopBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CmsCategoryShop
 *
 * @ORM\Table(indexes={@ORM\Index(name="id_shop", columns={"id_shop"})})
 * @ORM\Entity
 */
class CmsCategoryShop
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id_cms_category", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $idCmsCategory;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_shop", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $idShop;



    /**
     * Set idCmsCategory
     *
     * @param integer $idCmsCategory
     *
     * @return CmsCategoryShop
     */
    public function setIdCmsCategory($idCmsCategory)
    {
        $this->idCmsCategory = $idCmsCategory;

        return $this;
    }

    /**
     * Get idCmsCategory
     *
     * @return integer
     */
    public function getIdCmsCategory()
    {
        return $this->idCmsCategory;
    }

    /**
     * Set idShop
     *
     * @param integer $idShop
     *
     * @return CmsCategoryShop
     */
    public function setIdShop($idShop)
    {
        $this->idShop = $idShop;

        return $this;
    }

    /**
     * Get idShop
     *
     * @return integer
     */
    public function getIdShop()
    {
        return $this->idShop;
    }
}
