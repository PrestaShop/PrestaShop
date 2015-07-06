<?php
/**
 * Smarty Internal Plugin Templateparser Parsetrees
 * These are classes to build parsetrees in the template parser
 *
 * @package    Smarty
 * @subpackage Compiler
 * @author     Thue Kristensen
 * @author     Uwe Tews
 */

/**
 * @package    Smarty
 * @subpackage Compiler
 * @ignore
 */
abstract class Smarty_Internal_ParseTree
{

    /**
     * Parser object
     *
     * @var object
     */
    public $parser;

    /**
     * Buffer content
     *
     * @var mixed
     */
    public $data;

    /**
     * Subtree array
     *
     * @var array
     */
    public $subtrees = array();

    /**
     * Return buffer
     *
     * @return string buffer content
     */
    abstract public function to_smarty_php();
}


