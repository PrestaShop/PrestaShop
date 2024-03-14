<?php
/**
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
use PrestaShop\PrestaShop\Adapter\Category\CategoryProductSearchProvider;
use PrestaShop\PrestaShop\Adapter\Image\ImageRetriever;
use PrestaShop\PrestaShop\Adapter\Presenter\Category\CategoryPresenter;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\RedirectType;
use PrestaShop\PrestaShop\Core\Product\Search\ProductSearchQuery;
use PrestaShop\PrestaShop\Core\Product\Search\SortOrder;

class CategoryControllerCore extends ProductListingFrontController
{
    /** @var string Internal controller name */
    public $php_self = 'category';

    /** @var bool If set to false, customer cannot view the current category. */
    public $customer_access = true;

    /** @var bool */
    protected $notFound = false;

    /**
     * @var Category
     */
    protected $category;

    /** @var CategoryPresenter */
    protected $categoryPresenter;

    public function canonicalRedirection(string $canonicalURL = '')
    {
        if (Validate::isLoadedObject($this->category)) {
            parent::canonicalRedirection($this->context->link->getCategoryLink($this->category));
        }
    }

    /**
     * Returns canonical URL for current category
     *
     * @return string
     */
    public function getCanonicalURL(): string
    {
        if (!Validate::isLoadedObject($this->category)) {
            return '';
        }

        return $this->buildPaginatedUrl($this->context->link->getCategoryLink($this->category));
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

        parent::init();

        if (!Validate::isLoadedObject($this->category) || !$this->category->active || !$this->category->existsInShop($this->context->shop->id)) {
            if (!$this->category->id_type_redirected) {
                if (in_array($this->category->redirect_type, [RedirectType::TYPE_CATEGORY_PERMANENT, RedirectType::TYPE_CATEGORY_TEMPORARY])) {
                    $this->category->id_type_redirected = $this->category->id_category_default;
                }
            }

            switch ($this->category->redirect_type) {
                case RedirectType::TYPE_CATEGORY_PERMANENT:
                    header('HTTP/1.1 301 Moved Permanently');
                    header('Location: ' . $this->context->link->getCategoryLink($this->category->id_type_redirected));
                    exit;
                case RedirectType::TYPE_CATEGORY_TEMPORARY:
                    header('HTTP/1.1 302 Moved Temporarily');
                    header('Cache-Control: no-cache');
                    header('Location: ' . $this->context->link->getCategoryLink($this->category->id_type_redirected));
                    exit;
                case RedirectType::TYPE_GONE:
                    header('HTTP/1.1 410 Gone');
                    header('Status: 410 Gone');
                    $this->errors[] = $this->trans('This product is no longer available.', [], 'Shop.Notifications.Error');
                    $this->setTemplate('errors/410');

                    break;
                case RedirectType::TYPE_NOT_FOUND:
                default:
                    header('HTTP/1.1 404 Not Found');
                    header('Status: 404 Not Found');
                    $this->errors[] = $this->trans('This product is no longer available.', [], 'Shop.Notifications.Error');
                    $this->setTemplate('errors/404');
                    $this->notFound = true;

                    break;
            }

            return;
        } elseif (!$this->category->checkAccess($this->context->customer->id)) {
            header('HTTP/1.1 403 Forbidden');
            header('Status: 403 Forbidden');
            $this->errors[] = $this->trans('You do not have access to this category.', [], 'Shop.Notifications.Error');
            $this->setTemplate('errors/forbidden');

            return;
        }

        // Initialize presenter, we will use it for all cases
        $this->categoryPresenter = new CategoryPresenter($this->context->link);

        $this->context->smarty->assign([
            'category' => $this->getTemplateVarCategory(),
            'subcategories' => $this->getTemplateVarSubCategories(),
        ]);
    }

    /**
     * Assign template vars related to page content.
     *
     * @see FrontController::initContent()
     */
    public function initContent()
    {
        parent::initContent();

        if (
            Validate::isLoadedObject($this->category)
            && $this->category->active
            && $this->category->checkAccess($this->context->customer->id)
            && $this->category->existsInShop($this->context->shop->id)
        ) {
            $this->doProductSearch(
                'catalog/listing/category',
                [
                    'entity' => 'category',
                    'id' => $this->category->id,
                ]
            );
        }
    }

    /**
     * overrides layout if category is not visible.
     *
     * @return bool|string
     */
    public function getLayout()
    {
        if (!$this->category->checkAccess($this->context->customer->id) || $this->notFound) {
            return $this->context->shop->theme->getLayoutRelativePathForPage('error');
        }

        return parent::getLayout();
    }

    protected function getAjaxProductSearchVariables()
    {
        // Basic data with rendered products, facets, active filters etc.
        $data = parent::getAjaxProductSearchVariables();

        // Extra data for category pages, so we can dynamically update also these parts
        $rendered_category_header = $this->render('catalog/_partials/category-header', ['listing' => $data]);
        $data['rendered_products_header'] = $rendered_category_header;
        $rendered_category_footer = $this->render('catalog/_partials/category-footer', ['listing' => $data]);
        $data['rendered_products_footer'] = $rendered_category_footer;

        return $data;
    }

    /**
     * Gets the product search query for the controller. This is a set of information that
     * a filtering module or the default provider will use to fetch our products.
     *
     * @return ProductSearchQuery
     *
     * @throws \PrestaShop\PrestaShop\Core\Product\Search\Exception\InvalidSortOrderDirectionException
     */
    protected function getProductSearchQuery()
    {
        $query = new ProductSearchQuery();
        $query
            ->setQueryType('category')
            ->setIdCategory($this->category->id)
            ->setSortOrder(new SortOrder('product', Tools::getProductsOrder('by'), Tools::getProductsOrder('way')));

        return $query;
    }

    /**
     * Default product search provider used if no filtering module stood up for the job
     *
     * @return CategoryProductSearchProvider
     */
    protected function getDefaultProductSearchProvider()
    {
        return new CategoryProductSearchProvider(
            $this->getTranslator(),
            $this->category
        );
    }

    protected function getTemplateVarCategory()
    {
        $categoryVar = $this->categoryPresenter->present(
            $this->category,
            $this->context->language
        );

        $filteredCategory = Hook::exec(
            'filterCategoryContent',
            ['object' => $categoryVar],
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

        return $categoryVar;
    }

    protected function getTemplateVarSubCategories()
    {
        $subcategories = $this->category->getSubCategories($this->context->language->id);

        foreach ($subcategories as &$subcategory) {
            $subcategory = $this->categoryPresenter->present(
                $subcategory,
                $this->context->language
            );
        }

        return $subcategories;
    }

    /**
     * @deprecated since 9.0.0 and will be removed in 10.0.0
     */
    protected function getImage(Category $object, int $id_image)
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
            /** @var Category $category */
            if ($category->id_parent != 0 && !$category->is_root_category && $category->active) {
                $breadcrumb['links'][] = [
                    'title' => $category->name,
                    'url' => $this->context->link->getCategoryLink($category),
                ];
            }
        }

        if ($this->category->id_parent != 0 && !$this->category->is_root_category && $this->category->active) {
            $breadcrumb['links'][] = [
                'title' => $this->category->name,
                'url' => $this->context->link->getCategoryLink($this->category),
            ];
        }

        return $breadcrumb;
    }

    /**
     * @return Category
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * Initializes a set of commonly used variables related to the current page, available for use
     * in the template. @see FrontController::assignGeneralPurposeVariables for more information.
     *
     * @return array
     */
    public function getTemplateVarPage()
    {
        $page = parent::getTemplateVarPage();

        if ($this->notFound) {
            $page['page_name'] = 'pagenotfound';
            $page['body_classes']['pagenotfound'] = true;
            $page['title'] = $this->trans('The page you are looking for was not found.', [], 'Shop.Theme.Global');
        } else {
            $page['body_classes']['category-id-' . $this->category->id] = true;
            $page['body_classes']['category-' . $this->category->name] = true;
            $page['body_classes']['category-id-parent-' . $this->category->id_parent] = true;
            $page['body_classes']['category-depth-level-' . $this->category->level_depth] = true;
        }

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
            ['%category_name%' => $this->category->name],
            'Shop.Theme.Catalog'
        );
    }
}
