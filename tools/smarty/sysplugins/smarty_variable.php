<?php

/**
 * class for the Smarty variable object
 * This class defines the Smarty variable object
 *
 * @package    Smarty
 * @subpackage Template
 */
class Smarty_Variable
{
    /**
     * template variable
     *
     * @var mixed
     */
    public $value = null;
    /**
     * if true any output of this variable will be not cached
     *
     * @var boolean
     */
    public $nocache = false;
    /**
     * the scope the variable will have  (local,parent or root)
     *
     * @var int
     */
    public $scope = Smarty::SCOPE_LOCAL;

    /**
     * create Smarty variable object
     *
     * @param mixed   $value   the value to assign
     * @param boolean $nocache if true any output of this variable will be not cached
     * @param int     $scope   the scope the variable will have  (local,parent or root)
     */
    public function __construct($value = null, $nocache = false, $scope = Smarty::SCOPE_LOCAL)
    {
        $this->value = $value;
        $this->nocache = $nocache;
        $this->scope = $scope;
    }

    /**
     * <<magic>> String conversion
     *
     * @return string
     */
    public function __toString()
    {
        return (string) $this->value;
    }
}

