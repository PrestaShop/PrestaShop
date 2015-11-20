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

use PrestaShopBundle\Service\DataProvider\StockInterface;
use PrestaShopBundle\Service\Hook\HookEvent;
use Symfony\Component\HttpFoundation\Request;
use PrestaShopBundle\Service\TransitionalBehavior\AdminPagePreferenceInterface;
use PrestaShopBundle\Service\DataProvider\Admin\ProductInterface as ProductInterfaceProvider;
use PrestaShopBundle\Service\DataUpdater\Admin\ProductInterface as ProductInterfaceUpdater;
use Symfony\Component\HttpKernel\Log\LoggerInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use PrestaShopBundle\Form\Admin\Product as ProductForms;
use PrestaShopBundle\Exception\DataUpdateException;
use PrestaShopBundle\Model\Product\AdminModelAdapter as ProductAdminModelAdapter;
use Symfony\Component\HttpFoundation\JsonResponse;
use PrestaShopBundle\Form\Admin\Type\ChoiceCategoriesTreeType;
use Symfony\Component\Translation\TranslatorInterface;
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

        $logger = $this->container->get('logger');
        /* @var $logger LoggerInterface */

        $productProvider = $this->container->get('prestashop.core.admin.data_provider.product_interface');
        /* @var $productProvider ProductInterfaceProvider */
        $translator = $this->container->get('prestashop.adapter.translator');
        /* @var $translator TranslatorInterface */

        // get old values from persistence (before the current update)
        $persistedFilterParameters = $productProvider->getPersistedFilterParameters();
        // override the old values with the new ones.
        $persistedFilterParameters = array_replace($persistedFilterParameters, $request->request->all());

        // Add layout top-right menu actions
        $toolbarButtons = array();
        if ($pagePreference->getTemporaryShouldAllowUseLegacyPage('product')) {
            $toolbarButtons['legacy'] = array(
                'href' => $this->generateUrl('admin_product_use_legacy_setter', array('use' => 1)),
                'desc' => $translator->trans('Switch back to old Page', array(), $request->attributes->get('_legacy_controller')),
                'icon' => 'process-icon-toggle-on',
                'help' => $translator->trans('The new page cannot fit your needs now? Fallback to the old one, and tell us why!', array(), $request->attributes->get('_legacy_controller'))
            );
        }
        $toolbarButtons['add'] = array(
            'href' => $this->generateUrl('admin_product_form'),
            'desc' => $translator->trans('Add new product', array(), $request->attributes->get('_legacy_controller')),
            'icon' => 'process-icon-new'
        );

        // Fetch product list (and cache it into view subcall to listAction)
        $products = $productProvider->getCatalogProductList($offset, $limit, $orderBy, $sortOrder, $request->request->all());
        $logger->info('Product catalog filters stored.');
        $hasCategoryFilter = $productProvider->isCategoryFiltered();
        $hasColumnFilter = $productProvider->isColumnFiltered();

        // Alternative layout for empty list
        $totalFilteredProductCount = count($products);
        $totalProductCount = 0;
        if ((!$hasCategoryFilter && !$hasColumnFilter && $totalFilteredProductCount === 0)
            || ($totalProductCount = $productProvider->countAllProducts()) === 0) {
            // no filter, total filtered == 0, and then total count == 0 too.
            return $this->render('PrestaShopBundle:Admin/Product:catalogEmpty.html.twig');
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
                'product_count_filtered' => $totalFilteredProductCount,
                'product_count' => $totalProductCount,
                'activate_drag_and_drop' => (('position_ordering' == $orderBy) || ('position' == $orderBy && 'asc' == $sortOrder && !$hasColumnFilter)),
                'pagination_parameters' => $paginationParameters,
                'layoutHeaderToolbarBtn' => $toolbarButtons,
                'categories' => $categories->createView()
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
     * @return array Template vars
     */
    public function listAction(Request $request, $limit = 10, $offset = 0, $orderBy = 'id_product', $sortOrder = 'asc')
    {
        $totalCount = 0;
        $products = $request->attributes->get('products', null); // get from action subcall data, if any
        $productProvider = $this->container->get('prestashop.core.admin.data_provider.product_interface');
        /* @var $productProvider ProductInterfaceProvider */
        if ($products === null) {
            // 2 hooks are triggered here: actionAdminProductsListingFieldsModifier and actionAdminProductsListingResultsModifier
            $products = $productProvider->getCatalogProductList($offset, $limit, $orderBy, $sortOrder);
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
        }

        // Template vars injection
        return array(
            'activate_drag_and_drop' => (('position_ordering' == $orderBy) || ('position' == $orderBy && 'asc' == $sortOrder && !$hasColumnFilter)),
            'products' => $products,
            'product_count' => $totalCount
        );
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
        // Redirect to legacy controller (FIXME: temporary behavior)
        $pagePreference = $this->container->get('prestashop.core.admin.page_preference_interface');
        /* @var $pagePreference AdminPagePreferenceInterface */
        if ($pagePreference->getTemporaryShouldUseLegacyPage('product')) {
            $legacyUrlGenerator = $this->container->get('prestashop.core.admin.url_generator_legacy');
            /* @var $legacyUrlGenerator UrlGeneratorInterface */
            return $this->redirect($legacyUrlGenerator->generate('admin_product_form', array('id' => $id)), 302);
        }

        $response = new JsonResponse();
        $modelMapper = new ProductAdminModelAdapter($id, $this->container);
        $adminProductWrapper = $this->container->get('prestashop.adapter.admin.wrapper.product');

        $form = $this->createFormBuilder($modelMapper->getFormDatas())
            ->add('id_product', 'hidden')
            ->add('step1', new ProductForms\ProductInformation($this->container))
            ->add('step2', new ProductForms\ProductPrice($this->container))
            ->add('step3', new ProductForms\ProductQuantity($this->container))
            ->add('step4', new ProductForms\ProductShipping($this->container))
            ->add('step5', new ProductForms\ProductSeo($this->container))
            ->add('step6', new ProductForms\ProductOptions($this->container))
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                // Legacy code. To fix when Object model will change. But report Hooks.

                //define POST values for keeping legacy adminController skills
                $_POST = $modelMapper->getModelDatas($form->getData());

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
                        $adminProductWrapper->processDependsOnStock($product, ($_POST['depends_on_stock'] == 1), $combinationValues['id_product_attribute']);
                    }

                    // If there is no combination, then quantity is managed for the whole product (as combination ID 0)
                    // In all cases, legacy hooks are triggered: actionProductUpdate and actionUpdateQuantity
                    if (count($_POST['combinations']) === 0) {
                        $adminProductWrapper->processDependsOnStock($product, ($_POST['depends_on_stock'] == 1));
                        $adminProductWrapper->processQuantityUpdate($product, $_POST['qty_0']);
                    }
                    // else quantities are managed from $adminProductWrapper->processProductAttribute() above.

                    $adminProductWrapper->processProductOutOfStock($product, $_POST['out_of_stock']);

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

        return array(
            'form' => $form->createView(),
            'id_product' => $id,
            'has_combinations' => (isset($form->getData()['step3']['combinations']) && count($form->getData()['step3']['combinations']) > 0),
            'asm_globally_activated' => $stockManager->isAsmGloballyActivated()
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
                    $this->addFlash('success', $translator->trans('Product(s) successfully activated.'));
                    $logger->info('Products activated: ('.implode(',', $productIdList).').');
                    $hookDispatcher->dispatchMultiple(['actionAdminActivateAfter', 'actionAdminProductsControllerActivateAfter'], $hookEventParameters);
                    break;
                case 'deactivate_all':
                    $hookDispatcher->dispatchMultiple(['actionAdminDeactivateBefore', 'actionAdminProductsControllerDeactivateBefore'], $hookEventParameters);
                    // Hooks: managed in ProductUpdater
                    $productUpdater->activateProductIdList($productIdList, false);
                    $this->addFlash('success', $translator->trans('Product(s) successfully deactivated.'));
                    $logger->info('Products deactivated: ('.implode(',', $productIdList).').');
                    $hookDispatcher->dispatchMultiple(['actionAdminDeactivateAfter', 'actionAdminProductsControllerDeactivateAfter'], $hookEventParameters);
                    break;
                case 'delete_all':
                    $hookDispatcher->dispatchMultiple(['actionAdminDeleteBefore', 'actionAdminProductsControllerDeleteBefore'], $hookEventParameters);
                    // Hooks: managed in ProductUpdater
                    $productUpdater->deleteProductIdList($productIdList);
                    $this->addFlash('success', $translator->trans('Product(s) successfully deleted.'));
                    $logger->info('Products deleted: ('.implode(',', $productIdList).').');
                    $hookDispatcher->dispatchMultiple(['actionAdminDeleteAfter', 'actionAdminProductsControllerDeleteAfter'], $hookEventParameters);
                    break;
                default:
                    // should never happens since the route parameters are restricted to a set of action values in YML file.
                    $logger->error('Bulk action from ProductController received a bad parameter.');
                    throw new \Exception('Bad action received from call to ProductController::bulkAction: "'.$action.'"', 2001);
            }
        } catch (DataUpdateException $due) {
            $message = $due->getMessage();
            $this->addFlash('failure', $translator->trans($message));
            $logger->warning($message);
        }

        // redirect after success
        return $this->redirect($request->request->get('redirect_url'), 302);
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
                    $hookDispatcher->dispatchMultiple(['actionAdminSortBefore', 'actionAdminProductsControllerSortBefore'],
                        ['product_list_id' => $productIdList, 'product_list_position' => $productPositionList]);
                    // Hooks: managed in ProductUpdater
                    $productUpdater->sortProductIdList(array_combine($productIdList, $productPositionList), $productProvider->getPersistedFilterParameters());
                    $this->addFlash('success', $translator->trans('Products successfully sorted.'));
                    $logger->info('Products sorted: ('.implode(',', $productIdList).') with positions ('.implode(',', $productPositionList).').');
                    $hookDispatcher->dispatchMultiple(['actionAdminSortAfter', 'actionAdminProductsControllerSortAfter'],
                        ['product_list_id' => $productIdList, 'product_list_position' => $productPositionList]);
                    break;
                default:
                    // should never happens since the route parameters are restricted to a set of action values in YML file.
                    $logger->error('Mass edit action from ProductController received a bad parameter.');
                    throw new \Exception('Bad action received from call to ProductController::massEditAction: "'.$action.'"', 2001);
            }
        } catch (DataUpdateException $due) {
            $message = $due->getMessage();
            $this->addFlash('failure', $translator->trans($message));
            $logger->warning($message);
        }

        // redirect after success
        return $this->redirect($request->request->get('redirect_url'), 302);
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
                    $this->addFlash('success', $translator->trans('Product successfully deleted.'));
                    $logger->info('Product deleted: ('.$id.').');
                    $hookDispatcher->dispatchMultiple(['actionAdminDeleteAfter', 'actionAdminProductsControllerDeleteAfter'], $hookEventParameters);
                    break;
                case 'duplicate':
                    $hookDispatcher->dispatchMultiple(['actionAdminDuplicateBefore', 'actionAdminProductsControllerDuplicateBefore'], $hookEventParameters);
                    // Hooks: managed in ProductUpdater
                    $duplicateProductId = $productUpdater->duplicateProduct($id);
                    $this->addFlash('success', $translator->trans('Product successfully duplicated.'));
                    $logger->info('Product duplicated: (from '.$id.' to '.$duplicateProductId.').');
                    $hookDispatcher->dispatchMultiple(['actionAdminDuplicateAfter', 'actionAdminProductsControllerDuplicateAfter'], $hookEventParameters);
                    // stops here and redirect to the new product's page.
                    return $this->redirectToRoute('admin_product_form', array('id' => $duplicateProductId), 302);
                default:
                    // should never happens since the route parameters are restricted to a set of action values in YML file.
                    $logger->error('Unit action from ProductController received a bad parameter.');
                    throw new \Exception('Bad action received from call to ProductController::unitAction: "'.$action.'"', 2002);
            }
        } catch (DataUpdateException $due) {
            $message = $due->getMessage();
            $this->addFlash('failure', $translator->trans($message));
            $logger->warning($message);
        }

        // redirect after success
        return $this->redirect($request->request->get('redirect_url'), 302);
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
