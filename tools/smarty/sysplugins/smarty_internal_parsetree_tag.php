<?php
/**
 * Smarty Internal Plugin Templateparser Parse Tree
 * These are classes to build parse tree in the template parser
 *
 * @package    Smarty
 * @subpackage Compiler
 * @author     Thue Kristensen
 * @author     Uwe Tews
 */

/**
 * A complete smarty tag.
 *
 * @package    Smarty
 * @subpackage Compiler
 * @ignore
 */
class Smarty_Internal_ParseTree_Tag extends Smarty_Internal_ParseTree
{

    /**
     * Saved block nesting level
     *
     * @var int
     */
    public $saved_block_nesting;

    /**
     * Create parse tree buffer for Smarty tag
     *
     * @param object $parser parser object
     * @param string $data   content
     */
    public function __construct($parser, $data)
    {
        $this->parser = $parser;
        $this->data = $data;
        $this->saved_block_nesting = $parser->block_nesting_level;
    }

    /**
     * Return buffer content
     *
     * @return string content
     */
    public function to_smarty_php()
    {
        return $this->data;
    }

    /**
     * Return complied code that loads the evaluated output of buffer content into a temporary variable
     *
     * @return string template code
     */
    public function assign_to_var()
    {
        $var = sprintf('$_tmp%d', ++ Smarty_Internal_Templateparser::$prefix_number);
        $tmp = $this->parser->compiler->appendCode('<?php ob_start();?>', $this->data);
        $tmp = $this->parser->compiler->appendCode($tmp, "<?php {$var}=ob_get_clean();?>");
        $this->parser->compiler->prefix_code[] = sprintf("%s", $tmp);

        return $var;
    }
}
