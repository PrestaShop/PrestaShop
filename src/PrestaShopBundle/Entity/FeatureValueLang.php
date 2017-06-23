<?php

namespace PrestaShopBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * FeatureValueLang
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class FeatureValueLang
{
    /**
     * @var string
     *
     * @ORM\Column(name="value", type="string", length=255, nullable=true)
     */
    private $value;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_feature_value", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $idFeatureValue;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_lang", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $idLang;



    /**
     * Set value
     *
     * @param string $value
     *
     * @return FeatureValueLang
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }

    /**
     * Get value
     *
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Set idFeatureValue
     *
     * @param integer $idFeatureValue
     *
     * @return FeatureValueLang
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
     * Set idLang
     *
     * @param integer $idLang
     *
     * @return FeatureValueLang
     */
    public function setIdLang($idLang)
    {
        $this->idLang = $idLang;

        return $this;
    }

    /**
     * Get idLang
     *
     * @return integer
     */
    public function getIdLang()
    {
        return $this->idLang;
    }
}
