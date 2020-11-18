<?php
/**
 * 2007-2019 PrestaShop and Contributors
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

/**
 * @property Alias $object
 */
class AdminSearchConfControllerCore extends AdminController
{
    protected $toolbar_scroll = false;

    public function __construct()
    {
        $this->bootstrap = true;
        $this->table = 'alias';
        $this->className = 'Alias';
        $this->lang = false;

        parent::__construct();

        // Alias fields
        $this->addRowAction('edit');
        $this->addRowAction('delete');

        if (!Tools::getValue('realedit')) {
            $this->deleted = false;
        }

        $this->bulk_actions = array(
            'delete' => array(
                'text' => $this->trans('Delete selected', array(), 'Admin.Actions'),
                'confirm' => $this->trans('Delete selected items?', array(), 'Admin.Notifications.Info'),
                'icon' => 'icon-trash',
            ),
        );

        $this->fields_list = array(
            'alias' => array('title' => $this->trans('Aliases', array(), 'Admin.Shopparameters.Feature')),
            // Search is a noum here.
            'search' => array('title' => $this->trans('Search', array(), 'Admin.Shopparameters.Feature')),
            'active' => array('title' => $this->trans('Status', array(), 'Admin.Global'), 'class' => 'fixed-width-sm', 'align' => 'center', 'active' => 'status', 'type' => 'bool', 'orderby' => false),
        );

        $params = [
            'action' => 'searchCron',
            'ajax' => 1,
            'full' => 1,
            'token' => $this->getTokenForCron(),
        ];
        if (Shop::getContext() == Shop::CONTEXT_SHOP) {
            $params['id_shop'] = (int) Context::getContext()->shop->id;
        }

        // Search options
        $cron_url = Context::getContext()->link->getAdminLink(
            'AdminSearch',
            false,
            [],
            $params
        );

        list($total, $indexed) = Db::getInstance()->getRow('SELECT COUNT(*) as "0", SUM(product_shop.indexed) as "1" FROM ' . _DB_PREFIX_ . 'product p ' . Shop::addSqlAssociation('product', 'p') . ' WHERE product_shop.`visibility` IN ("both", "search") AND product_shop.`active` = 1');

        $this->fields_options = array(
            'indexation' => array(
                'title' => $this->trans('Indexing', array(), 'Admin.Shopparameters.Feature'),
                'icon' => 'icon-cogs',
                'info' => '<p>
						' . $this->trans('The "indexed" products have been analyzed by PrestaShop and will appear in the results of a front office search.', array(), 'Admin.Shopparameters.Feature') . '<br />
						' . $this->trans('Indexed products', array(), 'Admin.Shopparameters.Feature') . ' <strong>' . (int) $indexed . ' / ' . (int) $total . '</strong>.
					</p>
					<p>
						' . $this->trans('Building the product index may take a few minutes.', array(), 'Admin.Shopparameters.Feature') . '
						' . $this->trans('If your server stops before the process ends, you can resume the indexing by clicking "Add missing products to the index".', array(), 'Admin.Shopparameters.Feature') . '
					</p>
					<a href="' . Context::getContext()->link->getAdminLink('AdminSearch', false) . '&action=searchCron&ajax=1&token=' . $this->getTokenForCron() . '&amp;redirect=1' . (Shop::getContext() == Shop::CONTEXT_SHOP ? '&id_shop=' . (int) Context::getContext()->shop->id : '') . '" class="btn-link">
						<i class="icon-external-link-sign"></i>
						' . $this->trans('Add missing products to the index', array(), 'Admin.Shopparameters.Feature') . '
					</a><br />
					<a href="' . Context::getContext()->link->getAdminLink('AdminSearch', false) . '&action=searchCron&ajax=1&full=1&amp;token=' . $this->getTokenForCron() . '&amp;redirect=1' . (Shop::getContext() == Shop::CONTEXT_SHOP ? '&id_shop=' . (int) Context::getContext()->shop->id : '') . '" class="btn-link">
						<i class="icon-external-link-sign"></i>
						' . $this->trans('Re-build the entire index', array(), 'Admin.Shopparameters.Feature') . '
					</a><br /><br />
					<p>
						' . $this->trans('You can set a cron job that will rebuild your index using the following URL:', array(), 'Admin.Shopparameters.Feature') . '<br />
						<a href="' . Tools::safeOutput($cron_url) . '">
							<i class="icon-external-link-sign"></i>
							' . Tools::safeOutput($cron_url) . '
						</a>
					</p><br />',
                'fields' => array(
                    'PS_SEARCH_INDEXATION' => array(
                        'title' => $this->trans('Indexing', array(), 'Admin.Shopparameters.Feature'),
                        'validation' => 'isBool',
                        'type' => 'bool',
                        'cast' => 'intval',
                        'desc' => $this->trans('Enable the automatic indexing of products. If you enable this feature, the products will be indexed in the search automatically when they are saved. If the feature is disabled, you will have to index products manually by using the links provided in the field set.', array(), 'Admin.Shopparameters.Help'),
                    ),
                ),
                'submit' => array('title' => $this->trans('Save', array(), 'Admin.Actions')),
            ),
            'search' => array(
                'title' => $this->trans('Search', array(), 'Admin.Shopparameters.Feature'),
                'icon' => 'icon-search',
                'fields' => array(
                    'PS_SEARCH_START' => array(
                        'title' => $this->trans('Search within word', array(), 'Admin.Shopparameters.Feature'),
                        'validation' => 'isBool',
                        'cast' => 'intval',
                        'type' => 'bool',
                        'desc' => $this->trans(
                                'By default, to search for “blouse”, you have to enter “blous”, “blo”, etc (beginning of the word) – but not “lous” (within the word).',
                                array(),
                                'Admin.Shopparameters.Help'
                            ) . '<br/>' .
                            $this->trans(
                                'With this option enabled, it also gives the good result if you search for “lous”, “ouse”, or anything contained in the word.',
                                array(),
                                'Admin.Shopparameters.Help'
                            ),
                        'hint' => array(
                            $this->trans(
                                'Enable search within a whole word, rather than from its beginning only.',
                                array(),
                                'Admin.Shopparameters.Help'
                            ),
                            $this->trans(
                                'It checks if the searched term is contained in the indexed word. This may be resource-consuming.',
                                array(),
                                'Admin.Shopparameters.Help'
                            ),
                        ),
                    ),
                    'PS_SEARCH_END' => array(
                        'title' => $this->trans('Search exact end match', array(), 'Admin.Shopparameters.Feature'),
                        'validation' => 'isBool',
                        'cast' => 'intval',
                        'type' => 'bool',
                        'desc' => $this->trans(
                                'By default, if you search "book", you will have "book", "bookcase" and "bookend".',
                                array(),
                                'Admin.Shopparameters.Help'
                            ) . '<br/>' .
                            $this->trans(
                                'With this option enabled, it only gives one result “book”, as exact end of the indexed word is matching.',
                                array(),
                                'Admin.Shopparameters.Help'
                            ),
                        'hint' => array(
                            $this->trans(
                                'Enable more precise search with the end of the word.',
                                array(),
                                'Admin.Shopparameters.Help'
                            ),
                            $this->trans(
                                'It checks if the searched term is the exact end of the indexed word.',
                                array(),
                                'Admin.Shopparameters.Help'
                            ),
                        ),
                    ),
                    'PS_SEARCH_MINWORDLEN' => array(
                        'title' => $this->trans(
                            'Minimum word length (in characters)',
                            array(),
                            'Admin.Shopparameters.Feature'
                        ),
                        'hint' => $this->trans(
                            'Only words this size or larger will be indexed.',
                            array(),
                            'Admin.Shopparameters.Help'
                        ),
                        'validation' => 'isUnsignedInt',
                        'type' => 'text',
                        'cast' => 'intval',
                    ),
                    'PS_SEARCH_BLACKLIST' => array(
                        'title' => $this->trans('Blacklisted words', array(), 'Admin.Shopparameters.Feature'),
                        'validation' => 'isGenericName',
                        'hint' => $this->trans(
                            'Please enter the index words separated by a "|".',
                            array(),
                            'Admin.Shopparameters.Help'
                        ),
                        'type' => 'textareaLang',
                    ),
                ),
                'submit' => array('title' => $this->trans('Save', array(), 'Admin.Actions')),
            ),
            'relevance' => array(
                'title' => $this->trans('Weight', array(), 'Admin.Shopparameters.Feature'),
                'icon' => 'icon-cogs',
                'info' => $this->trans(
                        'The "weight" represents its importance and relevance for the ranking of the products when completing a new search.',
                        array(),
                        'Admin.Shopparameters.Feature'
                    ) . '<br />
						' . $this->trans(
                        'A word with a weight of eight will have four times more value than a word with a weight of two.',
                        array(),
                        'Admin.Shopparameters.Feature'
                    ) . '<br /><br />
						' . $this->trans(
                        'We advise you to set a greater weight for words which appear in the name or reference of a product. This will allow the search results to be as precise and relevant as possible.',
                        array(),
                        'Admin.Shopparameters.Feature'
                    ) . '<br /><br />
						' . $this->trans(
                        'Setting a weight to 0 will exclude that field from search index. Re-build of the entire index is required when changing to or from 0',
                        array(),
                        'Admin.Shopparameters.Feature'
                    ),
                'fields' => array(
                    'PS_SEARCH_WEIGHT_PNAME' => array(
                        'title' => $this->trans('Product name weight', array(), 'Admin.Shopparameters.Feature'),
                        'validation' => 'isUnsignedInt',
                        'type' => 'text',
                        'cast' => 'intval',
                    ),
                    'PS_SEARCH_WEIGHT_REF' => array(
                        'title' => $this->trans('Reference weight', array(), 'Admin.Shopparameters.Feature'),
                        'validation' => 'isUnsignedInt',
                        'type' => 'text',
                        'cast' => 'intval',
                    ),
                    'PS_SEARCH_WEIGHT_SHORTDESC' => array(
                        'title' => $this->trans(
                            'Short description weight',
                            array(),
                            'Admin.Shopparameters.Feature'
                        ),
                        'validation' => 'isUnsignedInt',
                        'type' => 'text',
                        'cast' => 'intval',
                    ),
                    'PS_SEARCH_WEIGHT_DESC' => array(
                        'title' => $this->trans('Description weight', array(), 'Admin.Shopparameters.Feature'),
                        'validation' => 'isUnsignedInt',
                        'type' => 'text',
                        'cast' => 'intval',
                    ),
                    'PS_SEARCH_WEIGHT_CNAME' => array(
                        'title' => $this->trans('Category weight', array(), 'Admin.Shopparameters.Feature'),
                        'validation' => 'isUnsignedInt',
                        'type' => 'text',
                        'cast' => 'intval',
                    ),
                    'PS_SEARCH_WEIGHT_MNAME' => array(
                        'title' => $this->trans('Brand weight', array(), 'Admin.Shopparameters.Feature'),
                        'validation' => 'isUnsignedInt',
                        'type' => 'text',
                        'cast' => 'intval',
                    ),
                    'PS_SEARCH_WEIGHT_TAG' => array(
                        'title' => $this->trans('Tags weight', array(), 'Admin.Shopparameters.Feature'),
                        'validation' => 'isUnsignedInt',
                        'type' => 'text',
                        'cast' => 'intval',
                    ),
                    'PS_SEARCH_WEIGHT_ATTRIBUTE' => array(
                        'title' => $this->trans('Attributes weight', array(), 'Admin.Shopparameters.Feature'),
                        'validation' => 'isUnsignedInt',
                        'type' => 'text',
                        'cast' => 'intval',
                    ),
                    'PS_SEARCH_WEIGHT_FEATURE' => array(
                        'title' => $this->trans('Features weight', array(), 'Admin.Shopparameters.Feature'),
                        'validation' => 'isUnsignedInt',
                        'type' => 'text',
                        'cast' => 'intval',
                    ),
                ),
                'submit' => array('title' => $this->trans('Save', array(), 'Admin.Actions')),
            ),
        );
    }

    public function initPageHeaderToolbar()
    {
        if (empty($this->display)) {
            $this->page_header_toolbar_btn['new_alias'] = array(
                'href' => self::$currentIndex . '&addalias&token=' . $this->token,
                'desc' => $this->trans('Add new alias', array(), 'Admin.Shopparameters.Feature'),
                'icon' => 'process-icon-new',
            );
        }
        $this->identifier_name = 'alias';
        parent::initPageHeaderToolbar();
        if ($this->can_import) {
            $this->toolbar_btn['import'] = array(
                'href' => $this->context->link->getAdminLink('AdminImport', true) . '&import_type=alias',
                'desc' => $this->trans('Import', array(), 'Admin.Actions'),
            );
        }
    }

    public function initProcess()
    {
        parent::initProcess();
        // This is a composite page, we don't want the "options" display mode
        if ($this->display == 'options') {
            $this->display = '';
        }
    }

    /**
     * Function used to render the options for this controller.
     */
    public function renderOptions()
    {
        if ($this->fields_options && is_array($this->fields_options)) {
            $helper = new HelperOptions($this);
            $this->setHelperDisplay($helper);
            $helper->toolbar_scroll = true;
            $helper->toolbar_btn = array('save' => array(
                'href' => '#',
                'desc' => $this->trans('Save', array(), 'Admin.Actions'),
            ));
            $helper->id = $this->id;
            $helper->tpl_vars = $this->tpl_option_vars;
            $options = $helper->generateOptions($this->fields_options);

            return $options;
        }
    }

    public function renderForm()
    {
        $this->fields_form = array(
            'legend' => array(
                'title' => $this->trans('Aliases', array(), 'Admin.Shopparameters.Feature'),
                'icon' => 'icon-search',
            ),
            'input' => array(
                array(
                    'type' => 'text',
                    'label' => $this->trans('Alias', array(), 'Admin.Shopparameters.Feature'),
                    'name' => 'alias',
                    'required' => true,
                    'hint' => array(
                        $this->trans('Enter each alias separated by a comma (e.g. \'prestshop,preztashop,prestasohp\').', array(), 'Admin.Shopparameters.Help'),
                        $this->trans('Forbidden characters: &lt;&gt;;=#{}', array(), 'Admin.Shopparameters.Help'),
                    ),
                ),
                array(
                    'type' => 'text',
                    'label' => $this->trans('Result', array(), 'Admin.Shopparameters.Feature'),
                    'name' => 'search',
                    'required' => true,
                    'hint' => $this->trans('Search this word instead.', array(), 'Admin.Shopparameters.Help'),
                ),
            ),
            'submit' => array(
                'title' => $this->trans('Save', array(), 'Admin.Actions'),
            ),
        );

        $this->fields_value = array('alias' => $this->object->getAliases());

        return parent::renderForm();
    }

    public function processSave()
    {
        $search = (string) Tools::getValue('search');
        $string = (string) Tools::getValue('alias');
        $aliases = explode(',', $string);
        if (empty($search) || empty($string)) {
            $this->errors[] = $this->trans('Aliases and results are both required.', array(), 'Admin.Shopparameters.Notification');
        }
        if (!Validate::isValidSearch($search)) {
            $this->errors[] = Tools::safeOutput($search) . ' ' . $this->trans('Is not a valid result', array(), 'Admin.Shopparameters.Notification');
        }
        foreach ($aliases as $alias) {
            if (!Validate::isValidSearch($alias)) {
                $this->errors[] = Tools::safeOutput($alias) . ' ' . $this->trans('Is not a valid alias', array(), 'Admin.Shopparameters.Notification');
            }
        }

        if (!count($this->errors)) {
            foreach ($aliases as $alias) {
                $obj = new Alias(null, trim($alias), trim($search));
                $obj->save();
            }
        }

        if (empty($this->errors)) {
            $this->confirmations[] = $this->trans('Creation successful', array(), 'Admin.Shopparameters.Notification');
        }
    }

    /**
     * Retrieve a part of the cookie key for token check. (needs to be static).
     *
     * @return string Token
     */
    private function getTokenForCron()
    {
        return substr(
            _COOKIE_KEY_,
            AdminSearchController::TOKEN_CHECK_START_POS,
            AdminSearchController::TOKEN_CHECK_LENGTH
        );
    }
}
