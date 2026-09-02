<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

/**
 * @property SmartyCustom|null $smarty
 */
class SmartyCustomTemplateCore extends Smarty_Internal_Template
{
    public function fetch($template = null, $cache_id = null, $compile_id = null, $parent = null, $display = false, $merge_tpl_vars = true, $no_output_filter = false)
    {
        if ($this->smarty->caching) {
            $tpl = parent::fetch($template, $cache_id, $compile_id, $parent);
            if (property_exists($this, 'cached')) {
                $filepath = str_replace($this->smarty->getCacheDir(), '', $this->cached->filepath);
                if ($this->smarty->is_in_lazy_cache($this->template_resource, $this->cache_id, $this->compile_id) != $filepath) {
                    $this->smarty->update_filepath($filepath, $this->template_resource, $this->cache_id, $this->compile_id);
                }
            }

            return $tpl;
        } else {
            return parent::fetch($template, $cache_id, $compile_id, $parent);
        }
    }
}
