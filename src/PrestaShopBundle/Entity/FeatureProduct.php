<?php

namespace PrestaShopBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * FeatureProduct
 *
 * @ORM\Table(indexes={@ORM\Index(name="id_feature_value", columns={"id_feature_value"}), @ORM\Index(name="id_product", columns={"id_product"})})
 * @ORM\Entity
 */
class FeatureProduct
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id_feature_value", type="integer", nullable=false)
     */
    private $idFeatureValue;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_feature", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $idFeature;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_product", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $idProduct;



    /**
     * Set idFeatureValue
     *
     * @param integer $idFeatureValue
     *
     * @return FeatureProduct
     */
    public function setIdFeatureValue($idFeatureValue)
    {
        $this->idFeatureValue = $idFeatureValue;

        return $this;
    }

    /**
     * Get idFeatureValue
     *
     * @return integer
     */
    public function getIdFeatureValue()
    {
        return $this->idFeatureValue;
    }

    /**
     * Set idFeature
     *
     * @param integer $idFeature
     *
     * @return FeatureProduct
     */
    public function setIdFeature($idFeature)
    {
        $this->idFeature = $idFeature;

        return $this;
    }

    /**
     * Get idFeature
     *
     * @return integer
     */
    public function getIdFeature()
    {
        return $this->idFeature;
    }

    /**
     * Set idProduct
     *
     * @param integer $idProduct
     *
     * @return FeatureProduct
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
}
