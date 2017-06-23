<?php

namespace PrestaShopBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CustomizationFieldLang
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class CustomizationFieldLang
{
    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255, nullable=false)
     */
    private $name;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_customization_field", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $idCustomizationField;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_lang", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $idLang;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_shop", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $idShop;



    /**
     * Set name
     *
     * @param string $name
     *
     * @return CustomizationFieldLang
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set idCustomizationField
     *
     * @param integer $idCustomizationField
     *
     * @return CustomizationFieldLang
     */
    public function setIdCustomizationField($idCustomizationField)
    {
        $this->idCustomizationField = $idCustomizationField;

        return $this;
    }

    /**
     * Get idCustomizationField
     *
     * @return integer
     */
    public function getIdCustomizationField()
    {
        return $this->idCustomizationField;
    }

    /**
     * Set idLang
     *
     * @param integer $idLang
     *
     * @return CustomizationFieldLang
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

    /**
     * Set idShop
     *
     * @param integer $idShop
     *
     * @return CustomizationFieldLang
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
