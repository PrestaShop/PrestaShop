<?php

namespace PrestaShopBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * SpecificPricePriority
 *
 * @ORM\Table(uniqueConstraints={@ORM\UniqueConstraint(name="id_product", columns={"id_product"})})
 * @ORM\Entity
 */
class SpecificPricePriority
{
    /**
     * @var string
     *
     * @ORM\Column(name="priority", type="string", length=80, nullable=false)
     */
    private $priority;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_specific_price_priority", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $idSpecificPricePriority;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_product", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $idProduct;



    /**
     * Set priority
     *
     * @param string $priority
     *
     * @return SpecificPricePriority
     */
    public function setPriority($priority)
    {
        $this->priority = $priority;

        return $this;
    }

    /**
     * Get priority
     *
     * @return string
     */
    public function getPriority()
    {
        return $this->priority;
    }

    /**
     * Set idSpecificPricePriority
     *
     * @param integer $idSpecificPricePriority
     *
     * @return SpecificPricePriority
     */
    public function setIdSpecificPricePriority($idSpecificPricePriority)
    {
        $this->idSpecificPricePriority = $idSpecificPricePriority;

        return $this;
    }

    /**
     * Get idSpecificPricePriority
     *
     * @return integer
     */
    public function getIdSpecificPricePriority()
    {
        return $this->idSpecificPricePriority;
    }

    /**
     * Set idProduct
     *
     * @param integer $idProduct
     *
     * @return SpecificPricePriority
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
