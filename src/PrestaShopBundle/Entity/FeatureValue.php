<?php

namespace PrestaShopBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * FeatureValue
 *
 * @ORM\Table(indexes={@ORM\Index(name="feature", columns={"id_feature"})})
 * @ORM\Entity
 */
class FeatureValue
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id_feature", type="integer", nullable=false)
     */
    private $idFeature;

    /**
     * @var boolean
     *
     * @ORM\Column(name="custom", type="boolean", nullable=true)
     */
    private $custom;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_feature_value", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idFeatureValue;



    /**
     * Set idFeature
     *
     * @param integer $idFeature
     *
     * @return FeatureValue
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
     * Set custom
     *
     * @param boolean $custom
     *
     * @return FeatureValue
     */
    public function setCustom($custom)
    {
        $this->custom = $custom;

        return $this;
    }

    /**
     * Get custom
     *
     * @return boolean
     */
    public function getCustom()
    {
        return $this->custom;
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
}
