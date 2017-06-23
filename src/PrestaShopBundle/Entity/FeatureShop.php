<?php

namespace PrestaShopBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * FeatureShop
 *
 * @ORM\Table(indexes={@ORM\Index(name="id_shop", columns={"id_shop"})})
 * @ORM\Entity
 */
class FeatureShop
{
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
     * @ORM\Column(name="id_shop", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $idShop;



    /**
     * Set idFeature
     *
     * @param integer $idFeature
     *
     * @return FeatureShop
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
     * Set idShop
     *
     * @param integer $idShop
     *
     * @return FeatureShop
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
