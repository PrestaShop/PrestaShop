<?php
/**
 * 2007-2018 PrestaShop
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
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */
use PrestaShop\PrestaShop\Core\Product\Search\ProductSearchQuery;
use PrestaShop\PrestaShop\Core\Product\Search\SortOrder;
use PrestaShop\PrestaShop\Adapter\Category\CategoryProductSearchProvider;
use PrestaShop\PrestaShop\Adapter\Image\ImageRetriever;

class CategoryControllerCore extends ProductListingFrontController
{
    /** string Internal controller name */
    public $php_self = 'category';

    /** @var bool If set to false, customer cannot view the current category. */
    public $customer_access = true;

    /**
     * @var Category
     */
    protected $category;

    public function canonicalRedirection($canonicalURL = '')
    {
        if (Validate::isLoadedObject($this->category)) {
            parent::canonicalRedirection($this->context->link->getCategoryLink($this->category));
        } elseif ($canonicalURL) {
            parent::canonicalRedirection($canonicalURL);
        }
    }

    public function getCanonicalURL()
    {
        return $this->context->link->getCategoryLink($this->category);
    }

    /**
     * Initializes controller.
     *
     * @see FrontController::init()
     *
     * @throws PrestaShopException
     */
    public function init()
    {
        $id_category = (int) Tools::getValue('id_category');
        $this->category = new Category(
            $id_category,
            $this->context->language->id
        );

        if (!Validate::isLoadedObject($this->category) || !$this->category->active) {
            Tools::redirect('index.php?controller=404');
        }

        parent::init();

        if (!$this->category->checkAccess($this->context->customer->id)) {
            header('HTTP/1.1 403 Forbidden');
            header('Status: 403 Forbidden');

            return;
        }

        $categoryVar = $this->getTemplateVarCategory();

        $filteredCategory = Hook::exec(
            'filterCategoryContent',
            array('object' => $categoryVar),
            $id_module = null,
            $array_return = false,
            $check_exceptions = true,
            $use_push = false,
            $id_shop = null,
            $chain = true
        );
        if (!empty($filteredCategory['object'])) {
            $categoryVar = $filteredCategory['object'];
        }

        $this->context->smarty->assign(array(
            'category' => $categoryVar,
            'subcategories' => $this->getTemplateVarSubCategories(),
        ));
    }

    /**
     * @inheritdoc
     */
    public function initContent()
    {
        parent::initContent();

        $templateName = 'catalog/listing/category';
        $templateParams = array(
            'entity' => 'category',
            'id' => $this->category->id
        );
        if ($this->category->checkAccess($this->context->customer->id)) {
            $this->doProductSearch(
                $templateName,
                $templateParams
            );
        } else {
            $this->setTemplate($templateName, $templateParams);
            $this->context->smarty->assign(['listing' => ['products'=>[]]]);
            $this->context->smarty->assign('categoryErrorMessage', $this->trans('You do not have access to this category.', array(), 'Shop.Notifications.Error'));
        }
    }

    public function getLayout()
    {

        if (!$this->category->checkAccess($this->context->customer->id)) {
            return 'layouts/layout-full-width.tpl';
        }

        return parent::getLayout();
    }

    protected function getProductSearchQuery()
    {
        $query = new ProductSearchQuery();
        $query
            ->setIdCategory($this->category->id)
            ->setSortOrder(new SortOrder('product', Tools::getProductsOrder('by'), Tools::getProductsOrder('way')))
        ;

        return $query;
    }

    protected function getDefaultProductSearchProvider()
    {
        return new CategoryProductSearchProvider(
            $this->getTranslator(),
            $this->category
        );
    }

    protected function getTemplateVarCategory()
    {
        $category = $this->objectPresenter->present($this->category);
        $category['image'] = $this->getImage(
            $this->category,
            $this->category->id_image
        );

        return $category;
    }

    protected function getTemplateVarSubCategories()
    {
        return array_map(function (array $category) {
            $object = new Category(
                $category['id_category'],
                $this->context->language->id
            );

            $category['image'] = $this->getImage(
                $object,
                $object->id_image
            );

            $category['url'] = $this->context->link->getCategoryLink(
                $category['id_category'],
                $category['link_rewrite']
            );

            return $category;
        }, $this->category->getSubCategories($this->context->language->id));
    }

    protected function getImage($object, $id_image)
    {
        $retriever = new ImageRetriever(
            $this->context->link
        );

        return $retriever->getImage($object, $id_image);
    }

    public function getBreadcrumbLinks()
    {
        $breadcrumb = parent::getBreadcrumbLinks();

        foreach ($this->category->getAllParents() as $category) {
            if ($category->id_parent != 0 && !$category->is_root_category) {
                $breadcrumb['links'][] = $this->getCategoryPath($category);
            }
        }

        $breadcrumb['links'][] = $this->getCategoryPath($this->category);

        return $breadcrumb;
    }

    public function getCategory()
    {
        return $this->category;
    }

    public function getTemplateVarPage()
    {
        $page = parent::getTemplateVarPage();

        $page['body_classes']['category-id-'.$this->category->id] = true;
        $page['body_classes']['category-'.$this->category->name] = true;
        $page['body_classes']['category-id-parent-'.$this->category->id_parent] = true;
        $page['body_classes']['category-depth-level-'.$this->category->level_depth] = true;

        return $page;
    }

    public function getListingLabel()
    {
        if (!Validate::isLoadedObject($this->category)) {
            $this->category = new Category(
                (int) Tools::getValue('id_category'),
                $this->context->language->id
            );
        }

        return $this->trans(
            'Category: %category_name%',
            array('%category_name%' => $this->category->name),
            'Shop.Theme.Catalog'
        );
    }
}
