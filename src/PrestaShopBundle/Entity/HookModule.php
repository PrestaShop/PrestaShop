<?php

namespace PrestaShopBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * HookModule
 *
 * @ORM\Table(indexes={@ORM\Index(name="id_hook", columns={"id_hook"}), @ORM\Index(name="id_module", columns={"id_module"}), @ORM\Index(name="position", columns={"id_shop", "position"})})
 * @ORM\Entity
 */
class HookModule
{
    /**
     * @var boolean
     *
     * @ORM\Column(name="position", type="boolean", nullable=false)
     */
    private $position;

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
     * @ORM\Column(name="id_hook", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $idHook;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_shop", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $idShop;



    /**
     * Set position
     *
     * @param boolean $position
     *
     * @return HookModule
     */
    public function setPosition($position)
    {
        $this->position = $position;

        return $this;
    }

    /**
     * Get position
     *
     * @return boolean
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * Set idModule
     *
     * @param integer $idModule
     *
     * @return HookModule
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
     * Set idHook
     *
     * @param integer $idHook
     *
     * @return HookModule
     */
    public function setIdHook($idHook)
    {
        $this->idHook = $idHook;

        return $this;
    }

    /**
     * Get idHook
     *
     * @return integer
     */
    public function getIdHook()
    {
        return $this->idHook;
    }

    /**
     * Set idShop
     *
     * @param integer $idShop
     *
     * @return HookModule
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
