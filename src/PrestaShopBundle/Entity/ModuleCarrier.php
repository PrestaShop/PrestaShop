<?php

namespace PrestaShopBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ModuleCarrier
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class ModuleCarrier
{
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
     * @var integer
     *
     * @ORM\Column(name="id_reference", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $idReference;



    /**
     * Set idModule
     *
     * @param integer $idModule
     *
     * @return ModuleCarrier
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
     * @return ModuleCarrier
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
     * Set idReference
     *
     * @param integer $idReference
     *
     * @return ModuleCarrier
     */
    public function setIdReference($idReference)
    {
        $this->idReference = $idReference;

        return $this;
    }

    /**
     * Get idReference
     *
     * @return integer
     */
    public function getIdReference()
    {
        return $this->idReference;
    }
}
