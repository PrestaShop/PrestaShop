<?php

namespace PrestaShop\PrestaShop\Core\Table;

final class RowAction
{
    private $name;

    /**
     * @var callable
     */
    private $callback;

    private $icon;

    private $action;

    public function __construct($action, $name, callable $callback, $icon)
    {
        $this->name = $name;
        $this->callback = $callback;
        $this->icon = $icon;
        $this->action = $action;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return callable
     */
    public function getCallback()
    {
        return $this->callback;
    }

    /**
     * @return mixed
     */
    public function getIcon()
    {
        return $this->icon;
    }

    /**
     * @return mixed
     */
    public function getAction()
    {
        return $this->action;
    }
}