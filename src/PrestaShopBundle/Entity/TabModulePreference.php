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
 * TabModulePreference
 *
 * @ORM\Table(uniqueConstraints={@ORM\UniqueConstraint(name="employee_module", columns={"id_employee", "id_tab", "module"})})
 * @ORM\Entity
 */
class TabModulePreference
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id_employee", type="integer", nullable=false)
     */
    private $idEmployee;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_tab", type="integer", nullable=false)
     */
    private $idTab;

    /**
     * @var string
     *
     * @ORM\Column(name="module", type="string", length=255, nullable=false)
     */
    private $module;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_tab_module_preference", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idTabModulePreference;



    /**
     * Set idEmployee
     *
     * @param integer $idEmployee
     *
     * @return TabModulePreference
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
     * Set idTab
     *
     * @param integer $idTab
     *
     * @return TabModulePreference
     */
    public function setIdTab($idTab)
    {
        $this->idTab = $idTab;

        return $this;
    }

    /**
     * Get idTab
     *
     * @return integer
     */
    public function getIdTab()
    {
        return $this->idTab;
    }

    /**
     * Set module
     *
     * @param string $module
     *
     * @return TabModulePreference
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
     * Get idTabModulePreference
     *
     * @return integer
     */
    public function getIdTabModulePreference()
    {
        return $this->idTabModulePreference;
    }
}
