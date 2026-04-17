<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
class HelperTreeShopsCore extends TreeCore
{
    public const DEFAULT_TEMPLATE = 'tree_shops.tpl';
    public const DEFAULT_NODE_FOLDER_TEMPLATE = 'tree_node_folder_checkbox_shops.tpl';
    public const DEFAULT_NODE_ITEM_TEMPLATE = 'tree_node_item_checkbox_shops.tpl';

    private $_lang;
    private $_selected_shops;

    public function __construct($id, $title = null, $lang = null)
    {
        parent::__construct($id);

        if (isset($title)) {
            $this->setTitle($title);
        }

        $this->setLang($lang);
    }

    public function getData()
    {
        if (!isset($this->_data)) {
            $this->setData(Shop::getTree());
        }

        return $this->_data;
    }

    public function setLang($value)
    {
        $this->_lang = $value;

        return $this;
    }

    public function getLang()
    {
        if (!isset($this->_lang)) {
            $this->setLang($this->getContext()->employee->id_lang);
        }

        return $this->_lang;
    }

    public function getNodeFolderTemplate()
    {
        if (!isset($this->_node_folder_template)) {
            $this->setNodeFolderTemplate(self::DEFAULT_NODE_FOLDER_TEMPLATE);
        }

        return $this->_node_folder_template;
    }

    public function getNodeItemTemplate()
    {
        if (!isset($this->_node_item_template)) {
            $this->setNodeItemTemplate(self::DEFAULT_NODE_ITEM_TEMPLATE);
        }

        return $this->_node_item_template;
    }

    public function setSelectedShops($value)
    {
        if (!is_array($value)) {
            throw new PrestaShopException('Selected shops value must be an array');
        }

        $this->_selected_shops = $value;

        return $this;
    }

    public function getSelectedShops()
    {
        if (!isset($this->_selected_shops)) {
            $this->_selected_shops = [];
        }

        return $this->_selected_shops;
    }

    public function getTemplate()
    {
        if (!isset($this->_template)) {
            $this->setTemplate(self::DEFAULT_TEMPLATE);
        }

        return $this->_template;
    }

    public function render($data = null, $use_default_actions = true, $use_selected_shop = true)
    {
        if (!isset($data)) {
            $data = $this->getData();
        }

        if ($use_default_actions) {
            $this->setActions([
                new TreeToolbarLink(
                    $this->translator->trans('Collapse all', [], 'Admin.Actions'),
                    '#',
                    '$(\'#' . $this->getId() . '\').tree(\'collapseAll\'); return false;',
                    'icon-collapse-alt'
                ),
                new TreeToolbarLink(
                    $this->translator->trans('Expand all', [], 'Admin.Actions'),
                    '#',
                    '$(\'#' . $this->getId() . '\').tree(\'expandAll\'); return false;',
                    'icon-expand-alt'
                ),
                new TreeToolbarLink(
                    $this->translator->trans('Check all', [], 'Admin.Actions'),
                    '#',
                    'checkAllAssociatedShops($(\'#' . $this->getId() . '\')); return false;',
                    'icon-check-sign'
                ),
                new TreeToolbarLink(
                    $this->translator->trans('Uncheck all', [], 'Admin.Actions'),
                    '#',
                    'uncheckAllAssociatedShops($(\'#' . $this->getId() . '\')); return false;',
                    'icon-check-empty'
                ),
            ]);
        }

        if ($use_selected_shop) {
            $this->setAttribute('selected_shops', $this->getSelectedShops());
        }

        return parent::render($data);
    }

    public function renderNodes($data = null)
    {
        if (!isset($data)) {
            $data = $this->getData();
        }

        if (!is_array($data) && !$data instanceof Traversable) {
            throw new PrestaShopException('Data value must be an traversable array');
        }

        $html = '';

        foreach ($data as $item) {
            if (array_key_exists('shops', $item)
                && !empty($item['shops'])) {
                $html .= $this->getContext()->smarty->createTemplate(
                    $this->getTemplateFile($this->getNodeFolderTemplate()),
                    $this->getContext()->smarty
                )->assign($this->getAttributes())->assign([
                    'children' => $this->renderNodes($item['shops']),
                    'node' => $item,
                ])->fetch();
            } else {
                $html .= $this->getContext()->smarty->createTemplate(
                    $this->getTemplateFile($this->getNodeItemTemplate()),
                    $this->getContext()->smarty
                )->assign($this->getAttributes())->assign([
                    'node' => $item,
                ])->fetch();
            }
        }

        return $html;
    }
}
