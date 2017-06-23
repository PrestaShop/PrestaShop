<?php
/**
 * 2007-2017 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2017 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */


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
