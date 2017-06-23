<?php

namespace PrestaShopBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ModuleShop
 *
 * @ORM\Table(indexes={@ORM\Index(name="id_shop", columns={"id_shop"})})
 * @ORM\Entity
 */
class ModuleShop
{
    /**
     * @var boolean
     *
     * @ORM\Column(name="enable_device", type="boolean", nullable=false)
     */
    private $enableDevice = '7';

    /**
     * @var integer
     *
     * @ORM\Column(name="id_module", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $idModule;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_shop", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $idShop;



    /**
     * Set enableDevice
     *
     * @param boolean $enableDevice
     *
     * @return ModuleShop
     */
    public function setEnableDevice($enableDevice)
    {
        $this->enableDevice = $enableDevice;

        return $this;
    }

    /**
     * Get enableDevice
     *
     * @return boolean
     */
    public function getEnableDevice()
    {
        return $this->enableDevice;
    }

    /**
     * Set idModule
     *
     * @param integer $idModule
     *
     * @return ModuleShop
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
     * Set idShop
     *
     * @param integer $idShop
     *
     * @return ModuleShop
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
