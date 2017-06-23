<?php

namespace PrestaShopBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CarrierLang
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class CarrierLang
{
    /**
     * @var string
     *
     * @ORM\Column(name="delay", type="string", length=512, nullable=true)
     */
    private $delay;

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
     * @var integer
     *
     * @ORM\Column(name="id_carrier", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $idCarrier;



    /**
     * Set delay
     *
     * @param string $delay
     *
     * @return CarrierLang
     */
    public function setDelay($delay)
    {
        $this->delay = $delay;

        return $this;
    }

    /**
     * Get delay
     *
     * @return string
     */
    public function getDelay()
    {
        return $this->delay;
    }

    /**
     * Set idLang
     *
     * @param integer $idLang
     *
     * @return CarrierLang
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
     * @return CarrierLang
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

    /**
     * Set idCarrier
     *
     * @param integer $idCarrier
     *
     * @return CarrierLang
     */
    public function setIdCarrier($idCarrier)
    {
        $this->idCarrier = $idCarrier;

        return $this;
    }

    /**
     * Get idCarrier
     *
     * @return integer
     */
    public function getIdCarrier()
    {
        return $this->idCarrier;
    }
}
