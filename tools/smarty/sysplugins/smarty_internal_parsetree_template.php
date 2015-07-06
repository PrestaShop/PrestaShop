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
 * Template element
 *
 * @package    Smarty
 * @subpackage Compiler
 * @ignore
 */
class Smarty_Internal_ParseTree_Template extends Smarty_Internal_ParseTree
{

    /**
     * Array of template elements
     *
     * @var array
     */
    public $subtrees = Array();

    /**
     * Create root of parse tree for template elements
     *
     * @param object $parser parse object
     */
    public function __construct($parser)
    {
        $this->parser = $parser;
    }

    /**
     * Append buffer to subtree
     *
     * @param Smarty_Internal_ParseTree $subtree
     */
    public function append_subtree(Smarty_Internal_ParseTree $subtree)
    {
        if (!empty($subtree->subtrees)) {
            $this->subtrees = array_merge($this->subtrees, $subtree->subtrees);
        } else {
            if ($subtree->data !== '') {
                $this->subtrees[] = $subtree;
            }
        }
    }

    /**
     * Sanitize and merge subtree buffers together
     *
     * @return string template code content
     */
    public function to_smarty_php()
    {
        $code = '';
        for ($key = 0, $cnt = count($this->subtrees); $key < $cnt; $key ++) {
            if ($this->subtrees[$key] instanceof Smarty_Internal_ParseTree_Text) {
                $subtree = $this->subtrees[$key]->to_smarty_php();
                while ($key + 1 < $cnt && ($this->subtrees[$key + 1] instanceof Smarty_Internal_ParseTree_Text || $this->subtrees[$key + 1]->data == '')) {
                    $key ++;
                    if ($this->subtrees[$key]->data == '') {
                        continue;
                    }
                    $subtree .= $this->subtrees[$key]->to_smarty_php();
                }
                if ($subtree == '') {
                    continue;
                }
                $code .= preg_replace('/((<%)|(%>)|(<\?php)|(<\?)|(\?>)|(<\/?script))/', "<?php echo '\$1'; ?>\n", $subtree);
                continue;
            }
            if ($this->subtrees[$key] instanceof Smarty_Internal_ParseTree_Tag) {
                $subtree = $this->subtrees[$key]->to_smarty_php();
                while ($key + 1 < $cnt && ($this->subtrees[$key + 1] instanceof Smarty_Internal_ParseTree_Tag || $this->subtrees[$key + 1]->data == '')) {
                    $key ++;
                    if ($this->subtrees[$key]->data == '') {
                        continue;
                    }
                    $subtree = $this->parser->compiler->appendCode($subtree, $this->subtrees[$key]->to_smarty_php());
                }
                if ($subtree == '') {
                    continue;
                }
                $code .= $subtree;
                continue;
            }
            $code .= $this->subtrees[$key]->to_smarty_php();
        }
        return $code;
    }
}
