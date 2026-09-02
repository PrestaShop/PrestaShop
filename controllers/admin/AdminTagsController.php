<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

/**
 * @deprecated since 9.2 and will be removed in 10.0 - This controller was migrated in 9.2
 *
 * @property Tag $object
 */
class AdminTagsControllerCore extends AdminController
{
    /** @var bool */
    public $bootstrap = true;

    public function __construct()
    {
        $this->table = 'tag';
        $this->className = 'Tag';

        parent::__construct();

        $this->fields_list = [
            'id_tag' => [
                'title' => $this->trans('ID', [], 'Admin.Global'),
                'align' => 'center',
                'class' => 'fixed-width-xs',
            ],
            'lang' => [
                'title' => $this->trans('Language', [], 'Admin.Global'),
                'filter_key' => 'l!name',
            ],
            'name' => [
                'title' => $this->trans('Name', [], 'Admin.Global'),
                'filter_key' => 'a!name',
            ],
            'products' => [
                'title' => $this->trans('Products', [], 'Admin.Global'),
                'align' => 'center',
                'class' => 'fixed-width-xs',
                'havingFilter' => true,
            ],
        ];

        $this->bulk_actions = [
            'delete' => [
                'text' => $this->trans('Delete selected', [], 'Admin.Actions'),
                'icon' => 'icon-trash',
                'confirm' => $this->trans('Delete selected items?', [], 'Admin.Notifications.Warning'),
            ],
        ];
    }

    public function initPageHeaderToolbar()
    {
        if (empty($this->display)) {
            $this->page_header_toolbar_btn['new_tag'] = [
                'href' => self::$currentIndex . '&addtag&token=' . $this->token,
                'desc' => $this->trans('Add new tag', [], 'Admin.Shopparameters.Feature'),
                'icon' => 'process-icon-new',
            ];
        }

        parent::initPageHeaderToolbar();
    }

    public function renderList()
    {
        $this->addRowAction('edit');
        $this->addRowAction('delete');

        $this->_select = 'l.name as lang, COUNT(pt.id_product) as products';
        $this->_join = '
			LEFT JOIN `' . _DB_PREFIX_ . 'product_tag` pt
				ON (a.`id_tag` = pt.`id_tag`)
			LEFT JOIN `' . _DB_PREFIX_ . 'lang` l
				ON (l.`id_lang` = a.`id_lang`)';
        $this->_group = 'GROUP BY a.name, a.id_lang';

        return parent::renderList();
    }

    public function postProcess()
    {
        if ($this->access('edit') && Tools::getValue('submitAdd' . $this->table)) {
            $id = (int) Tools::getValue($this->identifier);
            $obj = new $this->className($id);
            if (Validate::isLoadedObject($obj)) {
                /** @var Tag $obj */
                $previous_products = $obj->getProducts();
                $removed_products = [];

                foreach ($previous_products as $product) {
                    if (!in_array($product['id_product'], $_POST['products'])) {
                        $removed_products[] = $product['id_product'];
                    }
                }

                if (Configuration::get('PS_SEARCH_INDEXATION')) {
                    Search::removeProductsSearchIndex($removed_products);
                }

                $obj->setProducts($_POST['products']);
            }
        }

        return parent::postProcess();
    }

    /**
     * @return string|void
     *
     * @throws SmartyException
     */
    public function renderForm()
    {
        /** @var Tag|null $obj */
        $obj = $this->loadObject(true);
        if (!$obj) {
            return;
        }

        $this->fields_form = [
            'legend' => [
                'title' => $this->trans('Tag', [], 'Admin.Shopparameters.Feature'),
                'icon' => 'icon-tag',
            ],
            'input' => [
                [
                    'type' => 'text',
                    'label' => $this->trans('Name', [], 'Admin.Global'),
                    'name' => 'name',
                    'required' => true,
                ],
                [
                    'type' => 'select',
                    'label' => $this->trans('Language', [], 'Admin.Global'),
                    'name' => 'id_lang',
                    'required' => true,
                    'options' => [
                        'query' => Language::getLanguages(false),
                        'id' => 'id_lang',
                        'name' => 'name',
                    ],
                ],
            ],
            'selects' => [
                'products' => $obj->getProducts(true),
                'products_unselected' => $obj->getProducts(false),
            ],
            'submit' => [
                'title' => $this->trans('Save', [], 'Admin.Actions'),
            ],
        ];

        return parent::renderForm();
    }
}
