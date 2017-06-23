<?php

namespace PrestaShopBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ProductGroupReductionCache
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class ProductGroupReductionCache
{
    /**
     * @var string
     *
     * @ORM\Column(name="reduction", type="decimal", precision=4, scale=3, nullable=false)
     */
    private $reduction;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_product", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $idProduct;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_group", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $idGroup;



    /**
     * Set reduction
     *
     * @param string $reduction
     *
     * @return ProductGroupReductionCache
     */
    public function setReduction($reduction)
    {
        $this->reduction = $reduction;

        return $this;
    }

    /**
     * Get reduction
     *
     * @return string
     */
    public function getReduction()
    {
        return $this->reduction;
    }

    /**
     * Set idProduct
     *
     * @param integer $idProduct
     *
     * @return ProductGroupReductionCache
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
     * Set idGroup
     *
     * @param integer $idGroup
     *
     * @return ProductGroupReductionCache
     */
    public function setIdGroup($idGroup)
    {
        $this->idGroup = $idGroup;

        return $this;
    }

    /**
     * Get idGroup
     *
     * @return integer
     */
    public function getIdGroup()
    {
        return $this->idGroup;
    }
}
