<?php
/**
 * 2007-2019 PrestaShop and Contributors
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

use PrestaShop\PrestaShop\Adapter\Module\AdminModuleDataProvider;
use PrestaShop\PrestaShop\Core\Addon\AddonsCollection;
use PrestaShop\PrestaShop\Core\Addon\Module\ModuleManagerBuilder;
use PrestaShop\PrestaShop\Core\Grid\Definition\Factory\GridDefinitionFactoryInterface;
use PrestaShop\PrestaShop\Core\Grid\Definition\GridDefinitionInterface;
use PrestaShop\PrestaShop\Core\Kpi\Row\KpiRowInterface;
use PrestaShopBundle\Service\DataProvider\Admin\RecommendedModules;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Admin controller for the common actions across the whole admin interface.
 */
class CommonController extends FrameworkBundleAdminController
{
    /**
     * Get a summary of recent events on the shop.
     * This includes:
     * - Created orders
     * - Registered customers
     * - New messages
     *
     * @return JsonResponse
     */
    public function notificationsAction()
    {
        // Adapter needed here?
        return new JsonResponse((new \Notification)->getLastElements());
    }

    /**
     * Update the last time a notification type has been seen
     * 
     * @param Request $request
     */
    public function notificationsAckAction(Request $request)
    {
        $type = $request->request->get('type');
        // Adapter needed here?
        return new JsonResponse((new \Notification)->updateEmployeeLastElement($type));
    }

    /**
     * This will allow you to retrieve an HTML code with a ready and linked paginator.
     *
     * To be able to use this paginator, the current route must have these standard parameters:
     * - offset
     * - limit
     * Both will be automatically manipulated by the paginator.
     * The navigator links (previous/next page...) will never tranfer POST and/or GET parameters
     * (only route parameters that are in the URL).
     *
     * You must add a JS file to the list of JS for view rendering: pagination.js
     *
     * The final way to render a paginator is the following:
     * {% render controller('PrestaShopBundle\\Controller\\Admin\\CommonController::paginationAction',
     *   {'limit': limit, 'offset': offset, 'total': product_count, 'caller_parameters': pagination_parameters}) %}
     *
     * @Template("@PrestaShop/Admin/Common/pagination.html.twig")
     *
     * @param Request $request
     * @param int $limit
     * @param int $offset
     * @param int $total
     * @param string $view full|quicknav To change default template used to render the content
     * @param string $prefix Indicates the params prefix (eg: ?limit=10&offset=20 -> ?scope[limit]=10&scope[offset]=20)
     *
     * @return array|Response
     */
    public function paginationAction(Request $request, $limit = 10, $offset = 0, $total = 0, $view = 'full', $prefix = '')
    {
        $offsetParam = empty($prefix) ? 'offset' : sprintf('%s[offset]', $prefix);
        $limitParam = empty($prefix) ? 'limit' : sprintf('%s[limit]', $prefix);

        $limit = max($limit, 10);

        $currentPage = floor($offset / $limit) + 1;
        $pageCount = ceil($total / $limit);
        $from = $offset;
        $to = $offset + $limit - 1;

        // urls from route
        $callerParameters = $request->attributes->get('caller_parameters', array());
        foreach ($callerParameters as $k => $v) {
            if (strpos($k, '_') === 0) {
                unset($callerParameters[$k]);
            }
        }
        $routeName = $request->attributes->get('caller_route', $request->attributes->get('caller_parameters', ['_route' => false])['_route']);
        $nextPageUrl = (!$routeName || ($offset + $limit >= $total)) ? false : $this->generateUrl($routeName, array_merge(
            $callerParameters,
            array(
                $offsetParam => min($total - 1, $offset + $limit),
                $limitParam => $limit,
            )
        ));

        $previousPageUrl = (!$routeName || ($offset == 0)) ? false : $this->generateUrl($routeName, array_merge(
            $callerParameters,
            array(
                $offsetParam => max(0, $offset - $limit),
                $limitParam => $limit,
            )
        ));
        $firstPageUrl = (!$routeName || ($offset == 0)) ? false : $this->generateUrl($routeName, array_merge(
            $callerParameters,
            array(
                $offsetParam => 0,
                $limitParam => $limit,
            )
        ));
        $lastPageUrl = (!$routeName || ($offset + $limit >= $total)) ? false : $this->generateUrl($routeName, array_merge(
            $callerParameters,
            array(
                $offsetParam => ($pageCount - 1) * $limit,
                $limitParam => $limit,
            )
        ));
        $changeLimitUrl = (!$routeName) ? false : $this->generateUrl($routeName, array_merge(
            $callerParameters,
            array(
                $offsetParam => 0,
                $limitParam => '_limit',
            )
        ));
        $jumpPageUrl = (!$routeName) ? false : $this->generateUrl($routeName, array_merge(
            $callerParameters,
            array(
                $offsetParam => 999999,
                $limitParam => $limit,
            )
        ));
        $limitChoices = $request->attributes->get('limit_choices', array(10, 20, 50, 100));

        // Template vars injection
        $vars = array(
            'limit' => $limit,
            'changeLimitUrl' => $changeLimitUrl,
            'first_url' => $firstPageUrl,
            'previous_url' => $previousPageUrl,
            'from' => $from,
            'to' => $to,
            'total' => $total,
            'current_page' => $currentPage,
            'page_count' => $pageCount,
            'next_url' => $nextPageUrl,
            'last_url' => $lastPageUrl,
            'jump_page_url' => $jumpPageUrl,
            'limit_choices' => $limitChoices,
        );
        if ($view != 'full') {
            return $this->render('@PrestaShop/Admin/Common/pagination_' . $view . '.html.twig', $vars);
        }

        return $vars;
    }

    /**
     * This will allow you to retrieve an HTML code with a list of recommended modules depending on the domain.
     *
     * @Template("@PrestaShop/Admin/Common/recommendedModules.html.twig")
     *
     * @param string $domain
     * @param int $limit
     * @param int $randomize
     *
     * @return array Template vars
     */
    public function recommendedModulesAction($domain, $limit = 0, $randomize = 0)
    {
        $recommendedModules = $this->get('prestashop.data_provider.modules.recommended');
        /** @var $recommendedModules RecommendedModules */
        $moduleIdList = $recommendedModules->getRecommendedModuleIdList($domain, ($randomize == 1));

        $modulesProvider = $this->get('prestashop.core.admin.data_provider.module_interface');
        /** @var $modulesProvider AdminModuleDataProvider */
        $modulesRepository = ModuleManagerBuilder::getInstance()->buildRepository();

        $modules = array();
        foreach ($moduleIdList as $id) {
            try {
                $module = $modulesRepository->getModule($id);
            } catch (\Exception $e) {
                continue;
            }
            $modules[] = $module;
        }

        if ($randomize == 1) {
            shuffle($modules);
        }

        $modules = $recommendedModules->filterInstalledAndBadModules($modules);
        $collection = AddonsCollection::createFrom($modules);
        $modules = $modulesProvider->generateAddonsUrls($collection);

        return array(
            'domain' => $domain,
            'modules' => array_slice($modules, 0, $limit, true),
        );
    }

    /**
     * Render a right sidebar with content from an URL.
     *
     * @param $url
     * @param string $title
     * @param string $footer
     *
     * @return Response
     */
    public function renderSidebarAction($url, $title = '', $footer = '')
    {
        $tools = $this->get('prestashop.adapter.tools');

        return $this->render('@PrestaShop/Admin/Common/_partials/_sidebar.html.twig', [
            'footer' => $tools->purifyHTML($footer),
            'title' => $title,
            'url' => urldecode($url),
        ]);
    }

    /**
     * Renders a KPI row.
     *
     * @param KpiRowInterface $kpiRow
     *
     * @return Response
     */
    public function renderKpiRowAction(KpiRowInterface $kpiRow)
    {
        $presenter = $this->get('prestashop.core.kpi_row.presenter');

        return $this->render('@PrestaShop/Admin/Common/Kpi/kpi_row.html.twig', [
            'kpiRow' => $presenter->present($kpiRow),
        ]);
    }

    /**
     * @param string $controller
     * @param string $action
     * @param string $filterId
     *
     * @return JsonResponse
     *
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function resetSearchAction($controller = '', $action = '', $filterId = '')
    {
        $adminFiltersRepository = $this->get('prestashop.core.admin.admin_filter.repository');

        // for compatibility when $controller and $action are used
        if (!empty($controller) && !empty($action)) {
            $employeeId = $this->getUser()->getId();
            $shopId = $this->getContext()->shop->id;

            $adminFiltersRepository->removeByEmployeeAndRouteParams($employeeId, $shopId, $controller, $action);
        }

        if (!empty($filterId)) {
            $adminFiltersRepository->removeByFilterId($filterId);
        }

        return new JsonResponse();
    }

    /**
     * Specific action to render a specific field twice.
     *
     * @param $formName the form name
     * @param $formType the form type FQCN
     * @param $fieldName the field name
     * @param $fieldData the field data
     *
     * @return Response
     */
    public function renderFieldAction($formName, $formType, $fieldName, $fieldData)
    {
        $formData = array(
            $formName => array(
                $fieldName => $fieldData,
            ),
        );

        $form = $this->createFormBuilder($formData);
        $form->add($formName, $formType);

        return $this->render('@PrestaShop/Admin/Common/_partials/_form_field.html.twig', [
            'form' => $form->getForm()->get($formName)->get($fieldName)->createView(),
            'formId' => $formName . '_' . $fieldName . '_rendered',
        ]);
    }

    /**
     * Process Grid search.
     *
     * @param Request $request
     * @param string $gridDefinitionFactoryServiceId
     * @param string $redirectRoute
     * @param array $redirectQueryParamsToKeep
     *
     * @return RedirectResponse
     */
    public function searchGridAction(
        Request $request,
        $gridDefinitionFactoryServiceId,
        $redirectRoute,
        array $redirectQueryParamsToKeep = []
    ) {
        /** @var GridDefinitionFactoryInterface $definitionFactory */
        $definitionFactory = $this->get($gridDefinitionFactoryServiceId);
        /** @var GridDefinitionInterface $definition */
        $definition = $definitionFactory->getDefinition();

        $gridFilterFormFactory = $this->get('prestashop.core.grid.filter.form_factory');

        $filtersForm = $gridFilterFormFactory->create($definition);
        $filtersForm->handleRequest($request);

        $redirectParams = [];
        if ($filtersForm->isSubmitted()) {
            $redirectParams = [
                'filters' => $filtersForm->getData(),
            ];
        }

        foreach ($redirectQueryParamsToKeep as $paramName) {
            if ($request->query->has($paramName)) {
                $redirectParams[$paramName] = $request->query->get($paramName);
            }
        }

        return $this->redirectToRoute($redirectRoute, $redirectParams);
    }
}
