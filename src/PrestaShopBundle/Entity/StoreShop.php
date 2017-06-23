<?php

namespace PrestaShopBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * StoreShop
 *
 * @ORM\Table(indexes={@ORM\Index(name="id_shop", columns={"id_shop"})})
 * @ORM\Entity
 */
class StoreShop
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id_store", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $idStore;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_shop", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $idShop;



    /**
     * Set idStore
     *
     * @param integer $idStore
     *
     * @return StoreShop
     */
    public function setIdStore($idStore)
    {
        $this->idStore = $idStore;

        return $this;
    }

    /**
     * Get idStore
     *
     * @return integer
     */
    public function getIdStore()
    {
        return $this->idStore;
    }

    /**
     * Set idShop
     *
     * @param integer $idShop
     *
     * @return StoreShop
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
