<?php

namespace PrestaShopBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CustomizedData
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class CustomizedData
{
    /**
     * @var string
     *
     * @ORM\Column(name="value", type="string", length=255, nullable=false)
     */
    private $value;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_module", type="integer", nullable=false)
     */
    private $idModule = '0';

    /**
     * @var string
     *
     * @ORM\Column(name="price", type="decimal", precision=20, scale=6, nullable=false)
     */
    private $price = '0.000000';

    /**
     * @var string
     *
     * @ORM\Column(name="weight", type="decimal", precision=20, scale=6, nullable=false)
     */
    private $weight = '0.000000';

    /**
     * @var integer
     *
     * @ORM\Column(name="id_customization", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $idCustomization;

    /**
     * @var boolean
     *
     * @ORM\Column(name="type", type="boolean")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $type;

    /**
     * @var integer
     *
     * @ORM\Column(name="index", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $index;



    /**
     * Set value
     *
     * @param string $value
     *
     * @return CustomizedData
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
     * Set idModule
     *
     * @param integer $idModule
     *
     * @return CustomizedData
     */
    public function setIdModule($idModule)
    {
        $this->idModule = $idModule;

        return $this;
    }

    /**
     * Get idModule
     *
     * @return integer
     */
    public function getIdModule()
    {
        return $this->idModule;
    }

    /**
     * Set price
     *
     * @param string $price
     *
     * @return CustomizedData
     */
    public function setPrice($price)
    {
        $this->price = $price;

        return $this;
    }

    /**
     * Get price
     *
     * @return string
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * Set weight
     *
     * @param string $weight
     *
     * @return CustomizedData
     */
    public function setWeight($weight)
    {
        $this->weight = $weight;

        return $this;
    }

    /**
     * Get weight
     *
     * @return string
     */
    public function getWeight()
    {
        return $this->weight;
    }

    /**
     * Set idCustomization
     *
     * @param integer $idCustomization
     *
     * @return CustomizedData
     */
    public function setIdCustomization($idCustomization)
    {
        $this->idCustomization = $idCustomization;

        return $this;
    }

    /**
     * Get idCustomization
     *
     * @return integer
     */
    public function getIdCustomization()
    {
        return $this->idCustomization;
    }

    /**
     * Set type
     *
     * @param boolean $type
     *
     * @return CustomizedData
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
     * Set index
     *
     * @param integer $index
     *
     * @return CustomizedData
     */
    public function setIndex($index)
    {
        $this->index = $index;

        return $this;
    }

    /**
     * Get index
     *
     * @return integer
     */
    public function getIndex()
    {
        return $this->index;
    }
}
