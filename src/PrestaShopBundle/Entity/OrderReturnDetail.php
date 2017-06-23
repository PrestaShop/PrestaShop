<?php

namespace PrestaShopBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * OrderReturnDetail
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class OrderReturnDetail
{
    /**
     * @var integer
     *
     * @ORM\Column(name="product_quantity", type="integer", nullable=false)
     */
    private $productQuantity = '0';

    /**
     * @var integer
     *
     * @ORM\Column(name="id_order_return", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $idOrderReturn;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_order_detail", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $idOrderDetail;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_customization", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $idCustomization;



    /**
     * Set productQuantity
     *
     * @param integer $productQuantity
     *
     * @return OrderReturnDetail
     */
    public function setProductQuantity($productQuantity)
    {
        $this->productQuantity = $productQuantity;

        return $this;
    }

    /**
     * Get productQuantity
     *
     * @return integer
     */
    public function getProductQuantity()
    {
        return $this->productQuantity;
    }

    /**
     * Set idOrderReturn
     *
     * @param integer $idOrderReturn
     *
     * @return OrderReturnDetail
     */
    public function setIdOrderReturn($idOrderReturn)
    {
        $this->idOrderReturn = $idOrderReturn;

        return $this;
    }

    /**
     * Get idOrderReturn
     *
     * @return integer
     */
    public function getIdOrderReturn()
    {
        return $this->idOrderReturn;
    }

    /**
     * Set idOrderDetail
     *
     * @param integer $idOrderDetail
     *
     * @return OrderReturnDetail
     */
    public function setIdOrderDetail($idOrderDetail)
    {
        $this->idOrderDetail = $idOrderDetail;

        return $this;
    }

    /**
     * Get idOrderDetail
     *
     * @return integer
     */
    public function getIdOrderDetail()
    {
        return $this->idOrderDetail;
    }

    /**
     * Set idCustomization
     *
     * @param integer $idCustomization
     *
     * @return OrderReturnDetail
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
}
