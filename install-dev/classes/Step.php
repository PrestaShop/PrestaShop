<?php
/**
 * 2007-2016 PrestaShop
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
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2016 PrestaShop SA
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */


class Step
{
    /**
     *
     * @var string
     */
    protected $name;

    /**
     *
     * @var string
     */
    protected $displayName;

    /**
     *
     * @var string
     */
    protected $controllerName;

    /**
     *
     * @var object
     */
    protected $instance;

    /**
     *
     * @return string
     */
    public function __toString()
    {
        return (string) $this->displayName;
    }

    /**
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     *
     * @return string
     */
    public function getdisplayName()
    {
        return $this->displayName;
    }

    /**
     *
     * @return string
     */
    public function getControllerName()
    {
        return $this->controllerName;
    }

    /**
     *
     * @param string $name
     * @return Step
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     *
     * @param string $displayName
     * @return Step
     */
    public function setDisplayName($displayName)
    {
        $this->displayName = $displayName;

        return $this;
    }

    /**
     *
     * @param string $name
     * @return Step
     */
    public function setControllerName($controllerName)
    {
        $this->controllerName = $controllerName;

        return $this;
    }

    /**
     *
     * @return object
     */
    public function getControllerInstance()
    {
        if (null == $this->instance) {
            if (!file_exists(_PS_INSTALL_CONTROLLERS_PATH_.'http/'.$this->name.'.php')) {
                throw new PrestashopInstallerException("Controller file 'http/{$this->name}.php' not found");
            }

            require_once _PS_INSTALL_CONTROLLERS_PATH_.'http/'.$this->name.'.php';

            $this->instance = new $this->controllerName;
        }

        return $this->instance;
    }
}
