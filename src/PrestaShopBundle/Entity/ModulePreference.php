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
 * ModulePreference
 *
 * @ORM\Table(uniqueConstraints={@ORM\UniqueConstraint(name="employee_module", columns={"id_employee", "module"})})
 * @ORM\Entity
 */
class ModulePreference
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id_employee", type="integer", nullable=false)
     */
    private $idEmployee;

    /**
     * @var string
     *
     * @ORM\Column(name="module", type="string", length=255, nullable=false)
     */
    private $module;

    /**
     * @var boolean
     *
     * @ORM\Column(name="interest", type="boolean", nullable=true)
     */
    private $interest;

    /**
     * @var boolean
     *
     * @ORM\Column(name="favorite", type="boolean", nullable=true)
     */
    private $favorite;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_module_preference", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idModulePreference;



    /**
     * Set idEmployee
     *
     * @param integer $idEmployee
     *
     * @return ModulePreference
     */
    public function setIdEmployee($idEmployee)
    {
        $this->idEmployee = $idEmployee;

        return $this;
    }

    /**
     * Get idEmployee
     *
     * @return integer
     */
    public function getIdEmployee()
    {
        return $this->idEmployee;
    }

    /**
     * Set module
     *
     * @param string $module
     *
     * @return ModulePreference
     */
    public function setModule($module)
    {
        $this->module = $module;

        return $this;
    }

    /**
     * Get module
     *
     * @return string
     */
    public function getModule()
    {
        return $this->module;
    }

    /**
     * Set interest
     *
     * @param boolean $interest
     *
     * @return ModulePreference
     */
    public function setInterest($interest)
    {
        $this->interest = $interest;

        return $this;
    }

    /**
     * Get interest
     *
     * @return boolean
     */
    public function getInterest()
    {
        return $this->interest;
    }

    /**
     * Set favorite
     *
     * @param boolean $favorite
     *
     * @return ModulePreference
     */
    public function setFavorite($favorite)
    {
        $this->favorite = $favorite;

        return $this;
    }

    /**
     * Get favorite
     *
     * @return boolean
     */
    public function getFavorite()
    {
        return $this->favorite;
    }

    /**
     * Get idModulePreference
     *
     * @return integer
     */
    public function getIdModulePreference()
    {
        return $this->idModulePreference;
    }
}
