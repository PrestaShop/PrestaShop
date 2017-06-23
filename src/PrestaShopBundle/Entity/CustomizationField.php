<?php

namespace PrestaShopBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CustomizationField
 *
 * @ORM\Table(indexes={@ORM\Index(name="id_product", columns={"id_product"})})
 * @ORM\Entity
 */
class CustomizationField
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id_product", type="integer", nullable=false)
     */
    private $idProduct;

    /**
     * @var boolean
     *
     * @ORM\Column(name="type", type="boolean", nullable=false)
     */
    private $type;

    /**
     * @var boolean
     *
     * @ORM\Column(name="required", type="boolean", nullable=false)
     */
    private $required;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_module", type="boolean", nullable=false)
     */
    private $isModule = '0';

    /**
     * @var integer
     *
     * @ORM\Column(name="id_customization_field", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idCustomizationField;



    /**
     * Set idProduct
     *
     * @param integer $idProduct
     *
     * @return CustomizationField
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
     * Set type
     *
     * @param boolean $type
     *
     * @return CustomizationField
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return boolean
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set required
     *
     * @param boolean $required
     *
     * @return CustomizationField
     */
    public function setRequired($required)
    {
        $this->required = $required;

        return $this;
    }

    /**
     * Get required
     *
     * @return boolean
     */
    public function getRequired()
    {
        return $this->required;
    }

    /**
     * Set isModule
     *
     * @param boolean $isModule
     *
     * @return CustomizationField
     */
    public function setIsModule($isModule)
    {
        $this->isModule = $isModule;

        return $this;
    }

    /**
     * Get isModule
     *
     * @return boolean
     */
    public function getIsModule()
    {
        return $this->isModule;
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
}
