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

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use PrestaShopBundle\TransitionalBehavior\AdminPagePreferenceInterface;
use PrestaShopBundle\Service\DataProvider\Admin\ProductInterface as ProductInterfaceProvider;
use PrestaShopBundle\Service\DataUpdater\Admin\ProductInterface as ProductInterfaceUpdater;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use PrestaShopBundle\Form\Admin\Product as ProductForms;
use PrestaShopBundle\Exception\DataUpdateException;
use PrestaShopBundle\Model\Product\AdminModelAdapter as ProductAdminModelAdapter;
use Symfony\Component\HttpFoundation\JsonResponse;
use PrestaShopBundle\Form\Admin\Type\ChoiceCategoriesTreeType;

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

        $productProvider = $this->container->get('prestashop.core.admin.data_provider.product_interface');
        /* @var $productProvider ProductInterfaceProvider */
        $translator = $this->container->get('prestashop.adapter.translator');
        /* @var $translator TranslatorInterface */

        // get old values from persistence (before the current update)
        $persistedFilterParameters = $productProvider->getPersistedFilterParameters();
        // override the old values with the new ones.
        $persistedFilterParameters = array_replace($persistedFilterParameters, $request->request->all());

        // URLs injection
        $actionRedirectionUrl = $this->generateUrl('admin_product_catalog', array(
            'limit' => $request->attributes->get('limit'),
            'offset' => $request->attributes->get('offset'),
            'orderBy' => $request->attributes->get('orderBy'),
            'sortOrder' => $request->attributes->get('sortOrder')
        ));
        $urls = array(
            'post_url' => $this->generateUrl('admin_product_catalog', array(
                'limit' => $request->attributes->get('limit'),
                // No offset: filter & bulk action will post form and want to redirect to first page.
                //'offset' => $request->attributes->get('offset'),
                'orderBy' => $request->attributes->get('orderBy'),
                'sortOrder' => $request->attributes->get('sortOrder')
            )),
            'ordering_url' => $this->generateUrl('admin_product_catalog', array(
                'limit' => $request->attributes->get('limit'),
                // No offset: re-ordering action will use this url to redirect to first page.
                //'offset' => $request->attributes->get('offset'),
                'orderBy' => 'name', // will be replaced by JS. Must be non default value (see routes YML file)
                'sortOrder' => 'desc' // will be replaced by JS. Must be non default value (see routes YML file)
            )),
            'bulk_url' => $this->generateUrl('admin_product_bulk_action', array(
                'action' => 'activate_all' // will be replaced by JS. Must be non default value (see routes YML file)
            )),
            'mass_edit_url' => $this->generateUrl('admin_product_mass_edit_action', array(
                'action' => 'sort' // will be replaced by JS. Must be non default value (see routes YML file)
            )),
            'bulk_redirect_url' => $actionRedirectionUrl,
            'unit_redirect_url' => $actionRedirectionUrl,
            'bulk_redirect_url_next_page' => $this->generateUrl('admin_product_catalog', array(
                'limit' => $request->attributes->get('limit'),
                'offset' => $request->attributes->get('offset') + $request->attributes->get('limit'),
                'orderBy' => $request->attributes->get('orderBy'),
                'sortOrder' => $request->attributes->get('sortOrder')
            )),
        );

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
            // Paginator
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
            $urls,
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

        $form = $this->createFormBuilder($modelMapper->getFormDatas())
            ->add('id_product', 'hidden')
            ->add('step1', new ProductForms\ProductInformation($this->container))
            ->add('step2', new ProductForms\ProductPrice($this->container))
            ->add('step3', new ProductForms\ProductQuantity())
            ->add('step4', new ProductForms\ProductShipping())
            ->add('step5', new ProductForms\ProductSeo($this->container))
            ->add('step6', new ProductForms\ProductOptions($this->container))
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                //define POST values for keeping legacy adminController skills
                $_POST = $modelMapper->getModelDatas($form->getData());

                $adminProductController = $this->container->get('prestashop.adapter.admin.wrapper.product')->getInstance();
                $adminProductController->setIdObject($form->getData()['id_product']);
                $adminProductController->setAction('save');

                if ($product = $adminProductController->postCoreProcess()) {
                    $adminProductController->processSuppliers($product->id);
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

        return array(
            'form' => $form->createView(),
        );
    }

    /**
     * Do bulk action on a list of Products. Used with the 'selection action' dropdown menu on the Catalog page.
     *
     * @param Request $request
     * @param string $action The action to apply on the selected products
     * @throws \Exception If action not properly set or unknown.
     * @return redirection
     */
    public function bulkAction(Request $request, $action)
    {
        $productIdList = $request->request->get('bulk_action_selected_products');
        $productUpdater = $this->container->get('prestashop.core.admin.data_updater.product_interface');
        /* @var $productUpdater ProductInterfaceUpdater */
        $translator = $this->container->get('prestashop.adapter.translator');
        /* @var $translator TranslatorInterface */

        try {
            switch ($action) {
                case 'activate_all':
                    $success = $productUpdater->activateProductIdList($productIdList);
                    $this->addFlash('success', $translator->trans('Product(s) successfully activated.'));
                    break;
                case 'deactivate_all':
                    $success = $productUpdater->activateProductIdList($productIdList, false);
                    $this->addFlash('success', $translator->trans('Product(s) successfully deactivated.'));
                    break;
                case 'delete_all':
                    $success = $productUpdater->deleteProductIdList($productIdList);
                    $this->addFlash('success', $translator->trans('Product(s) successfully deleted.'));
                    break;
                default:
                    // should never happens since the route parameters are restricted to a set of action values in YML file.
                    throw new \Exception('Bad action received from call to ProductController::bulkAction: "'.$action.'"', 2001);
            }
        } catch (DataUpdateException $due) {
            $this->addFlash('failure', $translator->trans($due->getMessage()));
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
     * @return redirection
     */
    public function massEditAction(Request $request, $action)
    {
        $productProvider = $this->container->get('prestashop.core.admin.data_provider.product_interface');
        /* @var $productProvider ProductInterfaceProvider */
        $productUpdater = $this->container->get('prestashop.core.admin.data_updater.product_interface');
        /* @var $productUpdater ProductInterfaceUpdater */
        $translator = $this->container->get('prestashop.adapter.translator');
        /* @var $translator TranslatorInterface */

        try {
            switch ($action) {
                case 'sort':
                    $productIdList = $request->request->get('mass_edit_action_sorted_products');
                    $productPositionList = $request->request->get('mass_edit_action_sorted_positions');
                    $success = $productUpdater->sortProductIdList(array_combine($productIdList, $productPositionList), $productProvider->getPersistedFilterParameters());
                    $this->addFlash('success', $translator->trans('Products successfully sorted.'));
                    break;
                default:
                    // should never happens since the route parameters are restricted to a set of action values in YML file.
                    throw new \Exception('Bad action received from call to ProductController::massEditAction: "'.$action.'"', 2001);
            }
        } catch (DataUpdateException $due) {
            $this->addFlash('failure', $translator->trans($due->getMessage()));
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
     * @return redirection
     */
    public function unitAction(Request $request, $action, $id)
    {
        $productUpdater = $this->container->get('prestashop.core.admin.data_updater.product_interface');
        /* @var $productUpdater ProductInterfaceUpdater */
        $translator = $this->container->get('prestashop.adapter.translator');
        /* @var $translator TranslatorInterface */

        try {
            switch ($action) {
                case 'delete':
                    $success = $productUpdater->deleteProduct($id);
                    $this->addFlash('success', $translator->trans('Product successfully deleted.'));
                    break;
                case 'duplicate':
                    $duplicateProductId = $productUpdater->duplicateProduct($id);
                    $this->addFlash('success', $translator->trans('Product successfully duplicated.'));
                    // stops here and redirect to the new product's page.
                    return $this->redirectToRoute('admin_product_form', array('id_product' => $duplicateProductId), 302);
                default:
                    // should never happens since the route parameters are restricted to a set of action values in YML file.
                    throw new \Exception('Bad action received from call to ProductController::unitAction: "'.$action.'"', 2002);
            }
        } catch (DataUpdateException $due) {
            $this->addFlash('failure', $translator->trans($due->getMessage()));
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
     * @param Request $request
     * @param boolean $use True to use legacy version. False for refactored page.
     * @return redirection
     */
    public function shouldUseLegacyPagesAction(Request $request, $use)
    {
        $pagePreference = $this->container->get('prestashop.core.admin.page_preference_interface');
        /* @var $pagePreference AdminPagePreferenceInterface */
        $pagePreference->setTemporaryShouldUseLegacyPage('product', $use);
        // Then redirect
        $urlGenerator = $this->container->get($use?'prestashop.core.admin.url_generator_legacy':'prestashop.core.admin.url_generator');
        /* @var $urlGenerator UrlGeneratorInterface */
        return $this->redirect($urlGenerator->generate('admin_product_catalog'), 302);
    }
}
