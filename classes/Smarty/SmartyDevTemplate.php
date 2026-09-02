<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

/**
 * @property SmartyCustom|null $smarty
 */
class SmartyDevTemplateCore extends Smarty_Internal_Template
{
    /**
     * @param SmartyDevTemplateCore|null $template
     * @param null $cache_id
     * @param null $compile_id
     * @param object $parent
     * @param false $display
     * @param bool $merge_tpl_vars
     * @param false $no_output_filter
     *
     * @return string
     *
     * @throws SmartyException
     */
    // @phpstan-ignore-next-line
    public function fetch($template = null, $cache_id = null, $compile_id = null, $parent = null, $display = false, $merge_tpl_vars = true, $no_output_filter = false)
    {
        if (null !== $template) {
            $tpl = $template->template_resource;
        } else {
            $tpl = $this->template_resource;
        }

        // @phpstan-ignore-next-line
        $fetch = parent::fetch($template, $cache_id, $compile_id, $parent);

        return "\n<!-- begin $tpl -->\n" . $fetch . "\n<!-- end $tpl -->\n";
    }
}
