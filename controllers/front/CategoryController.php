<?php
/**
 * 2007-2015 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
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
 * @copyright 2007-2015 PrestaShop SA
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

 use PrestaShop\PrestaShop\Core\Business\Product\Search\ProductSearchQuery;
 use PrestaShop\PrestaShop\Core\Business\Product\Search\SortOrder;
 use PrestaShop\PrestaShop\Adapter\Category\CategoryProductSearchProvider;
 use PrestaShop\PrestaShop\Adapter\Translator;
 use PrestaShop\PrestaShop\Adapter\LegacyContext;

 class CategoryControllerCore extends ProductListingFrontController
 {
     /** string Internal controller name */
    public $php_self = 'category';

    /** @var bool If set to false, customer cannot view the current category. */
    public $customer_access = true;

    protected $category;

    public function canonicalRedirection($url = '')
    {
        // FIXME
    }

    public function getCanonicalURL()
    {
        // Canonical URL is the category URL without any parameters.
        return $this->updateQueryString(null);
    }

    /**
     * Initializes controller
     *
     * @see FrontController::init()
     * @throws PrestaShopException
     */
    public function init()
    {
        parent::init();

        $id_category = (int)Tools::getValue('id_category');
        $this->category = new Category(
            $id_category,
            $this->context->language->id
        );

        if (!$this->category->active) {
            Tools::redirect('index.php?controller=404');
        }

        if (!$this->category->checkAccess($this->context->customer->id)) {
            header('HTTP/1.1 403 Forbidden');
            header('Status: 403 Forbidden');
            $this->errors[] = $this->l('You do not have access to this category.');
            $this->setTemplate('catalog/forbidden-category.tpl');
            return;
        }

        $this->context->smarty->assign([
            'category'      => $this->getTemplateVarCategory(),
            'subcategories' => $this->getTemplateVarSubCategories()
        ]);

        $this->doProductSearch('catalog/category.tpl');
    }

    protected function getProductSearchQuery()
    {
        $query = new ProductSearchQuery;
        $query
            ->setIdCategory($this->category->id)
            ->setSortOrder(new SortOrder('product', 'position', 'asc'))
        ;
        return $query;
    }

    protected function getDefaultProductSearchProvider()
    {
        $translator = new Translator(new LegacyContext);
        return new CategoryProductSearchProvider(
            $translator,
            $this->category
        );
    }

    protected function getTemplateVarCategory()
    {
        $category = $this->objectSerializer->toArray($this->category);
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
        $retriever = new Adapter_ImageRetriever(
            $this->context->link
        );
        return $retriever->getImage($object, $id_image);
    }
 }
