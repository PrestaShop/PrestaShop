<?php
/*
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

class TreeToolbarSearchCategoriesCore extends TreeToolbarButtonCore implements ITreeToolbarButtonCore
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
        if ($this->hasAttribute('data')) {
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
