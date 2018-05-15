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
namespace PrestaShopBundle\Controller\Admin;

use PrestaShop\PrestaShop\Adapter\Tax\TaxRuleDataProvider;
use PrestaShop\PrestaShop\Adapter\Warehouse\WarehouseDataProvider;
use PrestaShopBundle\Component\CsvResponse;
use PrestaShopBundle\Entity\AdminFilter;
use PrestaShopBundle\Security\Voter\PageVoter;
use PrestaShopBundle\Service\DataProvider\StockInterface;
use PrestaShopBundle\Service\Hook\HookEvent;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use PrestaShopBundle\Service\Hook\HookFinder;
use PrestaShopBundle\Service\TransitionalBehavior\AdminPagePreferenceInterface;
use PrestaShopBundle\Service\DataProvider\Admin\ProductInterface as ProductInterfaceProvider;
use PrestaShopBundle\Service\DataUpdater\Admin\ProductInterface as ProductInterfaceUpdater;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Log\LoggerInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use PrestaShopBundle\Exception\UpdateProductException;
use PrestaShopBundle\Model\Product\AdminModelAdapter as ProductAdminModelAdapter;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Translation\TranslatorInterface;
use PrestaShopBundle\Service\Csv;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Product;
use Tools;

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
     * @Template("@PrestaShop/Admin/Product/CatalogPage/catalog.html.twig")
     * @param Request $request
     * @param integer $limit The size of the listing
     * @param integer $offset The offset of the listing
     * @param string $orderBy To order product list
     * @param string $sortOrder To order product list
     * @return array Template vars
     */
    public function catalogAction(Request $request, $limit = 10, $offset = 0, $orderBy = 'id_product', $sortOrder = 'desc')
    {
        if (
            !$this->isGranted(PageVoter::READ, 'ADMINPRODUCTS_')
            && !$this->isGranted(PageVoter::UPDATE, 'ADMINPRODUCTS_')
            && !$this->isGranted(PageVoter::CREATE, 'ADMINPRODUCTS_')
        ) {
            return $this->redirect('admin_dashboard');
        }

        /**
         * Parameters can be overwritten with urls:
         *
         * @example ?limit=100&offset=2&orderBy=name&sortOrder=desc
         */
        $limit = $request->query->get('limit', $limit);
        $offset = $request->query->get('offset', $offset);
        $orderBy = $request->query->get('orderBy', $orderBy);
        $sortOrder = $request->query->get('sortOrder', $sortOrder);

        $context = $this->get('prestashop.adapter.legacy.context')->getContext();
        $request->getSession()->set('_locale', $context->language->locale);

        // Redirect to legacy controller (FIXME: temporary behavior)
        $pagePreference = $this->get('prestashop.core.admin.page_preference_interface');
        /* @var $pagePreference AdminPagePreferenceInterface */
        if ($pagePreference->getTemporaryShouldUseLegacyPage('product')) {
            $legacyUrlGenerator = $this->get('prestashop.core.admin.url_generator_legacy');
            /* @var $legacyUrlGenerator UrlGeneratorInterface */
            $redirectionParams = array(
                // do not transmit limit & offset: go to the first page when redirecting
                'productOrderby' => $orderBy,
                'productOrderway' => $sortOrder
            );
            return $this->redirect($legacyUrlGenerator->generate('admin_product_catalog', $redirectionParams));
        }

        // If POST, then check/cast POST params formats
        if ($request->isMethod('POST')) {
            foreach ($request->request->all() as $param => $value) {
                switch ($param) {
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

        /* @var $logger LoggerInterface */
        $logger = $this->get('logger');

        /* @var $productProvider ProductInterfaceProvider */
        $productProvider = $this->get('prestashop.core.admin.data_provider.product_interface');

        /* @var $translator TranslatorInterface */
        $translator = $this->get('translator');

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
        $toolbarButtons['add'] = array(
            'href' => $this->generateUrl('admin_product_new'),
            'desc' => $translator->trans('New product', array(), 'Admin.Actions'),
            'icon' => 'add_circle_outline',
            'help' => $translator->trans('Create a new product: CTRL+P', array(), 'Admin.Catalog.Help'),
        );

        // Fetch product list (and cache it into view subcall to listAction)
        $products = $productProvider->getCatalogProductList($offset, $limit, $orderBy, $sortOrder, $request->request->all());
        $lastSql = $productProvider->getLastCompiledSql();
        $logger->info('Product catalog filters stored.');
        $hasCategoryFilter = $productProvider->isCategoryFiltered();
        $hasColumnFilter = $productProvider->isColumnFiltered();

        // Alternative layout for empty list
        $totalFilteredProductCount = (count($products) > 0) ? $products[0]['total'] : 0;
        if ((!$hasCategoryFilter && !$hasColumnFilter && $totalFilteredProductCount === 0)
            || ($totalProductCount = $productProvider->countAllProducts()) === 0
        ) {
            // no filter, total filtered == 0, and then total count == 0 too.
            $legacyUrlGenerator = $this->get('prestashop.core.admin.url_generator_legacy');
            return $this->render('PrestaShopBundle:Admin/Product/CatalogPage:catalog_empty.html.twig', array(
                'layoutHeaderToolbarBtn' => $toolbarButtons,
                'import_url' => $legacyUrlGenerator->generate('AdminImport'),
            ));
        } else {
            // Pagination
            $paginationParameters = $request->attributes->all();
            $paginationParameters['_route'] = 'admin_product_catalog';

            // Category tree
            $categories = $this->createForm(
                'PrestaShopBundle\Form\Admin\Type\ChoiceCategoriesTreeType',
                null,
                array(
                    'label' => $translator->trans('Categories', array(), 'Admin.Catalog.Feature'),
                    'list' => $this->get('prestashop.adapter.data_provider.category')->getNestedCategories(null, $context->language->id, false),
                    'valid_list' => [],
                    'multiple' => false,
                )
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

        $permissionError = null;
        if ($this->get('session')->getFlashBag()->has('permission_error')) {
            $permissionError = $this->get('session')->getFlashBag()->get('permission_error')[0];
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
                'enableSidebar' => true,
                'help_link' => $this->generateSidebarLink('AdminProducts'),
                'is_shop_context' => $this->get('prestashop.adapter.shop.context')->isShopContext(),
                'permission_error' => $permissionError,
                'layoutTitle' => $this->trans('Products', 'Admin.Global'),
            )
        );
    }

    /**
     * Get only the list of products to display on the main Admin Product page.
     * The full page that shows products list will subcall this action (from catalogAction).
     * URL example: /product/list/html/40/20/id_product/asc
     *
     * @Template("@PrestaShop/Admin/Product/CatalogPage/Lists/list.html.twig")
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
        /* @var $productProvider ProductInterfaceProvider */
        $productProvider = $this->get('prestashop.core.admin.data_provider.product_interface');
        $adminProductWrapper = $this->get('prestashop.adapter.admin.wrapper.product');
        $totalCount = 0;

        $this->get('prestashop.service.product')->cleanupOldTempProducts();

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
            $totalCount = isset($product['total']) ? $product['total'] : $totalCount;
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
            'has_category_filter' => $productProvider->isCategoryFiltered(),
            'is_shop_context' => $this->get('prestashop.adapter.shop.context')->isShopContext(),
        );
        if ($view != 'full') {
            return $this->render('@Product/CatalogPage/Lists/list_' . $view . '.html.twig', array_merge($vars, [
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
        if (!$this->isGranted(PageVoter::CREATE, 'ADMINPRODUCTS_')) {
            $translator = $this->get('translator');
            $errorMessage = $translator->trans(
                'You do not have permission to add this.',
                array(),
                'Admin.Notifications.Error'
            );
            $this->get('session')->getFlashBag()->add('permission_error', $errorMessage);

            return $this->redirectToRoute('admin_product_catalog');
        }

        $productProvider = $this->get('prestashop.core.admin.data_provider.product_interface');

        /* @var $productProvider ProductInterfaceProvider */
        $contextAdapter = $this->get('prestashop.adapter.legacy.context');
        $context = $contextAdapter->getContext();
        $productAdapter = $this->get('prestashop.adapter.data_provider.product');

        /** @var Product $product */
        $product = $productAdapter->getProductInstance();
        $product->id_category_default = $context->shop->id_category;
        /** @var TaxRuleDataProvider $taxRuleDataProvider */
        $taxRuleDataProvider = $this->get('prestashop.adapter.data_provider.tax');
        $product->id_tax_rules_group = $taxRuleDataProvider->getIdTaxRulesGroupMostUsed();
        $product->active = $productProvider->isNewProductDefaultActivated() ? 1 : 0;
        $product->state = Product::STATE_TEMP;

        //set name and link_rewrite in each lang
        foreach ($contextAdapter->getLanguages() as $lang) {
            $product->name[$lang['id_lang']] = '';
            $product->link_rewrite[$lang['id_lang']] = '';
        }

        $product->save();
        $product->addToCategories([$context->shop->id_category]);

        return $this->redirectToRoute('admin_product_form', ['id' => $product->id]);
    }

    /**
     * Product form
     *
     * @Template("@PrestaShop/Admin/Product/ProductPage/product.html.twig")
     * @param int $id The product ID
     * @param Request $request
     * @return array|Response Template vars
     */
    public function formAction($id, Request $request)
    {
        gc_disable();
        if (
            !$this->isGranted(PageVoter::READ, 'ADMINPRODUCTS_')
            && !$this->isGranted(PageVoter::UPDATE, 'ADMINPRODUCTS_')
            && !$this->isGranted(PageVoter::CREATE, 'ADMINPRODUCTS_')
        ) {
            return $this->redirect('admin_dashboard');
        }

        $productAdapter = $this->get('prestashop.adapter.data_provider.product');
        $product = $productAdapter->getProduct($id);

        if (!$product || empty($product->id)) {
            return $this->redirectToRoute('admin_product_catalog');
        }

        $shopContext = $this->get('prestashop.adapter.shop.context');
        $legacyContextService = $this->get('prestashop.adapter.legacy.context');
        $isMultiShopContext = count($shopContext->getContextListShopID()) > 1 ? true : false;

        // Redirect to legacy controller (FIXME: temporary behavior)
        $pagePreference = $this->get('prestashop.core.admin.page_preference_interface');
        /* @var $pagePreference AdminPagePreferenceInterface */
        if ($pagePreference->getTemporaryShouldUseLegacyPage('product')) {
            $legacyUrlGenerator = $this->get('prestashop.core.admin.url_generator_legacy');
            /* @var $legacyUrlGenerator UrlGeneratorInterface */
            return $this->redirect($legacyUrlGenerator->generate('admin_product_form', array('id' => $id)));
        }

        $response = new JsonResponse();
        $modelMapper = new ProductAdminModelAdapter(
            $product,
            $this->get('prestashop.adapter.legacy.context'),
            $this->get('prestashop.adapter.admin.wrapper.product'),
            $this->get('prestashop.adapter.tools'),
            $productAdapter,
            $this->get('prestashop.adapter.data_provider.supplier'),
            $this->get('prestashop.adapter.data_provider.warehouse'),
            $this->get('prestashop.adapter.data_provider.feature'),
            $this->get('prestashop.adapter.data_provider.pack'),
            $this->get('prestashop.adapter.shop.context'),
            $this->get('prestashop.adapter.data_provider.tax')
        );
        $adminProductWrapper = $this->get('prestashop.adapter.admin.wrapper.product');

        $form = $this->createFormBuilder($modelMapper->getFormData(), array('allow_extra_fields' => true))
            ->add('id_product', 'Symfony\Component\Form\Extension\Core\Type\HiddenType')
            ->add('step1', 'PrestaShopBundle\Form\Admin\Product\ProductInformation')
            ->add('step2', 'PrestaShopBundle\Form\Admin\Product\ProductPrice')
            ->add('step3', 'PrestaShopBundle\Form\Admin\Product\ProductQuantity')
            ->add('step4', 'PrestaShopBundle\Form\Admin\Product\ProductShipping')
            ->add('step5', 'PrestaShopBundle\Form\Admin\Product\ProductSeo', array(
                'mapping_type' => $product->getRedirectType(),
            ))
            ->add('step6', 'PrestaShopBundle\Form\Admin\Product\ProductOptions');

        // Prepare combination form (fake but just to validate the form)
        $combinations = $modelMapper->getAttributesResume();

        if (is_array($combinations)) {
            $maxInputVars = (int) ini_get('max_input_vars');
            $combinationsCount = count($combinations) * 25;
            $combinationsInputs = ceil($combinationsCount/1000)*1000;

            if ($combinationsInputs > $maxInputVars) {

                $this->addFlash('error', $this->trans(
                    'The value of the PHP.ini setting "max_input_vars" must be increased to %value% in order to be able to submit the product form.',
                    'Admin.Global.Error',
                    array('%value%' => $combinationsInputs)
                ));
            }


            foreach ($combinations as $combination) {
                $form->add(
                    'combination_'.$combination['id_product_attribute'],
                    'PrestaShopBundle\Form\Admin\Product\ProductCombination'
                );
            }
        }

        $form = $form->getForm();

        $formBulkCombinations = $this->createForm(
            'PrestaShopBundle\Form\Admin\Product\ProductCombinationBulk',
            null,
            array(
                'iso_code' => $this
                    ->get('prestashop.adapter.legacy.context')
                    ->getContext()->currency->iso_code,
                'price_display_precision' => $this->configuration
                    ->get('_PS_PRICE_DISPLAY_PRECISION_'),
            )
        );

        // Legacy code. To fix when Object model will change. But report Hooks.
        $postData = $request->request->all();
        $combinationsList = array();
        if (!empty($postData)) {
            foreach ((array)$postData as $postKey => $postValue) {
                if (preg_match('/^combination_.*/', $postKey)) {
                    $combinationsList[$postKey] = $postValue;
                    $postData['form'][$postKey] = $postValue; // need to validate the form
                }
            }

            // Duplicate Request to be a valid form (like it was real) with postData modified ..
            $request = $request->duplicate(
                $request->query->all(),
                $postData,
                $request->attributes->all(),
                $request->cookies->all(),
                $request->files->all(),
                $request->server->all()
            );
        }

        /* @var $form Form */
        $form->handleRequest($request);
        $formData = $form->getData();
        $formData['step3']['combinations'] = $combinationsList;

        if ($form->isSubmitted()) {
            if ($this->isDemoModeEnabled() && $request->isXmlHttpRequest()) {
                $errorMessage = $this->getDemoModeErrorMessage();

                return new JsonResponse(['error' => [$errorMessage]], 503);
            }

            if ($form->isValid()) {

                //define POST values for keeping legacy adminController skills
                $_POST = $modelMapper->getModelData($formData, $isMultiShopContext) + $_POST;
                $_POST['state'] = Product::STATE_SAVED;

                $adminProductController = $adminProductWrapper->getInstance();
                $adminProductController->setIdObject($formData['id_product']);
                $adminProductController->setAction('save');

                // Hooks: this will trigger legacy AdminProductController, postProcess():
                // actionAdminSaveBefore; actionAdminProductsControllerSaveBefore
                // actionProductAdd or actionProductUpdate (from processSave() -> processAdd() or processUpdate())
                // actionAdminSaveAfter; actionAdminProductsControllerSaveAfter
                if ($product = $adminProductController->postCoreProcess()) {
                    /* @var $product Product */
                    $adminProductController->processSuppliers($product->id);
                    $adminProductController->processFeatures($product->id);
                    $adminProductController->processSpecificPricePriorities();
                    foreach ($_POST['combinations'] as $combinationValues) {
                        $adminProductWrapper->processProductAttribute($product, $combinationValues);
                        // For now, each attribute set the same value.
                        $adminProductWrapper->processDependsOnStock(
                            $product,
                            ($_POST['depends_on_stock'] == '1'),
                            $combinationValues['id_product_attribute']
                        );
                    }
                    $adminProductWrapper->processDependsOnStock($product, ($_POST['depends_on_stock'] == '1'));

                    // If there is no combination, then quantity is managed for the whole product (as combination ID 0)
                    // In all cases, legacy hooks are triggered: actionProductUpdate and actionUpdateQuantity
                    if (count($_POST['combinations']) === 0 && isset($_POST['qty_0'])) {
                        $adminProductWrapper->processQuantityUpdate($product, $_POST['qty_0']);
                    }
                    // else quantities are managed from $adminProductWrapper->processProductAttribute() above.

                    $adminProductWrapper->processProductOutOfStock($product, $_POST['out_of_stock']);

                    $customizationFieldsIds = $adminProductWrapper
                        ->processProductCustomization($product, $_POST['custom_fields']);

                    $adminProductWrapper->processAttachments($product, $_POST['attachments']);

                    $adminProductController->processWarehouses();

                    $response->setData([
                        'product' => $product,
                        'customization_fields_ids' => $customizationFieldsIds
                    ]);
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

        $stockManager = $this->get('prestashop.core.data_provider.stock_interface');
        /* @var $stockManager StockInterface */

        $warehouseProvider = $this->get('prestashop.adapter.data_provider.warehouse');
        /* @var $warehouseProvider WarehouseDataProvider */

        //If context shop is define to a group shop, disable the form
        if ($shopContext->isShopGroupContext()) {
            return $this->render('@Product/ProductPage/disabled_form_alert.html.twig', ['showContentHeader' => false]);
        }

        // languages for switch dropdown
        $languages = $legacyContextService->getLanguages();

        // generate url preview
        if ($product->active) {
            $preview_url = $adminProductWrapper->getPreviewUrl($product);
            $preview_url_deactive = $adminProductWrapper->getPreviewUrlDeactivate($preview_url);
        } else {
            $preview_url_deactive = $adminProductWrapper->getPreviewUrl($product, false);
            $preview_url = $adminProductWrapper->getPreviewUrlDeactivate($preview_url_deactive);
        }

        $attributeGroups = $this
            ->getDoctrine()
            ->getManager()
            ->getRepository('PrestaShopBundle:Attribute')
            ->findByLangAndShop(1, 1);

        $drawerModules = (new HookFinder())->setHookName('displayProductPageDrawer')
            ->setParams(array('product' => $product))
            ->addExpectedInstanceClasses('PrestaShop\PrestaShop\Core\Product\ProductAdminDrawer')
            ->present();

        return array(
            'form' => $form->createView(),
            'formCombinations' => $formBulkCombinations->createView(),
            'categories' => $this->get('prestashop.adapter.data_provider.category')->getCategoriesWithBreadCrumb(),
            'id_product' => $id,
            'ids_product_attribute' => (isset($formData['step3']['id_product_attributes']) ? implode(',', $formData['step3']['id_product_attributes']) : ''),
            'has_combinations' => (isset($formData['step3']['id_product_attributes']) && count($formData['step3']['id_product_attributes']) > 0),
            'combinations_count' => isset($formData['step3']['id_product_attributes']) ? count($formData['step3']['id_product_attributes']) : 0,
            'asm_globally_activated' => $stockManager->isAsmGloballyActivated(),
            'warehouses' => ($stockManager->isAsmGloballyActivated()) ? $warehouseProvider->getWarehouses() : [],
            'is_multishop_context' => $isMultiShopContext,
            'is_combination_active' => $this->get('prestashop.adapter.legacy.configuration')->combinationIsActive(),
            'showContentHeader' => false,
            'preview_link' => $preview_url,
            'preview_link_deactivate' => $preview_url_deactive,
            'stats_link' => $legacyContextService->getAdminLink('AdminStats', true, ['module' => 'statsproduct', 'id_product' => $id]),
            'help_link' => $this->generateSidebarLink('AdminProducts'),
            'languages' => $languages,
            'default_language_iso' => $languages[0]['iso_code'],
            'attribute_groups' => $attributeGroups,
            'max_upload_size' => Tools::formatBytes(UploadedFile::getMaxFilesize()),
            'is_shop_context' => $this->get('prestashop.adapter.shop.context')->isShopContext(),
            'editable' => $this->isGranted(PageVoter::UPDATE, 'ADMINPRODUCTS_'),
            'drawerModules' => $drawerModules,
            'layoutTitle' => $this->trans('Product', 'Admin.Global'),
        );
    }

    /**
     * Do bulk action on a list of Products. Used with the 'selection action' dropdown menu on the Catalog page.
     *
     * @param Request $request
     * @param string $action The action to apply on the selected products
     * @throws \Exception If action not properly set or unknown.
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function bulkAction(Request $request, $action)
    {
        $translator = $this->get('translator');

        if ($this->shouldDenyAction($action, '_all')) {
            $errorMessage = $this->getForbiddenActionMessage($action, '_all');
            $this->get('session')->getFlashBag()->add('permission_error', $errorMessage);

            return $this->redirectToRoute('admin_product_catalog');
        }

        $productIdList = $request->request->get('bulk_action_selected_products');
        $productUpdater = $this->get('prestashop.core.admin.data_updater.product_interface');
        /* @var $productUpdater ProductInterfaceUpdater */

        $logger = $this->get('logger');
        /* @var $logger LoggerInterface */

        $hookEventParameters = ['product_list_id' => $productIdList];
        $hookDispatcher = $this->get('prestashop.hook.dispatcher');
        /* @var $hookDispatcher HookDispatcher */

        try {
            $hasMessages = $this->get('session')->getFlashBag()->has('success');

            if ($this->isDemoModeEnabled()) {
                throw new UpdateProductException($this->getDemoModeErrorMessage());
            }

            switch ($action) {
                case 'activate_all':
                    $hookDispatcher->dispatchMultiple(['actionAdminActivateBefore', 'actionAdminProductsControllerActivateBefore'], $hookEventParameters);
                    // Hooks: managed in ProductUpdater
                    $productUpdater->activateProductIdList($productIdList);
                    if (empty($hasMessages)) {
                        $this->addFlash('success', $translator->trans('Product(s) successfully activated.', array(), 'Admin.Catalog.Notification'));
                    }
                    $logger->info('Products activated: (' . implode(',', $productIdList) . ').');
                    $hookDispatcher->dispatchMultiple(['actionAdminActivateAfter', 'actionAdminProductsControllerActivateAfter'], $hookEventParameters);
                    break;
                case 'deactivate_all':
                    $hookDispatcher->dispatchMultiple(['actionAdminDeactivateBefore', 'actionAdminProductsControllerDeactivateBefore'], $hookEventParameters);
                    // Hooks: managed in ProductUpdater
                    $productUpdater->activateProductIdList($productIdList, false);
                    if (empty($hasMessages)) {
                        $this->addFlash('success', $translator->trans('Product(s) successfully deactivated.', array(), 'Admin.Catalog.Notification'));
                    }
                    $logger->info('Products deactivated: (' . implode(',', $productIdList) . ').');
                    $hookDispatcher->dispatchMultiple(['actionAdminDeactivateAfter', 'actionAdminProductsControllerDeactivateAfter'], $hookEventParameters);
                    break;
                case 'delete_all':
                    $hookDispatcher->dispatchMultiple(['actionAdminDeleteBefore', 'actionAdminProductsControllerDeleteBefore'], $hookEventParameters);
                    // Hooks: managed in ProductUpdater
                    $productUpdater->deleteProductIdList($productIdList);
                    if (empty($hasMessages)) {
                        $this->addFlash('success', $translator->trans('Product(s) successfully deleted.', array(), 'Admin.Catalog.Notification'));
                    }
                    $logger->info('Products deleted: (' . implode(',', $productIdList) . ').');
                    $hookDispatcher->dispatchMultiple(['actionAdminDeleteAfter', 'actionAdminProductsControllerDeleteAfter'], $hookEventParameters);
                    break;
                case 'duplicate_all':
                    $hookDispatcher->dispatchMultiple(['actionAdminDuplicateBefore', 'actionAdminProductsControllerDuplicateBefore'], $hookEventParameters);
                    // Hooks: managed in ProductUpdater
                    $productUpdater->duplicateProductIdList($productIdList);
                    if (empty($hasMessages)) {
                        $this->addFlash('success', $translator->trans('Product(s) successfully duplicated.', array(), 'Admin.Catalog.Notification'));
                    }
                    $logger->info('Products duplicated: (' . implode(',', $productIdList) . ').');
                    $hookDispatcher->dispatchMultiple(['actionAdminDuplicateAfter', 'actionAdminProductsControllerDuplicateAfter'], $hookEventParameters);
                    break;
                default:
                    // should never happens since the route parameters are restricted to a set of action values in YML file.
                    $logger->error('Bulk action from ProductController received a bad parameter.');
                    throw new \Exception('Bad action received from call to ProductController::bulkAction: "' . $action . '"', 2001);
            }
        } catch (UpdateProductException $due) {
            //TODO : need to translate this with an domain name
            $message = $due->getMessage();
            $this->addFlash('failure', $message);
            $logger->warning($message);
        }

        return new Response(json_encode(array('result' => 'ok')));
    }

    /**
     * Do mass edit action on the current page of products. Used with the 'grouped action' dropdown menu on the Catalog page.
     *
     * @param Request $request
     * @param string $action The action to apply on the selected products
     * @throws \Exception If action not properly set or unknown.
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function massEditAction(Request $request, $action)
    {
        $translator = $this->get('translator');

        if (!$this->isGranted(PageVoter::UPDATE, 'ADMINPRODUCTS_')) {
            $errorMessage = $translator->trans(
                'You do not have permission to edit this.',
                array(),
                'Admin.Notifications.Error'
            );
            $this->get('session')->getFlashBag()->add('permission_error', $errorMessage);

            return $this->redirectToRoute('admin_product_catalog');
        }

        $productProvider = $this->get('prestashop.core.admin.data_provider.product_interface');
        /* @var $productProvider ProductInterfaceProvider */

        $productUpdater = $this->get('prestashop.core.admin.data_updater.product_interface');
        /* @var $productUpdater ProductInterfaceUpdater */

        $logger = $this->get('logger');
        /* @var $logger LoggerInterface */

        $hookDispatcher = $this->get('prestashop.hook.dispatcher');
        /* @var $hookDispatcher HookDispatcher */

        try {
            switch ($action) {
                case 'sort':
                    $productIdList = $request->request->get('mass_edit_action_sorted_products');
                    $productPositionList = $request->request->get('mass_edit_action_sorted_positions');
                    $hookDispatcher->dispatchMultiple(
                        array('actionAdminSortBefore', 'actionAdminProductsControllerSortBefore'),
                        array('product_list_id' => $productIdList, 'product_list_position' => $productPositionList)
                    );
                    // Hooks: managed in ProductUpdater
                    $persistedFilterParams = $productProvider->getPersistedFilterParameters();
                    $productList = array_combine($productIdList, $productPositionList);
                    $productUpdater->sortProductIdList(
                        $productList,
                        array('filter_category' => $persistedFilterParams['filter_category'])
                    );
                    $this->addFlash('success', $translator->trans('Products successfully sorted.', array(), 'Admin.Catalog.Notification'));
                    $logger->info('Products sorted: (' . implode(',', $productIdList) . ') with positions (' . implode(',', $productPositionList) . ').');
                    $hookDispatcher->dispatchMultiple(
                        array('actionAdminSortAfter', 'actionAdminProductsControllerSortAfter'),
                        array('product_list_id' => $productIdList, 'product_list_position' => $productPositionList)
                    );
                    break;
                default:
                    // should never happens since the route parameters are restricted to a set of action values in YML file.
                    $logger->error('Mass edit action from ProductController received a bad parameter.');
                    throw new \Exception('Bad action received from call to ProductController::massEditAction: "' . $action . '"', 2001);
            }
        } catch (UpdateProductException $due) {
            //TODO : need to translate with domain name
            $message = $due->getMessage();
            $this->addFlash('failure', $message);
            $logger->warning($message);
        }

        $urlGenerator = $this->get('prestashop.core.admin.url_generator');
        return $this->redirect($urlGenerator->generate('admin_product_catalog'));
    }

    /**
     * Do action on one product at a time. Can be used at many places in the controller's page.
     *
     * @param string $action The action to apply on the selected product
     * @param integer $id The product ID to apply the action on.
     * @throws \Exception If action not properly set or unknown.
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function unitAction($action, $id)
    {
        $translator = $this->get('translator');

        if ($this->shouldDenyAction($action)) {
            $errorMessage = $this->getForbiddenActionMessage($action);
            $this->get('session')->getFlashBag()->add('permission_error', $errorMessage);

            return $this->redirectToRoute('admin_product_catalog');
        }

        $productUpdater = $this->get('prestashop.core.admin.data_updater.product_interface');
        /* @var $productUpdater ProductInterfaceUpdater */

        $logger = $this->get('logger');
        /* @var $logger LoggerInterface */

        $hookEventParameters = ['product_id' => $id];
        $hookDispatcher = $this->get('prestashop.hook.dispatcher');
        /* @var $hookDispatcher HookDispatcher */

        try {
            if ($this->isDemoModeEnabled()) {
                throw new UpdateProductException($this->getDemoModeErrorMessage());
            }

            switch ($action) {
                case 'delete':
                    $hookDispatcher->dispatchMultiple(['actionAdminDeleteBefore', 'actionAdminProductsControllerDeleteBefore'], $hookEventParameters);
                    // Hooks: managed in ProductUpdater
                    $productUpdater->deleteProduct($id);
                    $this->addFlash('success', $translator->trans('Product successfully deleted.', array(), 'Admin.Catalog.Notification'));
                    $logger->info('Product deleted: (' . $id . ').');
                    $hookDispatcher->dispatchMultiple(['actionAdminDeleteAfter', 'actionAdminProductsControllerDeleteAfter'], $hookEventParameters);
                    break;
                case 'duplicate':
                    $hookDispatcher->dispatchMultiple(['actionAdminDuplicateBefore', 'actionAdminProductsControllerDuplicateBefore'], $hookEventParameters);
                    // Hooks: managed in ProductUpdater
                    $duplicateProductId = $productUpdater->duplicateProduct($id);
                    $this->addFlash('success', $translator->trans('Product successfully duplicated.', array(), 'Admin.Catalog.Notification'));
                    $logger->info('Product duplicated: (from ' . $id . ' to ' . $duplicateProductId . ').');
                    $hookDispatcher->dispatchMultiple(['actionAdminDuplicateAfter', 'actionAdminProductsControllerDuplicateAfter'], $hookEventParameters);
                    // stops here and redirect to the new product's page.
                    return $this->redirectToRoute('admin_product_form', array('id' => $duplicateProductId));
                case 'activate':
                    $hookDispatcher->dispatchMultiple(['actionAdminActivateBefore', 'actionAdminProductsControllerActivateBefore'], $hookEventParameters);
                    // Hooks: managed in ProductUpdater
                    $productUpdater->activateProductIdList([$id]);
                    $this->addFlash('success', $translator->trans('Product successfully activated.', array(), 'Admin.Catalog.Notification'));
                    $logger->info('Product activated: ' . $id);
                    $hookDispatcher->dispatchMultiple(['actionAdminActivateAfter', 'actionAdminProductsControllerActivateAfter'], $hookEventParameters);
                    break;
                case 'deactivate':
                    $hookDispatcher->dispatchMultiple(['actionAdminDeactivateBefore', 'actionAdminProductsControllerDeactivateBefore'], $hookEventParameters);
                    // Hooks: managed in ProductUpdater
                    $productUpdater->activateProductIdList([$id], false);
                    $this->addFlash('success', $translator->trans('Product successfully deactivated.', array(), 'Admin.Catalog.Notification'));
                    $logger->info('Product deactivated: ' . $id);
                    $hookDispatcher->dispatchMultiple(['actionAdminDeactivateAfter', 'actionAdminProductsControllerDeactivateAfter'], $hookEventParameters);
                    break;
                default:
                    // should never happens since the route parameters are restricted to a set of action values in YML file.
                    $logger->error('Unit action from ProductController received a bad parameter.');
                    throw new \Exception('Bad action received from call to ProductController::unitAction: "' . $action . '"', 2002);
            }
        } catch (UpdateProductException $due) {
            //TODO : need to translate with a domain name
            $message = $due->getMessage();
            $this->addFlash('failure', $message);
            $logger->warning($message);
        }

        $urlGenerator = $this->get('prestashop.core.admin.url_generator');
        return $this->redirect($urlGenerator->generate('admin_product_catalog'));
    }

    public function exportAction()
    {

        $productProvider = $this->get('prestashop.core.admin.data_provider.product_interface');

        $persistedFilterParameters = $productProvider->getPersistedFilterParameters();
        $orderBy = $persistedFilterParameters['last_orderBy'];
        $sortOrder = $persistedFilterParameters['last_sortOrder'];

        // prepare callback to fetch data from DB
        $dataCallback = function ($offset, $limit) use ($productProvider, $orderBy, $sortOrder) {
            return $productProvider->getCatalogProductList($offset, $limit, $orderBy, $sortOrder, array(), true, false);
        };

        $translator = $this->get('translator');

        $headersData = array(
            'id_product' => 'Product ID',
            'image_link' => $translator->trans('Image', array(), 'Admin.Global'),
            'name' => $translator->trans('Name', array(), 'Admin.Global'),
            'reference' => $translator->trans('Reference', array(), 'Admin.Global'),
            'name_category' => $translator->trans('Category', array(), 'Admin.Global'),
            'price' => $translator->trans('Price (tax excl.)', array(), 'Admin.Catalog.Feature'),
            'price_final' => $translator->trans('Price (tax incl.)', array(), 'Admin.Catalog.Feature'),
            'sav_quantity' => $translator->trans('Quantity', array(), 'Admin.Global'),
            'badge_danger' => $translator->trans('Status', array(), 'Admin.Global'),
            'position' => $translator->trans('Position', array(), 'Admin.Global'),
        );

        return (new CsvResponse())
            ->setData($dataCallback)
            ->setHeadersData($headersData)
            ->setModeType(CsvResponse::MODE_OFFSET)
            ->setLimit(5000)
            ->setFileName('product_' . date('Y-m-d_His') . '.csv');
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
        $pagePreference = $this->get('prestashop.core.admin.page_preference_interface');
        /* @var $pagePreference AdminPagePreferenceInterface */
        $pagePreference->setTemporaryShouldUseLegacyPage('product', $use);

        $hookDispatcher = $this->get('prestashop.hook.dispatcher');
        /* @var $hookDispatcher HookDispatcher */
        $hookDispatcher->dispatch('shouldUseLegacyPage', (new HookEvent())->setHookParameters(['page' => 'product', 'use_legacy' => $use]));

        $logger = $this->get('logger');
        /* @var $logger LoggerInterface */
        $logger->info('Changed setting to use ' . ($use ? 'legacy' : 'new version') . ' pages for ProductController.');

        // Then redirect
        $urlGenerator = $this->get($use ? 'prestashop.core.admin.url_generator_legacy' : 'prestashop.core.admin.url_generator');
        /* @var $urlGenerator UrlGeneratorInterface */
        return $this->redirect($urlGenerator->generate('admin_product_catalog'));
    }

    /**
     * Set the Catalog filters values and redirect to the catalogAction.
     *
     * URL example: /product/catalog_filters/42/last/32
     *
     * @param integer|string $quantity The quantity to set on the catalog filters persistence.
     * @param string $active The activation state to set on the catalog filters persistence.
     * @return void (redirection)
     */
    public function catalogFiltersAction($quantity = 'none', $active = 'none')
    {
        $quantity = urldecode($quantity);

        $productProvider = $this->get('prestashop.core.admin.data_provider.product_interface');
        /* @var $productProvider ProductInterfaceProvider */

        // we merge empty filter set with given values, to reset the other filters!
        $productProvider->persistFilterParameters(array_merge(AdminFilter::getProductCatalogEmptyFilter(), [
            'filter_column_sav_quantity' => ($quantity == 'none') ? '' : $quantity,
            'filter_column_active' => ($active == 'none') ? '' : $active
        ]));

        return $this->redirectToRoute('admin_product_catalog');
    }

    /**
     * @todo make a twig extension and depends only on the required form to avoid generate all the data
     */
    public function renderFieldAction($productId, $step, $fieldName)
    {
        $productAdapter = $this->get('prestashop.adapter.data_provider.product');
        $product = $productAdapter->getProduct($productId);

        $modelMapper = new ProductAdminModelAdapter(
            $product,
            $this->get('prestashop.adapter.legacy.context'),
            $this->get('prestashop.adapter.admin.wrapper.product'),
            $this->get('prestashop.adapter.tools'),
            $productAdapter,
            $this->get('prestashop.adapter.data_provider.supplier'),
            $this->get('prestashop.adapter.data_provider.warehouse'),
            $this->get('prestashop.adapter.data_provider.feature'),
            $this->get('prestashop.adapter.data_provider.pack'),
            $this->get('prestashop.adapter.shop.context'),
            $this->get('prestashop.adapter.data_provider.tax')
        );

        $form = $this->createFormBuilder($modelMapper->getFormData());

        switch ($step) {
            case 'step1':
                $form->add('step1', 'PrestaShopBundle\Form\Admin\Product\ProductInformation');
                break;
            case 'step2':
                $form->add('step2', 'PrestaShopBundle\Form\Admin\Product\ProductPrice');
                break;
            case 'step3':
                $form->add('step3', 'PrestaShopBundle\Form\Admin\Product\ProductQuantity');
                break;
            case 'step4':
                $form->add('step4', 'PrestaShopBundle\Form\Admin\Product\ProductShipping');
                break;
            case 'step5':
                $form->add('step5', 'PrestaShopBundle\Form\Admin\Product\ProductSeo');
                break;
            case 'step6':
                $form->add('step6', 'PrestaShopBundle\Form\Admin\Product\ProductOptions');
                break;
            case 'default':
        }

        return $this->render('PrestaShopBundle:Admin/Common/_partials:_form_field.html.twig', [
            'form' => $form->getForm()->get($step)->get($fieldName)->createView(),
            'formId' => $step . '_' . $fieldName . '_rendered'
        ]);
    }

    /**
     * @param $action
     * @param string $suffix
     * @return bool
     */
    protected function shouldDenyAction($action, $suffix = '')
    {
        return (
                $action === 'delete' . $suffix && !$this->isGranted(PageVoter::DELETE, 'ADMINPRODUCTS_')
            ) || (
                ($action === 'activate' . $suffix || $action === 'deactivate' . $suffix) &&
                !$this->isGranted(PageVoter::UPDATE, 'ADMINPRODUCTS_')
            ) || (
                ($action === 'duplicate' . $suffix) &&
                (!$this->isGranted(PageVoter::UPDATE, 'ADMINPRODUCTS_') || !$this->isGranted(PageVoter::CREATE, 'ADMINPRODUCTS_'))
            )
        ;
    }

    /**
     * @param $action
     * @param string $suffix
     * @return string
     * @throws \Exception
     */
    protected function getForbiddenActionMessage($action, $suffix = '')
    {
        $translator = $this->get('translator');

        if ($action === 'delete' . $suffix) {
            return $translator->trans(
                'You do not have permission to delete this.',
                array(),
                'Admin.Notifications.Error'
            );
        }

        if ($action === 'deactivate' . $suffix || $action === 'activate' . $suffix) {
            return $translator->trans(
                'You do not have permission to edit this.',
                array(),
                'Admin.Notifications.Error'
            );
        }

        if ($action === 'duplicate' . $suffix) {
            return $translator->trans(
                'You do not have permission to add this.',
                array(),
                'Admin.Notifications.Error'
            );
        }

        throw new \Exception(sprintf('Invalid action (%s)', $action . $suffix));
    }
}
