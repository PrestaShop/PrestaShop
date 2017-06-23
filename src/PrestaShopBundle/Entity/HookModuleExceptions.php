<?php

namespace PrestaShopBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * HookModuleExceptions
 *
 * @ORM\Table(indexes={@ORM\Index(name="id_module", columns={"id_module"}), @ORM\Index(name="id_hook", columns={"id_hook"})})
 * @ORM\Entity
 */
class HookModuleExceptions
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id_shop", type="integer", nullable=false)
     */
    private $idShop = '1';

    /**
     * @var integer
     *
     * @ORM\Column(name="id_module", type="integer", nullable=false)
     */
    private $idModule;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_hook", type="integer", nullable=false)
     */
    private $idHook;

    /**
     * @var string
     *
     * @ORM\Column(name="file_name", type="string", length=255, nullable=true)
     */
    private $fileName;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_hook_module_exceptions", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idHookModuleExceptions;



    /**
     * Set idShop
     *
     * @param integer $idShop
     *
     * @return HookModuleExceptions
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
     * Set idModule
     *
     * @param integer $idModule
     *
     * @return HookModuleExceptions
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
     * @return HookModuleExceptions
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
     * Set fileName
     *
     * @param string $fileName
     *
     * @return HookModuleExceptions
     */
    public function setFileName($fileName)
    {
        $this->fileName = $fileName;

        return $this;
    }

    /**
     * Get fileName
     *
     * @return string
     */
    public function getFileName()
    {
        return $this->fileName;
    }

    /**
     * Get idHookModuleExceptions
     *
     * @return integer
     */
    public function getIdHookModuleExceptions()
    {
        return $this->idHookModuleExceptions;
    }
}
