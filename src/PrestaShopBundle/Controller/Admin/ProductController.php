<?php
/**
 * 2007-2019 PrestaShop SA and Contributors
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

namespace PrestaShopBundle\Controller\Admin;

use Category;
use Exception;
use PrestaShop\PrestaShop\Adapter\Product\ListParametersUpdater;
use PrestaShop\PrestaShop\Adapter\Tax\TaxRuleDataProvider;
use PrestaShop\PrestaShop\Adapter\Warehouse\WarehouseDataProvider;
use PrestaShopBundle\Component\CsvResponse;
use PrestaShopBundle\Entity\AdminFilter;
use PrestaShopBundle\Exception\UpdateProductException;
use PrestaShopBundle\Form\Admin\Product\ProductCategories;
use PrestaShopBundle\Form\Admin\Product\ProductCombination;
use PrestaShopBundle\Form\Admin\Product\ProductCombinationBulk;
use PrestaShopBundle\Form\Admin\Product\ProductInformation;
use PrestaShopBundle\Form\Admin\Product\ProductOptions;
use PrestaShopBundle\Form\Admin\Product\ProductPrice;
use PrestaShopBundle\Form\Admin\Product\ProductQuantity;
use PrestaShopBundle\Form\Admin\Product\ProductSeo;
use PrestaShopBundle\Form\Admin\Product\ProductShipping;
use PrestaShopBundle\Model\Product\AdminModelAdapter;
use PrestaShopBundle\Security\Voter\PageVoter;
use PrestaShopBundle\Service\DataProvider\Admin\ProductInterface as ProductInterfaceProvider;
use PrestaShopBundle\Service\DataProvider\StockInterface;
use PrestaShopBundle\Service\DataUpdater\Admin\ProductInterface as ProductInterfaceUpdater;
use PrestaShopBundle\Service\Hook\HookFinder;
use Product;
use Psr\Log\LoggerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
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
 * code (and so needs to be adapted on the module side ton comply on the new parameters formats,
 * the new UI style, etc...).
 */
class ProductController extends FrameworkBundleAdminController
{
    /**
     * Used to validate connected user authorizations.
     */
    const PRODUCT_OBJECT = 'ADMINPRODUCTS_';

    /**
     * Get the Catalog page with KPI banner, product list, bulk actions, filters, search, etc...
     *
     * URL example: /product/catalog/40/20/id_product/asc
     *
     * @Template("@PrestaShop/Admin/Product/CatalogPage/catalog.html.twig")
     *
     * @param Request $request
     * @param int $limit The size of the listing
     * @param int $offset The offset of the listing
     * @param string $orderBy To order product list
     * @param string $sortOrder To order product list
     *
     * @return array|Template|RedirectResponse|Response
     *
     * @throws \Symfony\Component\Translation\Exception\InvalidArgumentException
     * @throws \Symfony\Component\Routing\Exception\RouteNotFoundException
     * @throws \LogicException
     * @throws \Symfony\Component\Routing\Exception\MissingMandatoryParametersException
     * @throws \Symfony\Component\Routing\Exception\InvalidParameterException
     * @throws \Symfony\Component\Form\Exception\LogicException
     * @throws \Symfony\Component\Form\Exception\AlreadySubmittedException
     */
    public function catalogAction(
        Request $request,
        $limit = 10,
        $offset = 0,
        $orderBy = 'id_product',
        $sortOrder = 'desc'
    ) {
        if (!$this->isGranted(array(PageVoter::READ, PageVoter::UPDATE, PageVoter::CREATE), self::PRODUCT_OBJECT)) {
            return $this->redirect('admin_dashboard');
        }

        $language = $this->getContext()->language;
        $request->getSession()->set('_locale', $language->locale);
        $request = $this->get('prestashop.adapter.product.filter_categories_request_purifier')->purify($request);

        /** @var $productProvider ProductInterfaceProvider */
        $productProvider = $this->get('prestashop.core.admin.data_provider.product_interface');

        // Set values from persistence and replace in the request
        $persistedFilterParameters = $productProvider->getPersistedFilterParameters();
        /** @var ListParametersUpdater $listParametersUpdater */
        $listParametersUpdater = $this->get('prestashop.adapter.product.list_parameters_updater');
        $listParameters = $listParametersUpdater->buildListParameters(
            $request->query->all(),
            $persistedFilterParameters,
            compact('offset', 'limit', 'orderBy', 'sortOrder')
        );
        $offset = $listParameters['offset'];
        $limit = $listParameters['limit'];
        $orderBy = $listParameters['orderBy'];
        $sortOrder = $listParameters['sortOrder'];

        //The product provider performs the same merge internally, so we do the same so that the displayed filters are
        //consistent with the request ones
        $combinedFilterParameters = array_replace($persistedFilterParameters, $request->request->all());

        $toolbarButtons = $this->getToolbarButtons();

        // Fetch product list (and cache it into view subcall to listAction)
        $products = $productProvider->getCatalogProductList(
            $offset,
            $limit,
            $orderBy,
            $sortOrder,
            $request->request->all()
        );
        $lastSql = $productProvider->getLastCompiledSql();

        $hasCategoryFilter = $productProvider->isCategoryFiltered();
        $hasColumnFilter = $productProvider->isColumnFiltered();
        $totalFilteredProductCount = (count($products) > 0) ? $products[0]['total'] : 0;
        // Alternative layout for empty list
        if ((!$hasCategoryFilter && !$hasColumnFilter && $totalFilteredProductCount === 0)
            || ($totalProductCount = $productProvider->countAllProducts()) === 0
        ) {
            // no filter, total filtered == 0, and then total count == 0 too.
            $legacyUrlGenerator = $this->get('prestashop.core.admin.url_generator_legacy');

            return $this->render(
                '@PrestaShop/Admin/Product/CatalogPage/catalog_empty.html.twig',
                [
                    'layoutHeaderToolbarBtn' => $toolbarButtons,
                    'import_url' => $legacyUrlGenerator->generate('AdminImport'),
                ]
            );
        }

        // Pagination
        $paginationParameters = $request->attributes->all();
        $paginationParameters['_route'] = 'admin_product_catalog';
        $categoriesForm = $this->createForm(ProductCategories::class);
        if (!empty($persistedFilterParameters['filter_category'])) {
            $categoriesForm->setData(
                [
                    'categories' => [
                        'tree' => [0 => $combinedFilterParameters['filter_category']],
                    ],
                ]
            );
        }

        $cleanFilterParameters = $listParametersUpdater->cleanFiltersForPositionOrdering(
            $combinedFilterParameters,
            $orderBy,
            $hasCategoryFilter
        );

        $permissionError = null;
        if ($this->get('session')->getFlashBag()->has('permission_error')) {
            $permissionError = $this->get('session')->getFlashBag()->get('permission_error')[0];
        }

        $categoriesFormView = $categoriesForm->createView();
        $selectedCategory = !empty($combinedFilterParameters['filter_category']) ? new Category($combinedFilterParameters['filter_category']) : null;

        //Drag and drop is ONLY activated when EXPLICITLY requested by the user
        //Meaning a category is selected and the user clicks on REORDER button
        $activateDragAndDrop = 'position_ordering' === $orderBy && $hasCategoryFilter;

        // Template vars injection
        return array_merge(
            $cleanFilterParameters,
            [
                'limit' => $limit,
                'offset' => $offset,
                'orderBy' => $orderBy,
                'sortOrder' => $sortOrder,
                'has_filter' => $hasCategoryFilter || $hasColumnFilter,
                'has_category_filter' => $hasCategoryFilter,
                'selected_category' => $selectedCategory,
                'has_column_filter' => $hasColumnFilter,
                'products' => $products,
                'last_sql' => $lastSql,
                'product_count_filtered' => $totalFilteredProductCount,
                'product_count' => $totalProductCount,
                'activate_drag_and_drop' => $activateDragAndDrop,
                'pagination_parameters' => $paginationParameters,
                'layoutHeaderToolbarBtn' => $toolbarButtons,
                'categories' => $categoriesFormView,
                'pagination_limit_choices' => $productProvider->getPaginationLimitChoices(),
                'import_link' => $this->generateUrl('admin_import', ['import_type' => 'products']),
                'sql_manager_add_link' => $this->generateUrl('admin_sql_requests_create'),
                'enableSidebar' => true,
                'help_link' => $this->generateSidebarLink('AdminProducts'),
                'is_shop_context' => $this->get('prestashop.adapter.shop.context')->isShopContext(),
                'permission_error' => $permissionError,
                'layoutTitle' => $this->trans('Products', 'Admin.Global'),
            ]
        );
    }

    /**
     * Get only the list of products to display on the main Admin Product page.
     * The full page that shows products list will subcall this action (from catalogAction).
     * URL example: /product/list/html/40/20/id_product/asc.
     *
     * @Template("@PrestaShop/Admin/Product/CatalogPage/Lists/list.html.twig")
     *
     * @param Request $request
     * @param int $limit The size of the listing
     * @param int $offset The offset of the listing
     * @param string $orderBy To order product list
     * @param string $sortOrder To order product list
     * @param string $view full|quicknav To change default template used to render the content
     *
     * @return array|Template|Response
     */
    public function listAction(
        Request $request,
        $limit = 10,
        $offset = 0,
        $orderBy = 'id_product',
        $sortOrder = 'asc',
        $view = 'full'
    ) {
        /** @var $productProvider ProductInterfaceProvider */
        $productProvider = $this->get('prestashop.core.admin.data_provider.product_interface');
        $adminProductWrapper = $this->get('prestashop.adapter.admin.wrapper.product');
        $totalCount = 0;

        $this->get('prestashop.service.product')->cleanupOldTempProducts();

        $products = $request->attributes->get('products', null); // get from action subcall data, if any
        $lastSql = $request->attributes->get('last_sql', null); // get from action subcall data, if any

        if ($products === null) {
            // get old values from persistence (before the current update)
            $persistedFilterParameters = $productProvider->getPersistedFilterParameters();
            /** @var ListParametersUpdater $listParametersUpdater */
            $listParametersUpdater = $this->get('prestashop.adapter.product.list_parameters_updater');
            $listParameters = $listParametersUpdater->buildListParameters(
                $request->query->all(),
                $persistedFilterParameters,
                compact('offset', 'limit', 'orderBy', 'sortOrder')
            );
            $offset = $listParameters['offset'];
            $limit = $listParameters['limit'];
            $orderBy = $listParameters['orderBy'];
            $sortOrder = $listParameters['sortOrder'];

            /**
             * 2 hooks are triggered here:
             * - actionAdminProductsListingFieldsModifier
             * - actionAdminProductsListingResultsModifier.
             */
            $products = $productProvider->getCatalogProductList($offset, $limit, $orderBy, $sortOrder);
            $lastSql = $productProvider->getLastCompiledSql();
        }

        $hasCategoryFilter = $productProvider->isCategoryFiltered();

        // Adds controller info (URLs, etc...) to product list
        foreach ($products as &$product) {
            $totalCount = isset($product['total']) ? $product['total'] : $totalCount;
            $product['url'] = $this->generateUrl(
                'admin_product_form',
                ['id' => $product['id_product']]
            );
            $product['unit_action_url'] = $this->generateUrl(
                'admin_product_unit_action',
                [
                    'action' => 'duplicate',
                    'id' => $product['id_product'],
                ]
            );
            $product['preview_url'] = $adminProductWrapper->getPreviewUrlFromId($product['id_product']);
        }

        //Drag and drop is ONLY activated when EXPLICITLY requested by the user
        //Meaning a category is selected and the user clicks on REORDER button
        $activateDragAndDrop = 'position_ordering' === $orderBy && $hasCategoryFilter;

        // Template vars injection
        $vars = array(
            'activate_drag_and_drop' => $activateDragAndDrop,
            'products' => $products,
            'product_count' => $totalCount,
            'last_sql_query' => $lastSql,
            'has_category_filter' => $productProvider->isCategoryFiltered(),
            'is_shop_context' => $this->get('prestashop.adapter.shop.context')->isShopContext(),
        );
        if ($view !== 'full') {
            return $this->render(
                '@Product/CatalogPage/Lists/list_' . $view . '.html.twig',
                array_merge(
                    $vars,
                    [
                        'limit' => $limit,
                        'offset' => $offset,
                        'total' => $totalCount,
                    ]
                )
            );
        }

        return $vars;
    }

    /**
     * Gets the header toolbar buttons.
     *
     * @return array
     */
    private function getToolbarButtons()
    {
        $toolbarButtons = array();
        $toolbarButtons['add'] = array(
            'href' => $this->generateUrl('admin_product_new'),
            'desc' => $this->trans('New product', 'Admin.Actions'),
            'icon' => 'add_circle_outline',
            'help' => $this->trans('Create a new product: CTRL+P', 'Admin.Catalog.Help'),
        );

        return $toolbarButtons;
    }

    /**
     * Create a new basic product
     * Then return to form action.
     *
     * @return RedirectResponse
     *
     * @throws \LogicException
     * @throws \PrestaShopException
     */
    public function newAction()
    {
        if (!$this->isGranted(PageVoter::CREATE, self::PRODUCT_OBJECT)) {
            $errorMessage = $this->trans('You do not have permission to add this.', 'Admin.Notifications.Error');
            $this->get('session')->getFlashBag()->add('permission_error', $errorMessage);

            return $this->redirectToRoute('admin_product_catalog');
        }

        $productProvider = $this->get('prestashop.core.admin.data_provider.product_interface');
        $languages = $this->get('prestashop.adapter.legacy.context')->getLanguages();

        /** @var $productProvider ProductInterfaceProvider */
        $productAdapter = $this->get('prestashop.adapter.data_provider.product');
        $productShopCategory = $this->getContext()->shop->id_category;

        /** @var Product $product */
        $product = $productAdapter->getProductInstance();
        $product->id_category_default = $productShopCategory;

        /** @var TaxRuleDataProvider $taxRuleDataProvider */
        $taxRuleDataProvider = $this->get('prestashop.adapter.data_provider.tax');
        $product->id_tax_rules_group = $taxRuleDataProvider->getIdTaxRulesGroupMostUsed();
        $product->active = $productProvider->isNewProductDefaultActivated() ? 1 : 0;
        $product->state = Product::STATE_TEMP;

        //set name and link_rewrite in each lang
        foreach ($languages as $lang) {
            $product->name[$lang['id_lang']] = '';
            $product->link_rewrite[$lang['id_lang']] = '';
        }

        $product->save();
        $product->addToCategories([$productShopCategory]);

        return $this->redirectToRoute('admin_product_form', ['id' => $product->id]);
    }

    /**
     * Product form.
     *
     * @Template("@PrestaShop/Admin/Product/ProductPage/product.html.twig")
     *
     * @param int $id The product ID
     * @param Request $request
     *
     * @return array|Response Template vars
     *
     * @throws \LogicException
     */
    public function formAction($id, Request $request)
    {
        gc_disable();

        if (!$this->isGranted(array(PageVoter::READ, PageVoter::UPDATE, PageVoter::CREATE), self::PRODUCT_OBJECT)) {
            return $this->redirect('admin_dashboard');
        }

        $productAdapter = $this->get('prestashop.adapter.data_provider.product');
        $product = $productAdapter->getProduct($id);

        if (!$product || empty($product->id)) {
            return $this->redirectToRoute('admin_product_catalog');
        }

        $shopContext = $this->get('prestashop.adapter.shop.context');
        $legacyContextService = $this->get('prestashop.adapter.legacy.context');
        $isMultiShopContext = count($shopContext->getContextListShopID()) > 1;

        $modelMapper = $this->get('prestashop.adapter.admin.model.product');
        $adminProductWrapper = $this->get('prestashop.adapter.admin.wrapper.product');

        $form = $this->createProductForm($product, $modelMapper);

        $formBulkCombinations = $this->createForm(
            ProductCombinationBulk::class,
            null,
            [
                'iso_code' => $this
                    ->get('prestashop.adapter.legacy.context')
                    ->getContext()->currency->iso_code,
            ]
        );

        // Legacy code. To fix when Object model will change. But report Hooks.
        $postData = $request->request->all();
        $combinationsList = [];
        if (!empty($postData)) {
            foreach ($postData as $postKey => $postValue) {
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

        /* @var Form $form */
        $form->handleRequest($request);
        $formData = $form->getData();
        $formData['step3']['combinations'] = $combinationsList;

        try {
            if ($form->isSubmitted()) {
                if ($this->isDemoModeEnabled() && $request->isXmlHttpRequest()) {
                    $errorMessage = $this->getDemoModeErrorMessage();

                    return $this->returnErrorJsonResponse(
                        ['error' => [$errorMessage]],
                        Response::HTTP_SERVICE_UNAVAILABLE
                    );
                }

                if ($form->isValid()) {
                    //define POST values for keeping legacy adminController skills
                    $_POST = $modelMapper->getModelData($formData, $isMultiShopContext) + $_POST;
                    $_POST['form'] = $formData;
                    $_POST['state'] = Product::STATE_SAVED;

                    $adminProductController = $adminProductWrapper->getInstance();
                    $adminProductController->setIdObject($formData['id_product']);
                    $adminProductController->setAction('save');

                    // Hooks: this will trigger legacy AdminProductController, postProcess():
                    // actionAdminSaveBefore; actionAdminProductsControllerSaveBefore
                    // actionProductAdd or actionProductUpdate (from processSave() -> processAdd() or processUpdate())
                    // actionAdminSaveAfter; actionAdminProductsControllerSaveAfter
                    $productSaveResult = $adminProductController->postCoreProcess();

                    if (false == $productSaveResult) {
                        return $this->returnErrorJsonResponse(
                            ['error' => $adminProductController->errors],
                            Response::HTTP_BAD_REQUEST
                        );
                    }

                    $product = $productSaveResult;

                    /* @var Product $product */
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

                    // If there is no combination, then quantity and location are managed for the whole product (as combination ID 0)
                    // In all cases, legacy hooks are triggered: actionProductUpdate and actionUpdateQuantity
                    if (count($_POST['combinations']) === 0 && isset($_POST['qty_0'])) {
                        $adminProductWrapper->processQuantityUpdate($product, $_POST['qty_0']);
                        $adminProductWrapper->processLocation($product, (string) $_POST['location']);
                    }
                    // else quantities are managed from $adminProductWrapper->processProductAttribute() above.

                    $adminProductWrapper->processProductOutOfStock($product, $_POST['out_of_stock']);

                    $customizationFieldsIds = $adminProductWrapper
                        ->processProductCustomization($product, $_POST['custom_fields']);

                    $adminProductWrapper->processAttachments($product, $_POST['attachments']);

                    $adminProductController->processWarehouses();

                    $response = new JsonResponse();
                    $response->setData([
                        'product' => $product,
                        'customization_fields_ids' => $customizationFieldsIds,
                    ]);

                    if ($request->isXmlHttpRequest()) {
                        return $response;
                    }
                } elseif ($request->isXmlHttpRequest()) {
                    return $this->returnErrorJsonResponse(
                        $this->getFormErrorsForJS($form),
                        Response::HTTP_BAD_REQUEST
                    );
                }
            }
        } catch (Exception $e) {
            // this controller can be called as an AJAX JSON route or a HTML page
            // so we need to return the right type of response if an exception it thrown
            if ($request->isXmlHttpRequest()) {
                return $this->returnErrorJsonResponse(
                    [],
                    Response::HTTP_INTERNAL_SERVER_ERROR
                );
            }

            throw $e;
        }

        /** @var $stockManager StockInterface */
        $stockManager = $this->get('prestashop.core.data_provider.stock_interface');

        /** @var WarehouseDataProvider $warehouseProvider */
        $warehouseProvider = $this->get('prestashop.adapter.data_provider.warehouse');

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

        $doctrine = $this->getDoctrine()->getManager();
        $language = empty($languages[0]) ? ['id_lang' => 1, 'id_shop' => 1] : $languages[0];
        $attributeGroups = $doctrine->getRepository('PrestaShopBundle:Attribute')->findByLangAndShop((int) $language['id_lang'], (int) $language['id_shop']);

        $drawerModules = (new HookFinder())->setHookName('displayProductPageDrawer')
            ->setParams(['product' => $product])
            ->addExpectedInstanceClasses('PrestaShop\PrestaShop\Core\Product\ProductAdminDrawer')
            ->present();

        return [
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
            'seo_link' => $adminProductWrapper->getPreviewUrl($product, false),
            'preview_link' => $preview_url,
            'preview_link_deactivate' => $preview_url_deactive,
            'stats_link' => $this->getAdminLink('AdminStats', ['module' => 'statsproduct', 'id_product' => $id]),
            'help_link' => $this->generateSidebarLink('AdminProducts'),
            'languages' => $languages,
            'default_language_iso' => $languages[0]['iso_code'],
            'attribute_groups' => $attributeGroups,
            'max_upload_size' => Tools::formatBytes(UploadedFile::getMaxFilesize()),
            'is_shop_context' => $this->get('prestashop.adapter.shop.context')->isShopContext(),
            'editable' => $this->isGranted(PageVoter::UPDATE, self::PRODUCT_OBJECT),
            'drawerModules' => $drawerModules,
            'layoutTitle' => $this->trans('Product', 'Admin.Global'),
        ];
    }

    /**
     * Builds the product form.
     *
     * @param Product $product
     * @param AdminModelAdapter $modelMapper
     *
     * @return FormInterface
     *
     * @throws \Symfony\Component\Process\Exception\LogicException
     */
    private function createProductForm(Product $product, AdminModelAdapter $modelMapper)
    {
        $formBuilder = $this->createFormBuilder(
            $modelMapper->getFormData($product),
            ['allow_extra_fields' => true]
        )
            ->add('id_product', HiddenType::class)
            ->add('step1', ProductInformation::class)
            ->add('step2', ProductPrice::class, ['id_product' => $product->id])
            ->add('step3', ProductQuantity::class)
            ->add('step4', ProductShipping::class)
            ->add('step5', ProductSeo::class, [
                'mapping_type' => $product->getRedirectType(),
            ])
            ->add('step6', ProductOptions::class);

        // Prepare combination form (fake but just to validate the form)
        $combinations = $product->getAttributesResume(
            $this->getContext()->language->id
        );

        if (is_array($combinations)) {
            $maxInputVars = (int) ini_get('max_input_vars');
            $combinationsCount = count($combinations) * 25;
            $combinationsInputs = ceil($combinationsCount / 1000) * 1000;

            if ($combinationsInputs > $maxInputVars) {
                $this->addFlash(
                    'error',
                    $this->trans(
                        'The value of the PHP.ini setting "max_input_vars" must be increased to %value% in order to be able to submit the product form.',
                        'Admin.Notifications.Error',
                        array('%value%' => $combinationsInputs)
                    )
                );
            }

            foreach ($combinations as $combination) {
                $formBuilder->add(
                    'combination_' . $combination['id_product_attribute'],
                    ProductCombination::class
                );
            }
        }

        return $formBuilder->getForm();
    }

    /**
     * Do bulk action on a list of Products. Used with the 'selection action' dropdown menu on the Catalog page.
     *
     * @param Request $request
     * @param string $action The action to apply on the selected products
     *
     * @throws Exception if action not properly set or unknown
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function bulkAction(Request $request, $action)
    {
        if (!$this->actionIsAllowed($action, self::PRODUCT_OBJECT, '_all')) {
            $this->addFlash('permission_error', $this->getForbiddenActionMessage($action));

            return $this->redirectToRoute('admin_product_catalog');
        }

        $productIdList = $request->request->get('bulk_action_selected_products');
        /** @var $productUpdater ProductInterfaceUpdater */
        $productUpdater = $this->get('prestashop.core.admin.data_updater.product_interface');

        /** @var $logger LoggerInterface */
        $logger = $this->get('logger');

        $hookEventParameters = ['product_list_id' => $productIdList];
        /** @var HookDispatcher $hookDispatcher */
        $hookDispatcher = $this->get('prestashop.core.hook.dispatcher');

        try {
            $hasMessages = $this->get('session')->getFlashBag()->has('success');

            if ($this->isDemoModeEnabled()) {
                throw new UpdateProductException($this->getDemoModeErrorMessage());
            }

            switch ($action) {
                case 'activate_all':
                    $hookDispatcher->dispatchWithParameters(
                        'actionAdminActivateBefore',
                        $hookEventParameters
                    );
                    $hookDispatcher->dispatchWithParameters(
                        'actionAdminProductsControllerActivateBefore',
                        $hookEventParameters
                    );
                    // Hooks: managed in ProductUpdater
                    $productUpdater->activateProductIdList($productIdList);
                    if (empty($hasMessages)) {
                        $this->addFlash(
                            'success',
                            $this->trans('Product(s) successfully activated.', 'Admin.Catalog.Notification')
                        );
                    }

                    $logger->info('Products activated: (' . implode(',', $productIdList) . ').');
                    $hookDispatcher->dispatchWithParameters(
                        'actionAdminActivateAfter',
                        $hookEventParameters
                    );
                    $hookDispatcher->dispatchWithParameters(
                        'actionAdminProductsControllerActivateAfter',
                        $hookEventParameters
                    );

                    break;
                case 'deactivate_all':
                    $hookDispatcher->dispatchWithParameters(
                        'actionAdminDeactivateBefore',
                        $hookEventParameters
                    );
                    $hookDispatcher->dispatchWithParameters(
                        'actionAdminProductsControllerDeactivateBefore',
                        $hookEventParameters
                    );
                    // Hooks: managed in ProductUpdater
                    $productUpdater->activateProductIdList($productIdList, false);
                    if (empty($hasMessages)) {
                        $this->addFlash(
                            'success',
                            $this->trans('Product(s) successfully deactivated.', 'Admin.Catalog.Notification')
                        );
                    }

                    $logger->info('Products deactivated: (' . implode(',', $productIdList) . ').');
                    $hookDispatcher->dispatchWithParameters(
                        'actionAdminDeactivateAfter',
                        $hookEventParameters
                    );
                    $hookDispatcher->dispatchWithParameters(
                        'actionAdminProductsControllerDeactivateAfter',
                        $hookEventParameters
                    );

                    break;
                case 'delete_all':
                    $hookDispatcher->dispatchWithParameters(
                        'actionAdminDeleteBefore',
                        $hookEventParameters
                    );
                    $hookDispatcher->dispatchWithParameters(
                        'actionAdminProductsControllerDeleteBefore',
                        $hookEventParameters
                    );
                    // Hooks: managed in ProductUpdater
                    $productUpdater->deleteProductIdList($productIdList);
                    if (empty($hasMessages)) {
                        $this->addFlash(
                            'success',
                            $this->trans('Product(s) successfully deleted.', 'Admin.Catalog.Notification')
                        );
                    }

                    $logger->info('Products deleted: (' . implode(',', $productIdList) . ').');
                    $hookDispatcher->dispatchWithParameters(
                        'actionAdminDeleteAfter',
                        $hookEventParameters
                    );
                    $hookDispatcher->dispatchWithParameters(
                        'actionAdminProductsControllerDeleteAfter',
                        $hookEventParameters
                    );

                    break;
                case 'duplicate_all':
                    $hookDispatcher->dispatchWithParameters(
                        'actionAdminDuplicateBefore',
                        $hookEventParameters
                    );
                    $hookDispatcher->dispatchWithParameters(
                        'actionAdminProductsControllerDuplicateBefore',
                        $hookEventParameters
                    );
                    // Hooks: managed in ProductUpdater
                    $productUpdater->duplicateProductIdList($productIdList);
                    if (empty($hasMessages)) {
                        $this->addFlash(
                            'success',
                            $this->trans('Product(s) successfully duplicated.', 'Admin.Catalog.Notification')
                        );
                    }

                    $logger->info('Products duplicated: (' . implode(',', $productIdList) . ').');
                    $hookDispatcher->dispatchWithParameters(
                        'actionAdminDuplicateAfter',
                        $hookEventParameters
                    );
                    $hookDispatcher->dispatchWithParameters(
                        'actionAdminProductsControllerDuplicateAfter',
                        $hookEventParameters
                    );

                    break;
                default:
                    /*
                     * should never happens since the route parameters are
                     * restricted to a set of action values in YML file.
                     */
                    $logger->error('Bulk action from ProductController received a bad parameter.');

                    throw new Exception(
                        'Bad action received from call to ProductController::bulkAction: "' . $action . '"',
                        2001
                    );
            }
        } catch (UpdateProductException $due) {
            //TODO : need to translate this with an domain name
            $message = $due->getMessage();
            $this->addFlash('failure', $message);
            $logger->warning($message);
        }

        return new Response(json_encode(['result' => 'ok']));
    }

    /**
     * Do mass edit action on the current page of products.
     * Used with the 'grouped action' dropdown menu on the Catalog page.
     *
     * @param Request $request
     * @param string $action The action to apply on the selected products
     *
     * @throws Exception if action not properly set or unknown
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function massEditAction(Request $request, $action)
    {
        if (!$this->isGranted(PageVoter::UPDATE, self::PRODUCT_OBJECT)) {
            $errorMessage = $this->trans(
                'You do not have permission to edit this.',
                'Admin.Notifications.Error'
            );
            $this->get('session')->getFlashBag()->add('permission_error', $errorMessage);

            return $this->redirectToRoute('admin_product_catalog');
        }

        /** @var $productProvider ProductInterfaceProvider */
        $productProvider = $this->get('prestashop.core.admin.data_provider.product_interface');

        /** @var $productUpdater ProductInterfaceUpdater */
        $productUpdater = $this->get('prestashop.core.admin.data_updater.product_interface');

        /** @var $logger LoggerInterface */
        $logger = $this->get('logger');

        /* @var HookDispatcher $hookDispatcher */
        $hookDispatcher = $this->get('prestashop.core.hook.dispatcher');

        /* Initialize router params variable. */
        $routerParams = [];

        try {
            switch ($action) {
                case 'sort':
                    /* Change position_ordering to position */
                    $routerParams['orderBy'] = 'position';

                    $productIdList = $request->request->get('mass_edit_action_sorted_products');
                    $productPositionList = $request->request->get('mass_edit_action_sorted_positions');
                    $hookEventParameters = [
                        'product_list_id' => $productIdList,
                        'product_list_position' => $productPositionList,
                    ];

                    $hookDispatcher->dispatchWithParameters(
                        'actionAdminSortBefore',
                        $hookEventParameters
                    );
                    $hookDispatcher->dispatchWithParameters(
                        'actionAdminProductsControllerSortBefore',
                        $hookEventParameters
                    );

                    // Hooks: managed in ProductUpdater
                    $persistedFilterParams = $productProvider->getPersistedFilterParameters();
                    $productList = array_combine($productIdList, $productPositionList);
                    $productUpdater->sortProductIdList(
                        $productList,
                        ['filter_category' => $persistedFilterParams['filter_category']]
                    );

                    $this->addFlash(
                        'success',
                        $this->trans('Products successfully sorted.', 'Admin.Catalog.Notification')
                    );
                    $logger->info(
                        'Products sorted: (' . implode(',', $productIdList) .
                        ') with positions (' . implode(',', $productPositionList) . ').'
                    );
                    $hookEventParameters = [
                        'product_list_id' => $productIdList,
                        'product_list_position' => $productPositionList,
                    ];
                    $hookDispatcher->dispatchWithParameters(
                        'actionAdminSortAfter',
                        $hookEventParameters
                    );
                    $hookDispatcher->dispatchWithParameters(
                        'actionAdminProductsControllerSortAfter',
                        $hookEventParameters
                    );

                    break;
                default:
                    /*
                     * should never happens since the route parameters are
                     * restricted to a set of action values in YML file.
                     */
                    $logger->error('Mass edit action from ProductController received a bad parameter.');

                    throw new Exception(
                        'Bad action received from call to ProductController::massEditAction: "' . $action . '"',
                        2001
                    );
            }
        } catch (UpdateProductException $due) {
            //TODO : need to translate with domain name
            $message = $due->getMessage();
            $this->addFlash('failure', $message);
            $logger->warning($message);
        }

        $urlGenerator = $this->get('prestashop.core.admin.url_generator');

        return $this->redirect($urlGenerator->generate('admin_product_catalog', $routerParams));
    }

    /**
     * Do action on one product at a time. Can be used at many places in the controller's page.
     *
     * @param string $action The action to apply on the selected product
     * @param int $id the product ID to apply the action on
     *
     * @throws Exception if action not properly set or unknown
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function unitAction($action, $id)
    {
        if (!$this->actionIsAllowed($action, self::PRODUCT_OBJECT)) {
            $this->addFlash('permission_error', $this->getForbiddenActionMessage($action));

            return $this->redirectToRoute('admin_product_catalog');
        }

        $productUpdater = $this->get('prestashop.core.admin.data_updater.product_interface');
        /** @var $productUpdater ProductInterfaceUpdater */

        /** @var $logger LoggerInterface */
        $logger = $this->get('logger');

        $hookEventParameters = ['product_id' => $id];
        /** @var HookDispatcher $hookDispatcher */
        $hookDispatcher = $this->get('prestashop.core.hook.dispatcher');

        try {
            if ($this->isDemoModeEnabled()) {
                throw new UpdateProductException($this->getDemoModeErrorMessage());
            }

            switch ($action) {
                case 'delete':
                    $hookDispatcher->dispatchWithParameters(
                        'actionAdminDeleteBefore',
                        $hookEventParameters
                    );
                    $hookDispatcher->dispatchWithParameters(
                        'actionAdminProductsControllerDeleteBefore',
                        $hookEventParameters
                    );
                    // Hooks: managed in ProductUpdater
                    $productUpdater->deleteProduct($id);
                    $this->addFlash(
                        'success',
                        $this->trans('Product successfully deleted.', 'Admin.Catalog.Notification')
                    );
                    $logger->info('Product deleted: (' . $id . ').');
                    $hookDispatcher->dispatchWithParameters(
                        'actionAdminDeleteAfter',
                        $hookEventParameters
                    );
                    $hookDispatcher->dispatchWithParameters(
                        'actionAdminProductsControllerDeleteAfter',
                        $hookEventParameters
                    );

                    break;
                case 'duplicate':
                    $hookDispatcher->dispatchWithParameters(
                        'actionAdminDuplicateBefore',
                        $hookEventParameters
                    );
                    $hookDispatcher->dispatchWithParameters(
                        'actionAdminProductsControllerDuplicateBefore',
                        $hookEventParameters
                    );
                    // Hooks: managed in ProductUpdater
                    $duplicateProductId = $productUpdater->duplicateProduct($id);
                    $this->addFlash(
                        'success',
                        $this->trans('Product successfully duplicated.', 'Admin.Catalog.Notification')
                    );
                    $logger->info('Product duplicated: (from ' . $id . ' to ' . $duplicateProductId . ').');
                    $hookDispatcher->dispatchWithParameters(
                        'actionAdminDuplicateAfter',
                        $hookEventParameters
                    );
                    $hookDispatcher->dispatchWithParameters(
                        'actionAdminProductsControllerDuplicateAfter',
                        $hookEventParameters
                    );
                    // stops here and redirect to the new product's page.
                    return $this->redirectToRoute('admin_product_form', ['id' => $duplicateProductId]);
                case 'activate':
                    $hookDispatcher->dispatchWithParameters(
                        'actionAdminActivateBefore',
                        $hookEventParameters
                    );
                    $hookDispatcher->dispatchWithParameters(
                        'actionAdminProductsControllerActivateBefore',
                        $hookEventParameters
                    );
                    // Hooks: managed in ProductUpdater
                    $productUpdater->activateProductIdList([$id]);
                    $this->addFlash(
                        'success',
                        $this->trans('Product successfully activated.', 'Admin.Catalog.Notification')
                    );
                    $logger->info('Product activated: ' . $id);
                    $hookDispatcher->dispatchWithParameters(
                        'actionAdminActivateAfter',
                        $hookEventParameters
                    );
                    $hookDispatcher->dispatchWithParameters(
                        'actionAdminProductsControllerActivateAfter',
                        $hookEventParameters
                    );

                    break;
                case 'deactivate':
                    $hookDispatcher->dispatchWithParameters(
                        'actionAdminDeactivateBefore',
                        $hookEventParameters
                    );
                    $hookDispatcher->dispatchWithParameters(
                        'actionAdminProductsControllerDeactivateBefore',
                        $hookEventParameters
                    );
                    // Hooks: managed in ProductUpdater
                    $productUpdater->activateProductIdList([$id], false);
                    $this->addFlash(
                        'success',
                        $this->trans('Product successfully deactivated.', 'Admin.Catalog.Notification')
                    );
                    $logger->info('Product deactivated: ' . $id);
                    $hookDispatcher->dispatchWithParameters(
                        'actionAdminDeactivateAfter',
                        $hookEventParameters
                    );
                    $hookDispatcher->dispatchWithParameters(
                        'actionAdminProductsControllerDeactivateAfter',
                        $hookEventParameters
                    );

                    break;
                default:
                    /*
                     * should never happens since the route parameters are
                     * restricted to a set of action values in YML file.
                     */
                    $logger->error('Unit action from ProductController received a bad parameter.');

                    throw new Exception(
                        'Bad action received from call to ProductController::unitAction: "' . $action . '"',
                        2002
                    );
            }
        } catch (UpdateProductException $due) {
            //TODO : need to translate with a domain name
            $message = $due->getMessage();
            $this->addFlash('failure', $message);
            $logger->warning($message);
        }

        return $this->redirect($this->get('prestashop.core.admin.url_generator')->generate('admin_product_catalog'));
    }

    /**
     * @return CsvResponse
     *
     * @throws \Symfony\Component\Translation\Exception\InvalidArgumentException
     */
    public function exportAction()
    {
        return $this->get('prestashop.core.product.csv_exporter')->export();
    }

    /**
     * Set the Catalog filters values and redirect to the catalogAction.
     *
     * URL example: /product/catalog_filters/42/last/32
     *
     * @param int|string $quantity the quantity to set on the catalog filters persistence
     * @param string $active the activation state to set on the catalog filters persistence
     *
     * @return RedirectResponse
     */
    public function catalogFiltersAction($quantity = 'none', $active = 'none')
    {
        $quantity = urldecode($quantity);

        /** @var ProductInterfaceProvider $productProvider */
        $productProvider = $this->get('prestashop.core.admin.data_provider.product_interface');

        // we merge empty filter set with given values, to reset the other filters!
        $productProvider->persistFilterParameters(
            array_merge(
                AdminFilter::getProductCatalogEmptyFilter(),
                [
                    'filter_column_sav_quantity' => ($quantity == 'none') ? '' : $quantity,
                    'filter_column_active' => ($active == 'none') ? '' : $active,
                ]
            )
        );

        return $this->redirectToRoute('admin_product_catalog');
    }

    /**
     * @deprecated since 1.7.5.0, to be removed in 1.8 rely on CommonController::renderFieldAction
     *
     * @throws \OutOfBoundsException
     * @throws \LogicException
     * @throws \PrestaShopException
     */
    public function renderFieldAction($productId, $step, $fieldName)
    {
        @trigger_error(
            'This function is deprecated, use CommonController::renderFieldAction instead.',
            E_USER_DEPRECATED
        );

        $productAdapter = $this->get('prestashop.adapter.data_provider.product');
        $product = $productAdapter->getProduct($productId);
        $modelMapper = new AdminModelAdapter(
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
            $this->get('prestashop.adapter.data_provider.tax'),
            $this->get('router')
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

        return $this->render('@PrestaShop/Admin/Common/_partials/_form_field.html.twig', [
            'form' => $form->getForm()->get($step)->get($fieldName)->createView(),
            'formId' => $step . '_' . $fieldName . '_rendered',
        ]);
    }
}
