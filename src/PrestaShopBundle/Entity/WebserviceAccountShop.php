<?php

namespace PrestaShopBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * WebserviceAccountShop
 *
 * @ORM\Table(indexes={@ORM\Index(name="id_shop", columns={"id_shop"})})
 * @ORM\Entity
 */
class WebserviceAccountShop
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id_webservice_account", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $idWebserviceAccount;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_shop", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $idShop;



    /**
     * Set idWebserviceAccount
     *
     * @param integer $idWebserviceAccount
     *
     * @return WebserviceAccountShop
     */
    public function setIdWebserviceAccount($idWebserviceAccount)
    {
        $this->idWebserviceAccount = $idWebserviceAccount;

        return $this;
    }

    /**
     * Get idWebserviceAccount
     *
     * @return integer
     */
    public function getIdWebserviceAccount()
    {
        return $this->idWebserviceAccount;
    }

    /**
     * Set idShop
     *
     * @param integer $idShop
     *
     * @return WebserviceAccountShop
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
