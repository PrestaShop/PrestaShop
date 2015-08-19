<?php
/**
 * 2007-2015 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
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
 *  @author 	PrestaShop SA <contact@prestashop.com>
 *  @copyright  2007-2015 PrestaShop SA
 *  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */

class Core_Business_Employee_FiltersManager implements Core_Business_Employee_FiltersInterface
{
    private $employee;
    private $idShop;
    private $originalData = [];
    private $data = [];

    /**
     * @param object EmployeeCore $employee
     * @param int $idShop is shop
     */
    public function __construct(EmployeeCore $employee, $idShop)
    {
        $this->employee = $employee;
        $this->idShop = $idShop;
        $this->data = $this->originalData = $employee->getFilters($idShop);

        $events = Core_Foundation_Event_EventManager::getInstance();
        $events->attach('onBeforeDbClose', array($this, 'onBeforeDbClose'));

        return $this;
    }

    public function __get($name)
    {
        return isset($this->data[$name]) ? $this->data[$name] : null;
    }

    public function __set($name, $value)
    {
        $this->data[$name] = $value;
    }

    public function __isset($name)
    {
        return isset($this->data[$name]);
    }

    public function __unset($name)
    {
        unset($this->data[$name]);
    }

    public function remove($name)
    {
        $this->__unset($name);
    }

    public function getAll()
    {
        return $this->data;
    }

    public function getAllByPrefix($prefix)
    {
        $data = array();
        if (empty($prefix) || count($this->data) == 0) {
            return $data;
        }

        foreach ($this->data as $key => $value) {
            if (strncmp($key, $prefix, strlen($prefix)) == 0) {
                $data[$key] = $value;
            }
        }

        return $data;
    }

    public function removeAll()
    {
        $this->data = [];
    }

    public function save()
    {
        //save only if there data was updated
        if ($this->originalData !== $this->data) {
            $this->employee->saveFilters($this->idShop, $this->data);
        }
    }

    /* Event onBeforeDbClose */
    public function onBeforeDbClose()
    {
        $this->save();
    }
}
