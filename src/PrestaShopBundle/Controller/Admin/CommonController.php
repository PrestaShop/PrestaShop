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

namespace PrestaShopBundle\Controller\Admin;

use Context;
use PrestaShop\PrestaShop\Adapter\Tools;
use PrestaShop\PrestaShop\Core\Domain\Notification\Command\UpdateEmployeeNotificationLastElementCommand;
use PrestaShop\PrestaShop\Core\Domain\Notification\Query\GetNotificationLastElements;
use PrestaShop\PrestaShop\Core\Domain\Notification\QueryResult\NotificationsResults;
use PrestaShop\PrestaShop\Core\Grid\Definition\Factory\AbstractGridDefinitionFactory;
use PrestaShop\PrestaShop\Core\Grid\Definition\Factory\FilterableGridDefinitionFactoryInterface;
use PrestaShop\PrestaShop\Core\Grid\Definition\Factory\GridDefinitionFactoryInterface;
use PrestaShop\PrestaShop\Core\Grid\Position\Exception\PositionUpdateException;
use PrestaShop\PrestaShop\Core\Grid\Position\GridPositionUpdaterInterface;
use PrestaShop\PrestaShop\Core\Grid\Position\PositionDefinitionInterface;
use PrestaShop\PrestaShop\Core\Grid\Position\PositionUpdateFactoryInterface;
use PrestaShop\PrestaShop\Core\Kpi\Row\KpiRowInterface;
use PrestaShopBundle\Security\Admin\Employee;
use PrestaShopBundle\Security\Annotation\AdminSecurity;
use PrestaShopBundle\Service\Grid\ControllerResponseBuilder;
use PrestaShopBundle\Service\Grid\ResponseBuilder;
use ReflectionClass;
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
     * - New messages.
     *
     * @return JsonResponse
     */
    public function notificationsAction()
    {
        $employeeId = Context::getContext()->employee->id;
        /** @var NotificationsResults $elements */
        $elements = $this->getQueryBus()->handle(new GetNotificationLastElements($employeeId));

        return new JsonResponse($elements->getNotificationsResultsForJS());
    }

    /**
     * Update the last time a notification type has been seen.
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function notificationsAckAction(Request $request)
    {
        $type = $request->request->get('type');
        $this->getCommandBus()->handle(new UpdateEmployeeNotificationLastElementCommand($type));

        return new JsonResponse(true);
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
     * @param Request $request
     * @param int $limit
     * @param int $offset
     * @param int $total
     * @param string $view full|quicknav To change default template used to render the content
     * @param string $prefix Indicates the params prefix (eg: ?limit=10&offset=20 -> ?scope[limit]=10&scope[offset]=20)
     *
     * @return Response
     */
    public function paginationAction(Request $request, $limit = 10, $offset = 0, $total = 0, $view = 'full', $prefix = ''): Response
    {
        $offsetParam = empty($prefix) ? 'offset' : sprintf('%s[offset]', $prefix);
        $limitParam = empty($prefix) ? 'limit' : sprintf('%s[limit]', $prefix);

        $limit = max($limit, 10);

        $currentPage = floor($offset / $limit) + 1;
        $pageCount = ceil($total / $limit);
        $from = $offset;
        $to = $offset + $limit - 1;

        // urls from route
        $callerParameters = $request->attributes->get('caller_parameters', []);
        foreach ($callerParameters as $k => $v) {
            if (str_starts_with($k, '_')) {
                unset($callerParameters[$k]);
            }
        }
        $callerParameters += ['_route' => false];
        $routeName = $request->attributes->get('caller_route', $callerParameters['_route']);
        $nextPageUrl = (!$routeName || ($offset + $limit >= $total)) ? false : $this->generateUrl($routeName, array_merge(
            $callerParameters,
            [
                $offsetParam => min($total - 1, $offset + $limit),
                $limitParam => $limit,
            ]
        ));

        $previousPageUrl = (!$routeName || ($offset == 0)) ? false : $this->generateUrl($routeName, array_merge(
            $callerParameters,
            [
                $offsetParam => max(0, $offset - $limit),
                $limitParam => $limit,
            ]
        ));
        $firstPageUrl = (!$routeName || ($offset == 0)) ? false : $this->generateUrl($routeName, array_merge(
            $callerParameters,
            [
                $offsetParam => 0,
                $limitParam => $limit,
            ]
        ));
        $lastPageUrl = (!$routeName || ($offset + $limit >= $total)) ? false : $this->generateUrl($routeName, array_merge(
            $callerParameters,
            [
                $offsetParam => ($pageCount - 1) * $limit,
                $limitParam => $limit,
            ]
        ));
        $changeLimitUrl = (!$routeName) ? false : $this->generateUrl($routeName, array_merge(
            $callerParameters,
            [
                $offsetParam => 0,
                $limitParam => '_limit',
            ]
        ));
        $jumpPageUrl = (!$routeName) ? false : $this->generateUrl($routeName, array_merge(
            $callerParameters,
            [
                $offsetParam => 999999,
                $limitParam => $limit,
            ]
        ));
        $limitChoices = $request->attributes->get('limit_choices', [10, 20, 50, 100]);

        // Template vars injection
        $vars = [
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
        ];
        if ($view != 'full') {
            return $this->render('@PrestaShop/Admin/Common/pagination_' . $view . '.html.twig', $vars);
        }

        return $this->render('@PrestaShop/Admin/Common/pagination.html.twig', $vars);
    }

    /**
     * Render a right sidebar with content from an URL.
     *
     * @param string $url
     * @param string $title
     * @param string $footer
     *
     * @return Response
     */
    public function renderSidebarAction($url, $title = '', $footer = '')
    {
        $tools = $this->get(Tools::class);

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
        $employeeId = $this->getUser() instanceof Employee ? $this->getUser()->getId() : 0;
        $shopId = $this->getContext()->shop->id;

        // for compatibility when $controller and $action are used
        if (!empty($controller) && !empty($action)) {
            $adminFilter = $adminFiltersRepository->findByEmployeeAndRouteParams(
                $employeeId, $shopId, $controller, $action
            );
        }

        if (!empty($filterId)) {
            $adminFilter = $adminFiltersRepository->findByEmployeeAndFilterId($employeeId, $shopId, $filterId);
        }

        if (isset($adminFilter)) {
            $adminFiltersRepository->unsetFilters($adminFilter);
        }

        return new JsonResponse();
    }

    /**
     * Specific action to render a specific field twice.
     *
     * @param string $formName the form name
     * @param string $formType the form type FQCN
     * @param string $fieldName the field name
     * @param array $fieldData the field data
     *
     * @return Response
     */
    public function renderFieldAction($formName, $formType, $fieldName, $fieldData)
    {
        $formData = [
            $formName => [
                $fieldName => $fieldData,
            ],
        ];

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
    #[AdminSecurity("is_granted('read', request.get('_legacy_controller'))")]
    public function searchGridAction(
        Request $request,
        $gridDefinitionFactoryServiceId,
        $redirectRoute,
        array $redirectQueryParamsToKeep = []
    ) {
        /** @var GridDefinitionFactoryInterface $definitionFactory */
        $definitionFactory = $this->get($gridDefinitionFactoryServiceId);

        $filterId = null;

        if ($definitionFactory instanceof FilterableGridDefinitionFactoryInterface) {
            $filterId = $definitionFactory->getFilterId();
        } elseif ($definitionFactory instanceof AbstractGridDefinitionFactory) {
            // for backward compatibility for AbstractGridDefinitionFactory
            // using ::GRID_ID (that has been replaced by AbstractFilterableGridDefinitionFactory)
            $reflect = new ReflectionClass($definitionFactory);
            if (array_key_exists('GRID_ID', $reflect->getConstants())) {
                /* @phpstan-ignore-next-line Check of constant is done with ReflectionClass */
                $filterId = $definitionFactory::GRID_ID;
            }
        }

        if (null !== $filterId) {
            /** @var ResponseBuilder $responseBuilder */
            $responseBuilder = $this->get('prestashop.bundle.grid.response_builder');

            return $responseBuilder->buildSearchResponse(
                $definitionFactory,
                $request,
                $filterId,
                $redirectRoute,
                $redirectQueryParamsToKeep
            );
        }

        // Legacy grid definition which use controller/action as filter keys (and no scope for parameters)
        /** @var ControllerResponseBuilder $controllerResponseBuilder */
        $controllerResponseBuilder = $this->get('prestashop.bundle.grid.controller_response_builder');

        return $controllerResponseBuilder->buildSearchResponse(
            $definitionFactory,
            $request,
            $redirectRoute,
            $redirectQueryParamsToKeep
        );
    }

    /**
     * @param Request $request
     *
     * @return RedirectResponse
     */
    #[AdminSecurity("is_granted('update', request.get('_legacy_controller'))")]
    public function updatePositionAction(Request $request): RedirectResponse
    {
        $positionsData = [
            'positions' => $request->request->all('positions'),
        ];

        /** @var PositionDefinitionInterface $positionDefinition */
        $positionDefinition = $this->get($request->attributes->get('position_definition'));
        $positionUpdateFactory = $this->get(PositionUpdateFactoryInterface::class);

        try {
            $positionUpdate = $positionUpdateFactory->buildPositionUpdate($positionsData, $positionDefinition);
            $updater = $this->get(GridPositionUpdaterInterface::class);
            $updater->update($positionUpdate);
            $this->addFlash('success', $this->trans('Successful update', 'Admin.Notifications.Success'));
        } catch (PositionUpdateException $e) {
            $errors = [$e->toArray()];
            $this->flashErrors($errors);
        }

        return $this->redirectToRoute($request->attributes->get('redirect_route'));
    }
}
