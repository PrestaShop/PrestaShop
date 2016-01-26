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
namespace PrestaShopBundle\Controller\Admin;

use PrestaShop\PrestaShop\Adapter\Warehouse\WarehouseDataProvider;
use PrestaShopBundle\Service\DataProvider\StockInterface;
use PrestaShopBundle\Service\Hook\HookEvent;
use Symfony\Component\HttpFoundation\Request;
use PrestaShopBundle\Service\TransitionalBehavior\AdminPagePreferenceInterface;
use PrestaShopBundle\Service\DataProvider\Admin\ProductInterface as ProductInterfaceProvider;
use PrestaShopBundle\Service\DataUpdater\Admin\ProductInterface as ProductInterfaceUpdater;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Log\LoggerInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use PrestaShopBundle\Form\Admin\Product as ProductForms;
use PrestaShopBundle\Exception\DataUpdateException;
use PrestaShopBundle\Model\Product\AdminModelAdapter as ProductAdminModelAdapter;
use Symfony\Component\HttpFoundation\JsonResponse;
use PrestaShopBundle\Form\Admin\Type\ChoiceCategoriesTreeType;
use Symfony\Component\Translation\TranslatorInterface;
use PrestaShopBundle\Service\Csv;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * Admin controller for the Product pages using the Symfony architecture:
 * - categories
 * - product list
 * - product details
 * - product attributes
 * - ...
 *
 * This controller is the first one to be refactored to the new Symfony Architecture.
 * The retro-compatibility is dropped for the corresponding Admin pages.
 * A set of hooks are integrated and an Adapter is made to wrap the new EventDispatcher
 * component to the existing hook system. So existing hooks are always triggered, but from the new
 * code (and so needs to be adapted on the module side ton comply on the new parameters formats, the new UI style, etc...).
 *
 * FIXME: to adapt after 1.7.0 when alternative behavior will be removed (@see AdminPagePreferenceInterface::getTemporaryShouldUseLegacyPage()).
 */
class ProductController extends FrameworkBundleAdminController
{
    /**
     * Get the Catalog page with KPI banner, product list, bulk actions, filters, search, etc...
     *
     * URL example: /product/catalog/40/20/id_product/asc
     *
     * @Template
     * @param Request $request
     * @param integer $limit The size of the listing
     * @param integer $offset The offset of the listing
     * @param string $orderBy To order product list
     * @param string $sortOrder To order product list
     * @return array Template vars
     */
    public function catalogAction(Request $request, $limit = 10, $offset = 0, $orderBy = 'id_product', $sortOrder = 'asc')
    {
        // Redirect to legacy controller (FIXME: temporary behavior)
        $pagePreference = $this->container->get('prestashop.core.admin.page_preference_interface');
        /* @var $pagePreference AdminPagePreferenceInterface */
        if ($pagePreference->getTemporaryShouldUseLegacyPage('product')) {
            $legacyUrlGenerator = $this->container->get('prestashop.core.admin.url_generator_legacy');
            /* @var $legacyUrlGenerator UrlGeneratorInterface */
            $redirectionParams = array(
                // do not transmit limit & offset: go to the first page when redirecting
                'productOrderby' => $orderBy,
                'productOrderway' => $sortOrder
            );
            return $this->redirect($legacyUrlGenerator->generate('admin_product_catalog', $redirectionParams), 302);
        }

        // If POST, then check/cast POST params formats
        if ($request->isMethod('POST')) {
            foreach ($request->request->all() as $param => $value) {
                switch ($param) {
                    case 'filter_column_id_product':
                    case 'filter_category':
                        if (!is_numeric($value)) {
                            $request->request->set($param, '');
                        }
                        if (is_numeric($value) && $value < 0) {
                            $request->request->set($param, '');
                        }
                }
            }
        }

        $logger = $this->container->get('logger');
        /* @var $logger LoggerInterface */

        $productProvider = $this->container->get('prestashop.core.admin.data_provider.product_interface');
        /* @var $productProvider ProductInterfaceProvider */
        $translator = $this->container->get('prestashop.adapter.translator');
        /* @var $translator TranslatorInterface */

        // get old values from persistence (before the current update)
        $persistedFilterParameters = $productProvider->getPersistedFilterParameters();

        if ($offset === 'last') {
            $offset = $persistedFilterParameters['last_offset'];
        }
        if ($limit === 'last') {
            $limit = $persistedFilterParameters['last_limit'];
        }
        if ($orderBy === 'last') {
            $orderBy = $persistedFilterParameters['last_orderBy'];
        }
        if ($sortOrder === 'last') {
            $sortOrder = $persistedFilterParameters['last_sortOrder'];
        }

        // override the old values with the new ones.
        $persistedFilterParameters = array_replace($persistedFilterParameters, $request->request->all());

        // Add layout top-right menu actions
        $toolbarButtons = array();
        if ($pagePreference->getTemporaryShouldAllowUseLegacyPage('product')) {
            $toolbarButtons['legacy'] = array(
                'href' => $this->generateUrl('admin_product_use_legacy_setter', array('use' => 1)),
                'desc' => $translator->trans('Switch back to old Page', array(), 'AdminProducts'),
                'icon' => 'process-icon-toggle-on',
                'help' => $translator->trans('The new page cannot fit your needs now? Fallback to the old one, and tell us why!', array(), 'AdminProducts')
            );
        }
        $toolbarButtons['add'] = array(
            'href' => $this->generateUrl('admin_product_new'),
            'desc' => $translator->trans('Add new product', array(), 'AdminProducts'),
            'icon' => 'process-icon-new'
        );

        // Fetch product list (and cache it into view subcall to listAction)
        $products = $productProvider->getCatalogProductList($offset, $limit, $orderBy, $sortOrder, $request->request->all());
        $lastSql = $productProvider->getLastCompiledSql();
        $logger->info('Product catalog filters stored.');
        $hasCategoryFilter = $productProvider->isCategoryFiltered();
        $hasColumnFilter = $productProvider->isColumnFiltered();

        // Alternative layout for empty list
        $totalFilteredProductCount = (count($products)>0) ? $products[0]['total'] : 0;
        $totalProductCount = 0;
        if ((!$hasCategoryFilter && !$hasColumnFilter && $totalFilteredProductCount === 0)
            || ($totalProductCount = $productProvider->countAllProducts()) === 0) {
            // no filter, total filtered == 0, and then total count == 0 too.
            $legacyUrlGenerator = $this->container->get('prestashop.core.admin.url_generator_legacy');
            return $this->render('PrestaShopBundle:Admin/Product:catalogEmpty.html.twig', array(
                'layoutHeaderToolbarBtn' => $toolbarButtons,
                'import_url' => $legacyUrlGenerator->generate('AdminImport'),
            ));
        } else {
            // Pagination
            $paginationParameters = $request->attributes->all();
            $paginationParameters['_route'] = 'admin_product_catalog';

            // Category tree
            $categories = $this->createForm(
                new ChoiceCategoriesTreeType('categories', $this->container->get('prestashop.adapter.data_provider.category')->getNestedCategories(), array(), false)
            );
            if (!empty($persistedFilterParameters['filter_category'])) {
                $categories->setData(array('tree' => array(0 => $persistedFilterParameters['filter_category'])));
            }
        }

        // when position_ordering, ignore all filters except filter_category
        if ($orderBy == 'position_ordering' && $hasCategoryFilter) {
            foreach ($persistedFilterParameters as $key => $param) {
                if (strpos($key, 'filter_column_') === 0) {
                    $persistedFilterParameters[$key] = '';
                }
            }
        }

        // Template vars injection
        return array_merge(
            $persistedFilterParameters,
            array(
                'limit' => $limit,
                'offset' => $offset,
                'orderBy' => $orderBy,
                'sortOrder' => $sortOrder,
                'has_filter' => ($hasCategoryFilter | $hasColumnFilter),
                'has_category_filter' => $hasCategoryFilter,
                'has_column_filter' => $hasColumnFilter,
                'products' => $products,
                'last_sql' => $lastSql,
                'product_count_filtered' => $totalFilteredProductCount,
                'product_count' => $totalProductCount,
                'activate_drag_and_drop' => (('position_ordering' == $orderBy) || ('position' == $orderBy && 'asc' == $sortOrder && !$hasColumnFilter)),
                'pagination_parameters' => $paginationParameters,
                'layoutHeaderToolbarBtn' => $toolbarButtons,
                'categories' => $categories->createView(),
                'pagination_limit_choices' => $productProvider->getPaginationLimitChoices(),
                'import_link' => $this->get('prestashop.adapter.legacy.context')->getAdminLink('AdminImport', true, ['import_type' => 'products']),
                'sql_manager_add_link' => $this->get('prestashop.adapter.legacy.context')->getAdminLink('AdminRequestSql', true, ['addrequest_sql' => 1]),
            )
        );
    }

    /**
     * Get only the list of products to display on the main Admin Product page.
     * The full page that shows products list will subcall this action (from catalogAction).
     * URL example: /product/list/html/40/20/id_product/asc
     *
     * @Template
     * @param Request $request
     * @param integer $limit The size of the listing
     * @param integer $offset The offset of the listing
     * @param string $orderBy To order product list
     * @param string $sortOrder To order product list
     * @param string $view full|quicknav To change default template used to render the content
     * @return array Template vars
     */
    public function listAction(Request $request, $limit = 10, $offset = 0, $orderBy = 'id_product', $sortOrder = 'asc', $view = 'full')
    {
        $productProvider = $this->container->get('prestashop.core.admin.data_provider.product_interface');
        /* @var $productProvider ProductInterfaceProvider */
        $legacyContext = $this->container->get('prestashop.adapter.legacy.context');
        /* @var $legacyContext LegacyContext */
        $adminProductWrapper = $this->container->get('prestashop.adapter.admin.wrapper.product');
        $totalCount = 0;

        $products = $request->attributes->get('products', null); // get from action subcall data, if any
        $lastSql = $request->attributes->get('last_sql', null); // get from action subcall data, if any

        if ($products === null) {
            // get old values from persistence (before the current update)
            $persistedFilterParameters = $productProvider->getPersistedFilterParameters();

            if ($offset === 'last') {
                $offset = $persistedFilterParameters['last_offset'];
            }
            if ($limit === 'last') {
                $limit = $persistedFilterParameters['last_limit'];
            }
            if ($orderBy === 'last') {
                $orderBy = $persistedFilterParameters['last_orderBy'];
            }
            if ($sortOrder === 'last') {
                $sortOrder = $persistedFilterParameters['last_sortOrder'];
            }

            // 2 hooks are triggered here: actionAdminProductsListingFieldsModifier and actionAdminProductsListingResultsModifier
            $products = $productProvider->getCatalogProductList($offset, $limit, $orderBy, $sortOrder);
            $lastSql = $productProvider->getLastCompiledSql();
        }
        $hasColumnFilter = $productProvider->isColumnFiltered();

        // Adds controller info (URLs, etc...) to product list
        foreach ($products as &$product) {
            $totalCount = isset($product['total'])? $product['total'] : $totalCount;
            $product['url'] = $this->generateUrl('admin_product_form', array('id' => $product['id_product']));
            $product['unit_action_url'] = $this->generateUrl(
                'admin_product_unit_action',
                array('action' => 'duplicate', 'id' => $product['id_product'])
            );
            $product['preview_url'] = $adminProductWrapper->getPreviewUrlFromId($product['id_product']);
        }

        // Template vars injection
        $vars = array(
            'activate_drag_and_drop' => (('position_ordering' == $orderBy) || ('position' == $orderBy && 'asc' == $sortOrder && !$hasColumnFilter)),
            'products' => $products,
            'product_count' => $totalCount,
            'last_sql_query' => $lastSql,
        );
        if ($view != 'full') {
            return $this->render('PrestaShopBundle:Admin:Product/list_'.$view.'.html.twig', array_merge($vars, [
                'limit' => $limit,
                'offset' => $offset,
                'total' => $totalCount,

            ]));
        }
        return $vars;
    }

    /**
     * Create a new basic product
     * Then return to form action
     *
     * @return RedirectResponse
     */
    public function newAction()
    {
        $contextAdapter = $this->get('prestashop.adapter.legacy.context');
        $context = $contextAdapter->getContext();
        $toolsAdapter = $this->container->get('prestashop.adapter.tools');
        $productAdapter = $this->container->get('prestashop.adapter.data_provider.product');
        $translator = $this->container->get('prestashop.adapter.translator');
        $name = $translator->trans('New product', [], 'AdminProducts');

        $product = $productAdapter->getProductInstance();
        $product->active = 0;
        $product->id_category_default = $context->shop->id_category;

        //set name and link_rewrite in each lang
        foreach ($contextAdapter->getLanguages() as $lang) {
            $product->name[$lang['id_lang']] = $name;
            $product->link_rewrite[$lang['id_lang']] = $toolsAdapter->link_rewrite($name);
        }

        $product->save();

        $product->addToCategories([$context->shop->id_category]);

        return $this->redirectToRoute('admin_product_form', ['id' => $product->id]);
    }

    /**
     * Product form
     *
     * @Template
     * @param int $id The product ID
     * @param Request $request
     * @return array Template vars
     */
    public function formAction($id, Request $request)
    {
        $productAdapter = $this->container->get('prestashop.adapter.data_provider.product');
        $product = $productAdapter->getProduct($id);
        if (!$product || empty($product->id)) {
            return $this->redirectToRoute('admin_product_catalog');
        }

        $shopContext = $this->get('prestashop.adapter.shop.context');
        $legacyContextService = $this->get('prestashop.adapter.legacy.context');
        $legacyContext = $legacyContextService->getContext();
        $isMultiShopContext = count($shopContext->getContextListShopID()) > 1 ? true : false;

        // Redirect to legacy controller (FIXME: temporary behavior)
        $pagePreference = $this->container->get('prestashop.core.admin.page_preference_interface');
        /* @var $pagePreference AdminPagePreferenceInterface */
        if ($pagePreference->getTemporaryShouldUseLegacyPage('product')) {
            $legacyUrlGenerator = $this->container->get('prestashop.core.admin.url_generator_legacy');
            /* @var $legacyUrlGenerator UrlGeneratorInterface */
            return $this->redirect($legacyUrlGenerator->generate('admin_product_form', array('id' => $id)), 302);
        }

        $response = new JsonResponse();
        $modelMapper = new ProductAdminModelAdapter(
            $product,
            $this->container->get('prestashop.adapter.legacy.context'),
            $this->container->get('prestashop.adapter.admin.wrapper.product'),
            $this->container->get('prestashop.adapter.tools'),
            $productAdapter,
            $this->container->get('prestashop.adapter.data_provider.supplier'),
            $this->container->get('prestashop.adapter.data_provider.warehouse'),
            $this->container->get('prestashop.adapter.data_provider.feature'),
            $this->container->get('prestashop.adapter.data_provider.pack'),
            $this->container->get('prestashop.adapter.shop.context')
        );
        $adminProductWrapper = $this->container->get('prestashop.adapter.admin.wrapper.product');

        $form = $this->createFormBuilder($modelMapper->getFormData())
            ->add('id_product', 'hidden')
            ->add('step1', new ProductForms\ProductInformation(
                $this->container->get('prestashop.adapter.translator'),
                $this->container->get('prestashop.adapter.legacy.context'),
                $this->container->get('router'),
                $this->container->get('prestashop.adapter.data_provider.category'),
                $productAdapter,
                $this->container->get('prestashop.adapter.data_provider.feature'),
                $this->container->get('prestashop.adapter.data_provider.manufacturer')
            ))
            ->add('step2', new ProductForms\ProductPrice(
                $this->container->get('prestashop.adapter.translator'),
                $this->container->get('prestashop.adapter.data_provider.tax'),
                $this->container->get('router'),
                $this->container->get('prestashop.adapter.shop.context'),
                $this->container->get('prestashop.adapter.data_provider.country'),
                $this->container->get('prestashop.adapter.data_provider.currency'),
                $this->container->get('prestashop.adapter.data_provider.group'),
                $this->container->get('prestashop.adapter.legacy.context'),
                $this->container->get('prestashop.adapter.data_provider.customer')
            ))
            ->add('step3', new ProductForms\ProductQuantity(
                $this->container->get('prestashop.adapter.translator'),
                $this->container->get('router'),
                $this->container->get('prestashop.adapter.legacy.context')
            ))
            ->add('step4', new ProductForms\ProductShipping(
                $this->container->get('prestashop.adapter.translator'),
                $this->container->get('prestashop.adapter.legacy.context'),
                $this->container->get('prestashop.adapter.data_provider.warehouse'),
                $this->container->get('prestashop.adapter.data_provider.carrier')
            ))
            ->add('step5', new ProductForms\ProductSeo(
                $this->container->get('prestashop.adapter.translator'),
                $this->container->get('prestashop.adapter.legacy.context')
            ))
            ->add('step6', new ProductForms\ProductOptions(
                $this->container->get('prestashop.adapter.translator'),
                $this->container->get('prestashop.adapter.legacy.context'),
                $productAdapter,
                $this->container->get('prestashop.adapter.data_provider.supplier'),
                $this->container->get('prestashop.adapter.data_provider.currency'),
                $this->container->get('prestashop.adapter.data_provider.attachment'),
                $this->container->get('router')
            ))
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                // Legacy code. To fix when Object model will change. But report Hooks.

                //define POST values for keeping legacy adminController skills
                $_POST = $modelMapper->getModelData($form->getData(), $isMultiShopContext);

                $adminProductController = $adminProductWrapper->getInstance();
                $adminProductController->setIdObject($form->getData()['id_product']);
                $adminProductController->setAction('save');

                // Hooks: this will trigger legacy AdminProductController, postProcess():
                // actionAdminSaveBefore; actionAdminProductsControllerSaveBefore
                // actionProductAdd or actionProductUpdate (from processSave() -> processAdd() or processUpdate())
                // actionAdminSaveAfter; actionAdminProductsControllerSaveAfter
                if ($product = $adminProductController->postCoreProcess()) {
                    $adminProductController->processSuppliers($product->id);
                    $adminProductController->processFeatures($product->id);
                    $adminProductController->processSpecificPricePriorities();
                    foreach ($_POST['combinations'] as $combinationValues) {
                        $adminProductWrapper->processProductAttribute($product, $combinationValues);
                        // For now, each attribute set the same value.
                        $adminProductWrapper->processDependsOnStock($product, ($_POST['depends_on_stock'] == '1'), $combinationValues['id_product_attribute']);
                    }
                    $adminProductWrapper->processDependsOnStock($product, ($_POST['depends_on_stock'] == '1'));

                    // If there is no combination, then quantity is managed for the whole product (as combination ID 0)
                    // In all cases, legacy hooks are triggered: actionProductUpdate and actionUpdateQuantity
                    if (count($_POST['combinations']) === 0) {
                        $adminProductWrapper->processQuantityUpdate($product, $_POST['qty_0']);
                    }
                    // else quantities are managed from $adminProductWrapper->processProductAttribute() above.

                    $adminProductWrapper->processProductOutOfStock($product, $_POST['out_of_stock']);
                    $adminProductWrapper->processProductCustomization($product, $_POST['custom_fields']);
                    $adminProductWrapper->processAttachments($product, $_POST['attachments']);

                    $adminProductController->processWarehouses();

                    $response->setData(['product' => $product]);
                }

                if ($request->isXmlHttpRequest()) {
                    return $response;
                }
            } elseif ($request->isXmlHttpRequest()) {
                $response->setStatusCode(400);
                $response->setData($this->getFormErrorsForJS($form));
                return $response;
            }
        }

        $stockManager = $this->container->get('prestashop.core.data_provider.stock_interface');
        /* @var $stockManager StockInterface */

        $warehouseProvider = $this->container->get('prestashop.adapter.data_provider.warehouse');
        /* @var $warehouseProvider WarehouseDataProvider */

        //If context shop is define to a group shop, disable the form
        if ($legacyContext->shop->getContext() == $shopContext->getShopContextGroupConstant()) {
            return $this->render('PrestaShopBundle:Admin/Product:formDisable.html.twig', ['showContentHeader' => false]);
        }

        // languages for switch dropdown
        $languages = $legacyContextService->getLanguages();

        return array(
            'form' => $form->createView(),
            'id_product' => $id,
            'has_combinations' => (isset($form->getData()['step3']['combinations']) && count($form->getData()['step3']['combinations']) > 0),
            'asm_globally_activated' => $stockManager->isAsmGloballyActivated(),
            'warehouses' => ($stockManager->isAsmGloballyActivated())? $warehouseProvider->getWarehouses() : [],
            'is_multishop_context' => $isMultiShopContext,
            'showContentHeader' => false,
            'preview_link' => $adminProductWrapper->getPreviewUrl($product),
            'stats_link' => $legacyContextService->getAdminLink('AdminStats', true, ['module' => 'statsproduct', 'id_product' => $id]),
            'help_link' => 'http://help.prestashop.com/'.$legacyContextService->getEmployeeLanguageIso().'/doc/'
                .'AdminProducts?version='._PS_VERSION_.'&country='.$legacyContextService->getEmployeeLanguageIso(),
            'languages' => $languages,
            'default_language_iso' => $languages[0]['iso_code'],
        );
    }

    /**
     * Do bulk action on a list of Products. Used with the 'selection action' dropdown menu on the Catalog page.
     *
     * @param Request $request
     * @param string $action The action to apply on the selected products
     * @throws \Exception If action not properly set or unknown.
     * @return void (redirection)
     */
    public function bulkAction(Request $request, $action)
    {
        $productIdList = $request->request->get('bulk_action_selected_products');
        $productUpdater = $this->container->get('prestashop.core.admin.data_updater.product_interface');
        /* @var $productUpdater ProductInterfaceUpdater */
        $translator = $this->container->get('prestashop.adapter.translator');
        /* @var $translator TranslatorInterface */

        $logger = $this->container->get('logger');
        /* @var $logger LoggerInterface */

        $hookEventParameters = ['product_list_id' => $productIdList];
        $hookDispatcher = $this->container->get('prestashop.hook.dispatcher');
        /* @var $hookDispatcher HookDispatcher */

        try {
            switch ($action) {
                case 'activate_all':
                    $hookDispatcher->dispatchMultiple(['actionAdminActivateBefore', 'actionAdminProductsControllerActivateBefore'], $hookEventParameters);
                    // Hooks: managed in ProductUpdater
                    $productUpdater->activateProductIdList($productIdList);
                    $this->addFlash('success', $translator->trans('Product(s) successfully activated.', [], 'AdminProducts'));
                    $logger->info('Products activated: ('.implode(',', $productIdList).').');
                    $hookDispatcher->dispatchMultiple(['actionAdminActivateAfter', 'actionAdminProductsControllerActivateAfter'], $hookEventParameters);
                    break;
                case 'deactivate_all':
                    $hookDispatcher->dispatchMultiple(['actionAdminDeactivateBefore', 'actionAdminProductsControllerDeactivateBefore'], $hookEventParameters);
                    // Hooks: managed in ProductUpdater
                    $productUpdater->activateProductIdList($productIdList, false);
                    $this->addFlash('success', $translator->trans('Product(s) successfully deactivated.', [], 'AdminProducts'));
                    $logger->info('Products deactivated: ('.implode(',', $productIdList).').');
                    $hookDispatcher->dispatchMultiple(['actionAdminDeactivateAfter', 'actionAdminProductsControllerDeactivateAfter'], $hookEventParameters);
                    break;
                case 'delete_all':
                    $hookDispatcher->dispatchMultiple(['actionAdminDeleteBefore', 'actionAdminProductsControllerDeleteBefore'], $hookEventParameters);
                    // Hooks: managed in ProductUpdater
                    $productUpdater->deleteProductIdList($productIdList);
                    $this->addFlash('success', $translator->trans('Product(s) successfully deleted.', [], 'AdminProducts'));
                    $logger->info('Products deleted: ('.implode(',', $productIdList).').');
                    $hookDispatcher->dispatchMultiple(['actionAdminDeleteAfter', 'actionAdminProductsControllerDeleteAfter'], $hookEventParameters);
                    break;
                case 'duplicate_all':
                    $hookDispatcher->dispatchMultiple(['actionAdminDuplicateBefore', 'actionAdminProductsControllerDuplicateBefore'], $hookEventParameters);
                    // Hooks: managed in ProductUpdater
                    $productUpdater->duplicateProductIdList($productIdList);
                    $this->addFlash('success', $translator->trans('Product(s) successfully duplicated.', [], 'AdminProducts'));
                    $logger->info('Products duplicated: ('.implode(',', $productIdList).').');
                    $hookDispatcher->dispatchMultiple(['actionAdminDuplicateAfter', 'actionAdminProductsControllerDuplicateAfter'], $hookEventParameters);
                    break;
                default:
                    // should never happens since the route parameters are restricted to a set of action values in YML file.
                    $logger->error('Bulk action from ProductController received a bad parameter.');
                    throw new \Exception('Bad action received from call to ProductController::bulkAction: "'.$action.'"', 2001);
            }
        } catch (DataUpdateException $due) {
            //TODO : need to translate this with an domain name
            $message = $due->getMessage();
            $this->addFlash('failure', $message);
            $logger->warning($message);
        }

        // redirect after success
        if ($request->request->has('redirect_url')) {
            return $this->redirect($request->request->get('redirect_url'), 302);
        }
        return new Response(json_encode(array('result' => 'ok')));
    }

    /**
     * Do mass edit action on the current page of products. Used with the 'grouped action' dropdown menu on the Catalog page.
     *
     * @param Request $request
     * @param string $action The action to apply on the selected products
     * @throws \Exception If action not properly set or unknown.
     * @return void (redirection)
     */
    public function massEditAction(Request $request, $action)
    {
        $productProvider = $this->container->get('prestashop.core.admin.data_provider.product_interface');
        /* @var $productProvider ProductInterfaceProvider */
        $productUpdater = $this->container->get('prestashop.core.admin.data_updater.product_interface');
        /* @var $productUpdater ProductInterfaceUpdater */
        $translator = $this->container->get('prestashop.adapter.translator');
        /* @var $translator TranslatorInterface */

        $logger = $this->container->get('logger');
        /* @var $logger LoggerInterface */

        $hookDispatcher = $this->container->get('prestashop.hook.dispatcher');
        /* @var $hookDispatcher HookDispatcher */

        try {
            switch ($action) {
                case 'sort':
                    $productIdList = $request->request->get('mass_edit_action_sorted_products');
                    $productPositionList = $request->request->get('mass_edit_action_sorted_positions');
                    $hookDispatcher->dispatchMultiple(
                        ['actionAdminSortBefore', 'actionAdminProductsControllerSortBefore'],
                        ['product_list_id' => $productIdList, 'product_list_position' => $productPositionList]
                    );
                    // Hooks: managed in ProductUpdater
                    $productUpdater->sortProductIdList(array_combine($productIdList, $productPositionList), $productProvider->getPersistedFilterParameters());
                    $this->addFlash('success', $translator->trans('Products successfully sorted.', [], 'AdminProducts'));
                    $logger->info('Products sorted: ('.implode(',', $productIdList).') with positions ('.implode(',', $productPositionList).').');
                    $hookDispatcher->dispatchMultiple(
                        ['actionAdminSortAfter', 'actionAdminProductsControllerSortAfter'],
                        ['product_list_id' => $productIdList, 'product_list_position' => $productPositionList]
                    );
                    break;
                default:
                    // should never happens since the route parameters are restricted to a set of action values in YML file.
                    $logger->error('Mass edit action from ProductController received a bad parameter.');
                    throw new \Exception('Bad action received from call to ProductController::massEditAction: "'.$action.'"', 2001);
            }
        } catch (DataUpdateException $due) {
            //TODO : need to translate with domain name
            $message = $due->getMessage();
            $this->addFlash('failure', $message);
            $logger->warning($message);
        }

        // redirect after success
        if ($request->request->has('redirect_url')) {
            return $this->redirect($request->request->get('redirect_url'), 302);
        } else {
            $urlGenerator = $this->container->get('prestashop.core.admin.url_generator');
            return $this->redirect($urlGenerator->generate('admin_product_catalog'), 302);
        }
    }

    /**
     * Do action on one product at a time. Can be used at many places in the controller's page.
     *
     * @param Request $request
     * @param string $action The action to apply on the selected product
     * @throws \Exception If action not properly set or unknown.
     * @return void (redirection)
     */
    public function unitAction(Request $request, $action, $id)
    {
        $productUpdater = $this->container->get('prestashop.core.admin.data_updater.product_interface');
        /* @var $productUpdater ProductInterfaceUpdater */
        $translator = $this->container->get('prestashop.adapter.translator');
        /* @var $translator TranslatorInterface */

        $logger = $this->container->get('logger');
        /* @var $logger LoggerInterface */

        $hookEventParameters = ['product_id' => $id];
        $hookDispatcher = $this->container->get('prestashop.hook.dispatcher');
        /* @var $hookDispatcher HookDispatcher */

        try {
            switch ($action) {
                case 'delete':
                    $hookDispatcher->dispatchMultiple(['actionAdminDeleteBefore', 'actionAdminProductsControllerDeleteBefore'], $hookEventParameters);
                    // Hooks: managed in ProductUpdater
                    $productUpdater->deleteProduct($id);
                    $this->addFlash('success', $translator->trans('Product successfully deleted.', [], 'AdminProducts'));
                    $logger->info('Product deleted: (' . $id . ').');
                    $hookDispatcher->dispatchMultiple(['actionAdminDeleteAfter', 'actionAdminProductsControllerDeleteAfter'], $hookEventParameters);
                    break;
                case 'duplicate':
                    $hookDispatcher->dispatchMultiple(['actionAdminDuplicateBefore', 'actionAdminProductsControllerDuplicateBefore'], $hookEventParameters);
                    // Hooks: managed in ProductUpdater
                    $duplicateProductId = $productUpdater->duplicateProduct($id);
                    $this->addFlash('success', $translator->trans('Product successfully duplicated.', [], 'AdminProducts'));
                    $logger->info('Product duplicated: (from ' . $id . ' to ' . $duplicateProductId . ').');
                    $hookDispatcher->dispatchMultiple(['actionAdminDuplicateAfter', 'actionAdminProductsControllerDuplicateAfter'], $hookEventParameters);
                    // stops here and redirect to the new product's page.
                    return $this->redirectToRoute('admin_product_form', array('id' => $duplicateProductId), 302);
                case 'activate':
                    $hookDispatcher->dispatchMultiple(['actionAdminActivateBefore', 'actionAdminProductsControllerActivateBefore'], $hookEventParameters);
                    // Hooks: managed in ProductUpdater
                    $productUpdater->activateProductIdList([$id]);
                    $this->addFlash('success', $translator->trans('Product successfully activated.', [], 'AdminProducts'));
                    $logger->info('Product activated: '.$id);
                    $hookDispatcher->dispatchMultiple(['actionAdminActivateAfter', 'actionAdminProductsControllerActivateAfter'], $hookEventParameters);
                    break;
                case 'deactivate':
                    $hookDispatcher->dispatchMultiple(['actionAdminDeactivateBefore', 'actionAdminProductsControllerDeactivateBefore'], $hookEventParameters);
                    // Hooks: managed in ProductUpdater
                    $productUpdater->activateProductIdList([$id], false);
                    $this->addFlash('success', $translator->trans('Product successfully deactivated.', [], 'AdminProducts'));
                    $logger->info('Product deactivated: '.$id);
                    $hookDispatcher->dispatchMultiple(['actionAdminDeactivateAfter', 'actionAdminProductsControllerDeactivateAfter'], $hookEventParameters);
                    break;
                default:
                    // should never happens since the route parameters are restricted to a set of action values in YML file.
                    $logger->error('Unit action from ProductController received a bad parameter.');
                    throw new \Exception('Bad action received from call to ProductController::unitAction: "' . $action . '"', 2002);
            }
        } catch (DataUpdateException $due) {
            //TODO : need to translate with a domain name
            $message = $due->getMessage();
            $this->addFlash('failure', $message);
            $logger->warning($message);
        }

        // redirect after success
        if ($request->request->has('redirect_url')) {
            return $this->redirect($request->get('redirect_url'), 302);
        } else {
            $urlGenerator = $this->container->get('prestashop.core.admin.url_generator');
            return $this->redirect($urlGenerator->generate('admin_product_catalog'), 302);
        }
    }

    /**
     * Export product list (like the catalog should list, taking into account the filters, but not the pagination)
     * in CSV format (or else for later if needed).
     *
     * This action does not finish correctly: a die is done to stop the stream that is downloaded by the browser.
     * So Symfony router cannot take back the hand of the process for the last event listeners (terminate events).
     *
     * @param string $_format The format of the output
     */
    public function exportAction($_format)
    {
        // init vars
        $productProvider = $this->container->get('prestashop.core.admin.data_provider.product_interface');
        /* @var $productProvider ProductInterfaceProvider */
        $csvTools = $this->container->get('prestashop.csv');
        /* @var $csvTools Csv */

        $persistedFilterParameters = $productProvider->getPersistedFilterParameters();
        $orderBy = $persistedFilterParameters['last_orderBy'];
        $sortOrder = $persistedFilterParameters['last_sortOrder'];

        // prepare callback to fetch data from DB
        $dataCallback = function ($offset, $limit) use ($productProvider, $orderBy, $sortOrder) {
            return $productProvider->getCatalogProductList($offset, $limit, $orderBy, $sortOrder, array(), true, false);
        };

        // export CSV
        $csvTools->exportData(
            $dataCallback,
            [   'id_product' => 'ID',
                'image' => 'Image',
                'name' => 'Name',
                'reference' => 'Reference',
                'name_category' => 'Category',
                'price' => 'Base price',
                'price_final' => 'Final price',
                'sav_quantity' => 'Quantity',
                'badge_danger' => 'Status',
                'position' => 'Position',
            ],
            100,
            'product_'.date('Y-m-d_His').'.csv',
            30*60, // 30 minutes of download max!
            true // TODO: windows CRLF, to make dynamic or always ON?
        );
        // exportData will "die" at the end of its process.
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
     * @param boolean $use True to use legacy version. False for refactored page.
     * @return void (redirection)
     */
    public function shouldUseLegacyPagesAction($use)
    {
        $pagePreference = $this->container->get('prestashop.core.admin.page_preference_interface');
        /* @var $pagePreference AdminPagePreferenceInterface */
        $pagePreference->setTemporaryShouldUseLegacyPage('product', $use);

        $hookDispatcher = $this->container->get('prestashop.hook.dispatcher');
        /* @var $hookDispatcher HookDispatcher */
        $hookDispatcher->dispatch('shouldUseLegacyPage', (new HookEvent())->setHookParameters(['page' => 'product', 'use_legacy' => $use]));

        $logger = $this->container->get('logger');
        /* @var $logger LoggerInterface */
        $logger->info('Changed setting to use '.($use?'legacy':'new version').' pages for ProductController.');

        // Then redirect
        $urlGenerator = $this->container->get($use?'prestashop.core.admin.url_generator_legacy':'prestashop.core.admin.url_generator');
        /* @var $urlGenerator UrlGeneratorInterface */
        return $this->redirect($urlGenerator->generate('admin_product_catalog'), 302);
    }
}
