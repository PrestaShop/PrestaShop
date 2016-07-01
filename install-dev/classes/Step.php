<?php

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
