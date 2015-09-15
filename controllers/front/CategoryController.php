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

class CategoryControllerCore extends ProductPresentingFrontControllerCore
{
    /** string Internal controller name */
    public $php_self = 'category';

    /** @var Category Current category object */
    protected $category;

    /** @var bool If set to false, customer cannot view the current category. */
    public $customer_access = true;

    /** @var int Number of products in the current page. */
    protected $nbProducts;

    /** @var array Products to be displayed in the current page . */
    protected $cat_products;

    /**
     * Sets default medias for this controller
     */
    public function setMedia()
    {
        parent::setMedia();

        if (!$this->useMobileTheme()) {
            //TODO : check why cluetip css is include without js file
            $this->addCSS(array(
                _THEME_CSS_DIR_.'scenes.css'       => 'all',
                _THEME_CSS_DIR_.'category.css'     => 'all',
                _THEME_CSS_DIR_.'product_list.css' => 'all',
            ));
        }

        $scenes = Scene::getScenes($this->category->id, $this->context->language->id, true, false);
        if ($scenes && count($scenes)) {
            $this->addJS(_THEME_JS_DIR_.'scenes.js');
            $this->addJqueryPlugin(array('scrollTo', 'serialScroll'));
        }

        $this->addJS(_THEME_JS_DIR_.'category.js');
    }

    /**
     * Redirects to canonical or "Not Found" URL
     *
     * @param string $canonical_url
     */
    public function canonicalRedirection($canonical_url = '')
    {
        if (Tools::getValue('live_edit')) {
            return;
        }

        if (!Validate::isLoadedObject($this->category) || !$this->category->inShop() || !$this->category->isAssociatedToShop() || in_array($this->category->id, array(Configuration::get('PS_HOME_CATEGORY'), Configuration::get('PS_ROOT_CATEGORY')))) {
            $this->redirect_after = '404';
            $this->redirect();
        }

        if (!Tools::getValue('noredirect') && Validate::isLoadedObject($this->category)) {
            parent::canonicalRedirection($this->context->link->getCategoryLink($this->category));
        }
    }

    /**
     * Initializes controller
     *
     * @see FrontController::init()
     * @throws PrestaShopException
     */
    public function init()
    {
        // Get category ID
        $id_category = (int)Tools::getValue('id_category');
        if (!$id_category || !Validate::isUnsignedId($id_category)) {
            $this->errors[] = Tools::displayError('Missing category ID');
        }

        // Instantiate category
        $this->category = new Category($id_category, $this->context->language->id);

        parent::init();

        // Check if the category is active and return 404 error if is disable.
        if (!$this->category->active) {
            header('HTTP/1.1 404 Not Found');
            header('Status: 404 Not Found');
        }

        // Check if category can be accessible by current customer and return 403 if not
        if (!$this->category->checkAccess($this->context->customer->id)) {
            header('HTTP/1.1 403 Forbidden');
            header('Status: 403 Forbidden');
            $this->errors[] = Tools::displayError('You do not have access to this category.');
            $this->customer_access = false;
        }
    }

    protected function getImage($object, $id_image)
    {
        $retriever = new Adapter_ImageRetriever(
            $this->context->link
        );
        return $retriever->getImage($object, $id_image);
    }

    public function prepareProductForTemplate(array $product)
    {
        $presenter = $this->getProductPresenter();
        $settings = $this->getProductPresentationSettings();

        return $presenter->presentForListing(
            $settings,
            $product,
            $this->context->language
        );
    }

    /**
     * Initializes page content variables
     */
    public function initContent()
    {
        parent::initContent();

        $this->setTemplate('catalog/category.tpl');

        if (!$this->customer_access) {
            return;
        }

        if (isset($this->context->cookie->id_compare)) {
            $this->context->smarty->assign('compareProducts', CompareProduct::getCompareProducts((int)$this->context->cookie->id_compare));
        }

        // Product sort must be called before assignProductList()
        $this->productSort();
        $this->assignSortOptions();
        $this->assignScenes();
        $this->assignSubcategories();
        $this->assignProductList();

        $category = $this->objectSerializer->toArray($this->category);
        $category['image'] = $this->getImage($this->category, $this->category->id_image);
        $products = array_map(function (array $product) {
            return $this->prepareProductForTemplate($product);
        }, $this->cat_products);

        $this->context->smarty->assign(array(
            'category'             => $category,
            'description_short'    => Tools::truncateString($this->category->description, 350),
            'products'             => $products,
            'id_category'          => (int)$this->category->id,
            'id_category_parent'   => (int)$this->category->id_parent,
            'return_category_name' => Tools::safeOutput($this->category->name),
            'path'                 => Tools::getPath($this->category->id),
            'add_prod_display'     => Configuration::get('PS_ATTRIBUTE_CATEGORY_DISPLAY'),
            'categorySize'         => Image::getSize(ImageType::getFormatedName('category')),
            'mediumSize'           => Image::getSize(ImageType::getFormatedName('medium')),
            'thumbSceneSize'       => Image::getSize(ImageType::getFormatedName('m_scene')),
            'homeSize'             => Image::getSize(ImageType::getFormatedName('home')),
            'allow_oosp'           => (int)Configuration::get('PS_ORDER_OUT_OF_STOCK'),
            'comparator_max_item'  => (int)Configuration::get('PS_COMPARATOR_MAX_ITEM'),
            'body_classes'         => array($this->php_self.'-'.$this->category->id, $this->php_self.'-'.$this->category->link_rewrite)
        ));
    }

    protected function getSortOptions()
    {
        $settings = $this->getProductPresentationSettings();

        if ($settings->catalog_mode) {
            $options = [];
        } else {
            $options = [
                ['orderBy' => 'price', 'sortOrder' => 'asc', 'label' => $this->l('Increasing price')],
                ['orderBy' => 'price', 'sortOrder' => 'desc', 'label' => $this->l('Decreasing price')],
            ];
        }

        $options[] = ['orderBy' => 'name', 'sortOrder' => 'asc', 'label' => $this->l('Product name, A to Z')];
        $options[] = ['orderBy' => 'name', 'sortOrder' => 'desc', 'label' => $this->l('Product name, Z to A')];

        if (!$settings->catalog_mode && $settings->stock_management_enabled) {
            $options[] = ['orderBy' => 'quantity', 'sortOrder' => 'desc', 'label' => $this->l('In stock')];
        }

        $options[] = ['orderBy' => 'reference', 'sortOrder' => 'asc', 'label' => $this->l('Product reference, A to Z')];
        $options[] = ['orderBy' => 'reference', 'sortOrder' => 'desc', 'label' => $this->l('Product reference, Z to A')];

        $pageURL = $this->context->link->getCategoryLink(
            $this->category,
            $this->category->link_rewrite,
            $this->context->language->id
        );

        $options = array_map(function ($option) use ($pageURL) {
            $option['url'] = $pageURL . '?orderby=' . $option['orderBy'] . '&orderway=' . $option['sortOrder'];
            $option['current'] = ($option['orderBy'] === Tools::getValue('orderby')) &&
                                 ($option['sortOrder'] === Tools::getValue('orderway'))
            ;
            return $option;
        }, $options);

        return $options;
    }

    public function assignSortOptions()
    {
        $this->context->smarty->assign('sort_options', $this->getSortOptions());
    }

    /**
     * Assigns scenes template variables
     */
    protected function assignScenes()
    {
        // Scenes (could be externalised to another controller if you need them)
        $scenes = Scene::getScenes($this->category->id, $this->context->language->id, true, false);
        $this->context->smarty->assign('scenes', $scenes);

        // Scenes images formats
        if ($scenes && ($scene_image_types = ImageType::getImagesTypes('scenes'))) {
            foreach ($scene_image_types as $scene_image_type) {
                if ($scene_image_type['name'] == ImageType::getFormatedName('m_scene')) {
                    $thumb_scene_image_type = $scene_image_type;
                } elseif ($scene_image_type['name'] == ImageType::getFormatedName('scene')) {
                    $large_scene_image_type = $scene_image_type;
                }
            }

            $this->context->smarty->assign(array(
                'thumbSceneImageType' => isset($thumb_scene_image_type) ? $thumb_scene_image_type : null,
                'largeSceneImageType' => isset($large_scene_image_type) ? $large_scene_image_type : null,
            ));
        }
    }

    /**
     * Assigns subcategory templates variables
     */
    protected function assignSubcategories()
    {
        $subcategories = array_map(function (array $category) {
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

        $this->context->smarty->assign([
            'subcategories' => $subcategories
        ]);
    }

    /**
     * Assigns product list template variables
     */
    public function assignProductList()
    {
        $hook_executed = false;
        Hook::exec('actionProductListOverride', array(
            'nbProducts'   => &$this->nbProducts,
            'catProducts'  => &$this->cat_products,
            'hookExecuted' => &$hook_executed,
        ));

        // The hook was not executed, standard working
        if (!$hook_executed) {
            $this->context->smarty->assign('categoryNameComplement', '');
            $this->nbProducts = $this->category->getProducts(null, null, null, $this->orderBy, $this->orderWay, true);
            $this->pagination((int)$this->nbProducts); // Pagination must be call after "getProducts"
            $this->cat_products = $this->category->getProducts($this->context->language->id, (int)$this->p, (int)$this->n, $this->orderBy, $this->orderWay);
        }
        // Hook executed, use the override
        else {
            // Pagination must be call after "getProducts"
            $this->pagination($this->nbProducts);
        }

        $this->addColorsToProductList($this->cat_products);

        Hook::exec('actionProductListModifier', array(
            'nb_products'  => &$this->nbProducts,
            'cat_products' => &$this->cat_products,
        ));

        foreach ($this->cat_products as &$product) {
            if (isset($product['id_product_attribute']) && $product['id_product_attribute'] && isset($product['product_attribute_minimal_quantity'])) {
                $product['minimal_quantity'] = $product['product_attribute_minimal_quantity'];
            }
        }

        $this->context->smarty->assign('nb_products', $this->nbProducts);
    }

    /**
     * Returns an instance of the current category
     *
     * @return Category
     */
    public function getCategory()
    {
        return $this->category;
    }
}
