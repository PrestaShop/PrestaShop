<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
class TreeToolbarSearchCore extends TreeToolbarButtonCore implements ITreeToolbarButtonCore
{
    protected $_template = 'tree_toolbar_search.tpl';

    public function __construct($label, $id, $name = null, $class = null)
    {
        parent::__construct($label);

        $this->setId($id);
        $this->setName($name);
        $this->setClass($class);
    }

    public function render()
    {
        if ($this->hasAttribute('data_search')) {
            $this->setAttribute(
                'typeahead_source',
                $this->_renderData($this->getAttribute('data_search'))
            );
        } elseif ($this->hasAttribute('data')) {
            $this->setAttribute(
                'typeahead_source',
                $this->_renderData($this->getAttribute('data'))
            );
        }

        $admin_webpath = str_ireplace(_PS_CORE_DIR_, '', _PS_ADMIN_DIR_);
        $admin_webpath = preg_replace('/^' . preg_quote(DIRECTORY_SEPARATOR, '/') . '/', '', $admin_webpath);
        $bo_theme = ((Validate::isLoadedObject($this->getContext()->employee)
            && $this->getContext()->employee->bo_theme) ? $this->getContext()->employee->bo_theme : 'default');

        if (!file_exists(_PS_BO_ALL_THEMES_DIR_ . $bo_theme . DIRECTORY_SEPARATOR . 'template')) {
            $bo_theme = 'default';
        }

        if ($this->getContext()->controller->ajax) {
            $html = '<script type="text/javascript" src="' . __PS_BASE_URI__ . $admin_webpath . '/themes/' . $bo_theme . '/js/vendor/typeahead.min.js"></script>';
        } else {
            $this->getContext()->controller->addJs(__PS_BASE_URI__ . $admin_webpath . '/themes/' . $bo_theme . '/js/vendor/typeahead.min.js');
        }

        return (isset($html) ? $html : '') . parent::render();
    }

    private function _renderData($data)
    {
        if (!is_array($data) && !$data instanceof Traversable) {
            throw new PrestaShopException('Data value must be a traversable array');
        }

        $html = '';

        foreach ($data as $item) {
            $html .= json_encode($item) . ',';
            if (array_key_exists('children', $item) && !empty($item['children'])) {
                $html .= $this->_renderData($item['children']);
            }
        }

        return $html;
    }
}
