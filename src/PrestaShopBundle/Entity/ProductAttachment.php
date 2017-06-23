<?php

namespace PrestaShopBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ProductAttachment
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class ProductAttachment
{
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
     * @ORM\Column(name="id_attachment", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $idAttachment;



    /**
     * Set idProduct
     *
     * @param integer $idProduct
     *
     * @return ProductAttachment
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
     * Set idAttachment
     *
     * @param integer $idAttachment
     *
     * @return ProductAttachment
     */
    public function setIdAttachment($idAttachment)
    {
        $this->idAttachment = $idAttachment;

        return $this;
    }

    /**
     * Get idAttachment
     *
     * @return integer
     */
    public function getIdAttachment()
    {
        return $this->idAttachment;
    }
}
