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
namespace PrestaShop\PrestaShop\Core\Business\Controller\Admin;

use PrestaShop\PrestaShop\Core\Business\Controller\AdminController;
use PrestaShop\PrestaShop\Core\Foundation\Routing\Response;
use Symfony\Component\HttpFoundation\Request;
use PrestaShop\PrestaShop\Core\Business\Controller\AutoObjectInflaterTrait;
use PrestaShop\PrestaShop\Core\Foundation\Form\FormFactory;
use PrestaShop\PrestaShop\Core\Business\Product\Form as ProductForms;
use PrestaShop\PrestaShop\Core\Foundation\Controller\AbstractController;
use PrestaShop\PrestaShop\Core\Business\Form\Type\ChoiceCategoriesTreeType;
use PrestaShop\PrestaShop\Core\Business\Product\ModelApdapter\Product as ProductModelAdapter;
use PrestaShop\PrestaShop\Core\Foundation\Exception\DevelopmentErrorException;
use PrestaShop\PrestaShop\Core\Foundation\Exception\WarningException;
use PrestaShop\PrestaShop\Core\Business\Routing\AdminRouter;
use PrestaShop\PrestaShop\Core\Foundation\IoC\Container;
use PrestaShop\PrestaShop\Core\Foundation\Dispatcher\BaseEvent;

/**
 * Admin controller for the Product pages using the Symfony architecture:
 * - categories
 * - product list
 * - product details
 * - product attributes
 * - ...
 *
 * This controller is the first one to be beta tested on the new Symfony Architecture.
 * The retro-compatibility is dropped for the corresponding Admin pages.
 * A set of hooks are integrated and an Adapter is made to wrap the new EventDispatcher
 * component to the existing hook system. So existing hooks are always triggered, but from the new
 * code (and so needs to be adapted on the module side ton comply on the new parameters formats, the new UI style, etc...).
 *
 * FIXME: to adapt after 1.7.0 when alternative behavior will be removed (@see self::shouldUseLegacyPagesAction()).
 */
class ProductController extends AdminController
{
    /* (non-PHPdoc)
     * @see \PrestaShop\PrestaShop\Core\Foundation\Controller\AbstractController::__construct()
     */
    public function __construct(AdminRouter $router, Container $container)
    {
        parent::__construct($router, $container);
        $this->registerExecutionSequenceService('CoreBusiness:Controller\\ExecutionSequenceService\\AutoResponseFormatSetter');
        $this->registerExecutionSequenceService('CoreBusiness:Controller\\ExecutionSequenceService\\AutoObjectInflater');
    }

    /**
     * Get the Catalog page with KPI banner, product list, bulk actions, filters, search, etc...
     *
     * URL example: /product/catalog/40/20/id_product/asc
     *
     * @param Request $request
     * @param Response $response
     */
    public function productCatalogAction(Request $request, Response $response)
    {
        // Redirect to legacy controller (FIXME: temporary behavior)
        if ($this->shouldUseLegacyPages()) {
            $this->redirectToRoute(
                $request,
                'admin_product_catalog',
                array(
                    // do not tranmit limit & offset: go to the first page when
                    'productOrderby' => $request->attributes->get('orderBy'),
                    'productOrderway' => $request->attributes->get('orderWay')
                ),
                true, // force legacy URL
                false // temporary
            );
        }

        // Retrieve persisted filter parameters
        $dataProvider = $this->container->make('CoreAdapter:Product\\AdminProductDataProvider');
        // get old values from persistence (before the current update)
        $persistedFilterParameters = $dataProvider->getPersistedFilterParameters('ls_products_');
        // override the old values with the new ones.
        $persistedFilterParameters = array_replace($persistedFilterParameters, $request->request->all());
        // calling addContentData with null key to insert many values at once.
        $response->addContentData(null, $persistedFilterParameters);

        // Get Product list from productListAction subcall (this will update filter params in persistence)
        $productListParams = array(
            'ls_products_limit' => $request->attributes->get('limit'),
            'ls_products_offset' => $request->attributes->get('offset'),
            'ls_products_orderBy' => $request->attributes->get('orderBy'),
            'ls_products_orderWay' => $request->attributes->get('orderWay'),
            '_layout_mode' => 'none_html'
        );
        $subResponse = $this->subcall('admin_product_list', $productListParams, AbstractController::RESPONSE_PARTIAL_VIEW, true);

        $response->addContentData('product_list', $subResponse->getContent());
        $hasCategoryFilter = $dataProvider->isCategoryFiltered();
        $hasColumnFilter = $dataProvider->isColumnFiltered();
        $response->addContentData('has_filter', $hasCategoryFilter | $hasColumnFilter);
        $response->addContentData('has_category_filter', $hasCategoryFilter);
        $response->addContentData('has_column_filter', $hasColumnFilter);
        $response->addContentData('activate_drag_and_drop', ('position' == $request->attributes->get('orderBy') && 'asc' == $request->attributes->get('orderWay')));

        // URLs injection
        $response->addContentData('post_url', $this->generateUrl('admin_product_catalog', array(
            'limit' => $request->attributes->get('limit'),
            // No offset: filter & bulk action will post form and want to redirect to first page.
            //'offset' => $request->attributes->get('offset'),
            'orderBy' => $request->attributes->get('orderBy'),
            'orderWay' => $request->attributes->get('orderWay')
        )));
        $response->addContentData('ordering_url', $this->generateUrl('admin_product_catalog', array(
            'limit' => $request->attributes->get('limit'),
            // No offset: re-ordering action will use this url to redirect to first page.
            //'offset' => $request->attributes->get('offset'),
            'orderBy' => 'name', // will be replaced by JS. Must be non default value (see routes YML file)
            'orderWay' => 'desc' // will be replaced by JS. Must be non default value (see routes YML file)
        )));
        $response->addContentData('bulk_url', $this->generateUrl('admin_product_bulk_action', array(
            'action' => 'activate_all' // will be replaced by JS. Must be non default value (see routes YML file)
        )));
        $actionRedirectionUrl = $this->generateUrl('admin_product_catalog', array(
            'limit' => $request->attributes->get('limit'),
            'offset' => $request->attributes->get('offset'),
            'orderBy' => $request->attributes->get('orderBy'),
            'orderWay' => $request->attributes->get('orderWay')
        ));
        $response->addContentData('bulk_redirect_url', $actionRedirectionUrl);
        $response->addContentData('unit_redirect_url', $actionRedirectionUrl);

        // Alternative layout for empty list
        $totalFilteredProductCount = $subResponse->getContentData('product_count'); // total count of SQL query (filtered)
        if (!$hasCategoryFilter && !$hasColumnFilter && $totalFilteredProductCount === 0) {
            // no filter, total filtered gives 0, no need to query 'non filtered total' twice!
            $response->setTemplate('Core/Controller/Product/productCatalogEmpty.tpl');
        } else {
            $totalProductCount = $dataProvider->countAllProducts();
            if ($totalProductCount === 0) {
                // total count == 0 too.
                $response->setTemplate('Core/Controller/Product/productCatalogEmpty.tpl');
            } else {
                $response->addContentData('product_count_filtered', $totalFilteredProductCount);
                $response->addContentData('product_count', $totalProductCount);

                // Navigator
                $this->addNavigatorToResponse($request, $response, $totalFilteredProductCount);

                // Category tree
                $formFactory = new FormFactory();
                $form = $formFactory->create(new ChoiceCategoriesTreeType('categories', \Category::getNestedCategories(), false));
                if (!empty($persistedFilterParameters['ls_products_filter_category'])) {
                    $form->setData(array('tree' => array(0 => $persistedFilterParameters['ls_products_filter_category'])));
                }
                $engine = new \PrestaShop\PrestaShop\Core\Foundation\View\ViewFactory($this->container, 'twig');
                $response->addContentData(
                    'categories',
                    $engine->view->render('Core/Controller/Product/categoriesTreeSelector.html.twig', array('form' => $form->createView()))
                );
                $response->addJs(_PS_JS_DIR_.'Core/Admin/Product.js');
            }
        }

        // Add layout top-right menu actions
        $toolbarButtons = array();
        if ($this->shouldDisplayUseLegacyOption()) {
            $toolbarButtons['legacy'] = array(
                'href' => $this->generateUrl('admin_product_use_legacy_setter', array('use' => 1), false),
                'desc' => $this->container->make('CoreAdapter:Translator')->trans('Switch back to old Page', array(), 'AdminProducts'),
                'icon' => 'process-icon-toggle-on',
                'help' => $this->container->make('CoreAdapter:Translator')->trans('The new page cannot fit your needs now? Fallback to the old one, and tell us why!', array(), 'AdminProducts')
            );
        }
        $toolbarButtons['add'] = array(
            'href' => $this->generateUrl('admin_product_form'),
            'desc' => $this->container->make('CoreAdapter:Translator')->trans('Add new product', array(), 'AdminProducts'),
            'icon' => 'process-icon-new'
        );
        $response->setHeaderToolbarBtn($toolbarButtons);
    }

    /**
     * Get only the list of products to display on the main Admin Product page.
     * The full page that shows products list will subcall this action (from productListCatalogAction).
     * URL example: /product/list/layout_html/40/20/id_product/asc
     *
     * @param Request $request
     * @param Response $response
     * @param Product[] $products The collection of products requested. Filled by AutoObjectInflaterTrait.
     * @return void The response format is automatically placed by the Router through _layout_mode attribute
     */
    public function productListAction(Request $request, Response $response, array &$products)
    {
        $totalCount = 0;
        // Adds controller info (URLs, etc...) to product list
        foreach ($products as &$product) {
            $totalCount = isset($product['total'])? $product['total'] : $totalCount;
            $product['url'] = $this->generateUrl('admin_product_form', array('id_product' => $product['id_product']));
        }

        $response->replaceContentData('products', $products);
        $response->addContentData('product_count', $totalCount);

        // ordering by drag&drop
        $response->addContentData('activate_drag_and_drop', ('position' == $request->attributes->get('ls_products_orderBy') && 'asc' == $request->attributes->get('ls_products_orderWay')));
    }

    /**
     * TODO: Luc, mets un commentaire ici stp !
     *
     * @param Request $request
     * @param Response $response
     * @param unknown $product
     * @return string
     */
    public function productFormAction(Response $response, Request $request, $product)
    {
        $response->setContentData(['product' => $product]);

        $legacyContext = $this->container->make('Adapter_LegacyContext');
        $locales = $this->container->make('CoreAdapter:Language\\LanguageDataProvider')->getLanguages();

        // Redirect to legacy controller (FIXME: temporary behavior)
        if ($this->shouldUseLegacyPages()) {
            $this->redirectToRoute(
                $request,
                'admin_product_form',
                array(
                    'id_product' => $request->attributes->get('id_product', 'new')
                ),
                true, // force legacy URL
                false // temporary
            );
        }

        $formFactory = new FormFactory();
        $builder = $formFactory->createBuilder('form', ProductModelAdapter::formMapper($product, $this->container, $locales));

        $response->addJs(array(
            _PS_JS_DIR_.'tiny_mce/tiny_mce.js',
            _PS_JS_DIR_.'admin/tinymce.inc.js',
            _PS_JS_DIR_.'admin/tinymce_loader.js',
            _PS_JS_DIR_.'vendor/node_modules/typeahead.js/dist/typeahead.jquery.min.js',
            _PS_JS_DIR_.'vendor/node_modules/typeahead.js/dist/bloodhound.min.js',
        ));

        $response->addCss(array(
            $legacyContext->getAdminBaseUrl().'/themes/default/css/admin-core-theme.css'
        ));

        $form = $builder
            ->add('id_product', 'hidden')
            ->add('step1', new ProductForms\ProductInformation($this->container))
            ->add('step2', new ProductForms\ProductQuantity($this->container))
            ->add('step3', new ProductForms\ProductShipping($this->container))
            ->add('step4', new ProductForms\ProductSeo($this->container))
            ->add('step5', new ProductForms\ProductOptions($this->container))
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $_POST = ProductModelAdapter::modelMapper($form->getData(), $this->container, $locales);

                $adminProductController = $this->container->make('CoreAdapter:Product\\AdminProductControllerWrapper')->get();
                $adminProductController->setIdObject($form->getData()['id_product']);
                $adminProductController->setAction('save');

                if ($product = $adminProductController->postCoreProcess()) {
                    $adminProductController->processSuppliers($product->id);
                    $response->setContentData(['product' => $product]);
                }

                if ($request->isXmlHttpRequest()) {
                    return self::RESPONSE_JSON;
                }
            } elseif ($request->isXmlHttpRequest()) {
                $response->setStatusCode(400);
                $response->setContentData($this->getFormErrorsForJS($form));
                return self::RESPONSE_JSON;
            }
        }

        $response->setEngineName('twig');
        $response->setLegacyControllerName('AdminProducts');
        $response->setDisplayType('add');

        $response->addContentData('form', $form->createView());
    }


    /**
     * Do bulk action on a list of Products. Used with the 'bulk action' dropdown menu on the Catalog page.
     *
     * @param Request $request
     * @throws DevelopmentErrorException
     */
    public function bulkAction(Request $request)
    {
        $action = $request->attributes->get('action');
        $productIdList = $request->request->get('bulk_action_selected_products');
        $updater = $this->container->make('CoreAdapter:Product\\ProductDataUpdater');

        try {
            switch ($action) {
                case 'activate_all':
                    $success = $updater->activateProductIdList($productIdList);
                    $this->enqueueMessage($this->container->make('Translator')->trans('Product(s) successfully activated.'), true);
                    break;
                case 'deactivate_all':
                    $success = $updater->activateProductIdList($productIdList, false);
                    $this->enqueueMessage($this->container->make('Translator')->trans('Product(s) successfully deactivated.'), true);
                    break;
                case 'delete_all':
                    $success = $updater->deleteProductIdList($productIdList);
                    $this->enqueueMessage($this->container->make('Translator')->trans('Product(s) successfully deleted.'), true);
                    break;
                default:
                    // should never happens since the route parameters are restricted to a set of action values in YML file.
                    throw new DevelopmentErrorException('Bad action received from call to ProductController::bulkAction.', $action, 2001);
            }
        } catch (WarningException $we) {
            /* Let the layout display the message... Continue execution */
        }

        // redirect after success
        $this->redirect($request->request->get('redirect_url'), false);
    }

    /**
     * Do action on one product at a time. Can be used at many places in the controller's page.
     *
     * @param Request $request
     * @param unknown $product
     * @throws DevelopmentErrorException
     */
    public function unitAction(Request $request, $product)
    {
        $action = $request->attributes->get('action');
        $updater = $this->container->make('CoreAdapter:Product\\ProductDataUpdater');

        try {
            switch ($action) {
                case 'delete':
                    $success = $updater->deleteProduct($product);
                    $this->enqueueMessage($this->container->make('Translator')->trans('Product successfully deleted.'), true);
                    break;
                case 'duplicate':
                    $duplicateProductId = $updater->duplicateProduct($product);
                    $this->enqueueMessage($this->container->make('Translator')->trans('Product successfully duplicated.'), true);
                    // stops here and redirect to the new product's page.
                    $this->redirectToRoute($request, 'admin_product_form', array('id_product' => $duplicateProductId), false, false);
                default:
                    // should never happens since the route parameters are restricted to a set of action values in YML file.
                    throw new DevelopmentErrorException('Bad action received from call to ProductController::unitAction.', $action, 2002);
            }
        } catch (WarningException $we) {
            /* Let the layout display the message... Continue execution */
        }

        // redirect after success
        $this->redirect($request->request->get('redirect_url'), false);
    }

    /**
     * Use it internally to know if we need to redirect to legacy Controllers.
     * @see self::shouldUseLegacyPagesAction() for more information.
     *
     * FIXME: This is a temporary behavior.
     *
     * @return boolean True to redirect to legacy.
     */
    final private function shouldUseLegacyPages()
    {
        $dataProvider = $this->container->make('CoreAdapter:Product\\AdminProductDataProvider');
        return $dataProvider->getTemporaryShouldUseLegacyPages();
    }

    /**
     * Will allow switch button only if PrestaShop install comes from an upgrade from before 1.7.
     * If the first install was made in 1.7, or the current version is >1.7.0.x, does not display switch.
     *
     * @see self::shouldUseLegacyPagesAction() for more information.
     *
     * FIXME: This is a temporary behavior.
     *
     * @return boolean True to show swtich button.
     */
    final private function shouldDisplayUseLegacyOption()
    {
        $dataProvider = $this->container->make('CoreAdapter:Product\\AdminProductDataProvider');
        return $dataProvider->getTemporaryShouldAllowUseLegacyPages();
    }

    /**
     * This action will persist user choice to use (or not) the legacy Product pages instead of the new one.
     *
     * This action will allow the merchant to switch between the new and the old pages for Catalog & Products pages.
     * This is a temporary behavior, that will be removed in a futur minor release. This is here to let the modules
     * adapting their hooks to the new controller behavior during a short time.
     *
     * FIXME: This is a temporary behavior. (clean the route YML conf in the same time)
     *
     * @param Request $request
     */
    public function shouldUseLegacyPagesAction(Request $request)
    {
        $dataProvider = $this->container->make('CoreAdapter:Product\\AdminProductDataProvider');
        $useLegacy = $request->attributes->get('use');
        $dataProvider->setTemporaryShouldUseLegacyPages($useLegacy == 1);
        $this->redirectToRoute(
            $request,
            'admin_product_catalog',
            array(
                // do not tranmit limit & offset: go to the first page when
                'productOrderby' => $request->attributes->get('orderBy'),
                'productOrderway' => $request->attributes->get('orderWay')
            ),
            $useLegacy, // force legacy URL
            false // temporary
        );
    }
}
