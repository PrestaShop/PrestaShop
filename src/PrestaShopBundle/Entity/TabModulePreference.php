<?php

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
