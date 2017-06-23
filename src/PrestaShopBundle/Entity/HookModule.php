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
