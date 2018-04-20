<?php

namespace PrestaShop\PrestaShop\Core\Table;

/**
 * Class RowAction holds information related to row action for each row element
 */
final class RowAction
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var callable
     */
    private $callback;

    /**
     * @var string
     */
    private $icon = '';

    /**
     * @var string
     */
    private $action;

    /**
     * @param string $identifier    Action identifier should be unique between all table row actions
     * @param string $name          Translated action name
     * @param callable $callback    Action collback
     * @param string $icon     Action icon name
     */
    public function __construct($identifier, $name, callable $callback, $icon = '')
    {
        $this->name = $name;
        $this->callback = $callback;
        $this->icon = $icon;
        $this->action = $identifier;
    }

    /**
     * @return string
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
     * @return string|null
     */
    public function getIcon()
    {
        return $this->icon;
    }

    /**
     * @return string
     */
    public function getIdentifier()
    {
        return $this->action;
    }
}
