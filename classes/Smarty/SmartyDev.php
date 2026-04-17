<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
class SmartyDev extends Smarty
{
    public function __construct()
    {
        parent::__construct();
        $this->template_class = 'SmartyDevTemplate';
    }

    /**
     * {@inheritdoc}
     */
    public function fetch($template = null, $cache_id = null, $compile_id = null, $parent = null, $display = false, $merge_tpl_vars = true, $no_output_filter = false)
    {
        return "\n<!-- begin $template -->\n"
                . parent::fetch($template, $cache_id, $compile_id, $parent)
                . "\n<!-- end $template -->\n";
    }
}
