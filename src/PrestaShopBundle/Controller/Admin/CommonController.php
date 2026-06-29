<?php

/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Controller\Admin;

use PrestaShop\PrestaShop\Adapter\Tools;
use PrestaShop\PrestaShop\Core\Domain\Notification\Command\UpdateEmployeeNotificationLastElementCommand;
use PrestaShop\PrestaShop\Core\Domain\Notification\Query\GetNotificationLastElements;
use PrestaShop\PrestaShop\Core\Domain\Notification\QueryResult\NotificationsResults;
use PrestaShop\PrestaShop\Core\ExtraProperty\Definition\ExtraPropertyDefinition;
use PrestaShop\PrestaShop\Core\ExtraProperty\Definition\ExtraPropertyDefinitionRepositoryInterface;
use PrestaShop\PrestaShop\Core\ExtraProperty\Value\ExtraPropertyWriterInterface;
use PrestaShop\PrestaShop\Core\Grid\Definition\Factory\AbstractGridDefinitionFactory;
use PrestaShop\PrestaShop\Core\Grid\Definition\Factory\FilterableGridDefinitionFactoryInterface;
use PrestaShop\PrestaShop\Core\Grid\Definition\Factory\GridDefinitionFactoryProvider;
use PrestaShop\PrestaShop\Core\Grid\Position\Exception\PositionUpdateException;
use PrestaShop\PrestaShop\Core\Grid\Position\PositionDefinitionProvider;
use PrestaShop\PrestaShop\Core\Kpi\Row\KpiRowInterface;
use PrestaShop\PrestaShop\Core\Kpi\Row\KpiRowPresenter;
use PrestaShopBundle\Entity\Repository\AdminFilterRepository;
use PrestaShopBundle\Security\Attribute\AdminSecurity;
use PrestaShopBundle\Service\Grid\ControllerResponseBuilder;
use ReflectionClass;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

/**
 * Admin controller for the common actions across the whole admin interface.
 */
class CommonController extends PrestaShopAdminController
{
    public static function getSubscribedServices(): array
    {
        return parent::getSubscribedServices() + [
            ControllerResponseBuilder::class => ControllerResponseBuilder::class,
            ExtraPropertyDefinitionRepositoryInterface::class => ExtraPropertyDefinitionRepositoryInterface::class,
            ExtraPropertyWriterInterface::class => ExtraPropertyWriterInterface::class,
        ];
    }

    /**
     * Get a summary of recent events on the shop.
     * This includes:
     * - Created orders
     * - Registered customers
     * - New messages.
     *
     * @return JsonResponse
     */
    public function notificationsAction(): JsonResponse
    {
        /** @var NotificationsResults $elements */
        $elements = $this->dispatchQuery(new GetNotificationLastElements($this->getEmployeeContext()->getEmployee()->getId()));

        return new JsonResponse($elements->getNotificationsResultsForJS());
    }

    /**
     * Update the last time a notification type has been seen.
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function notificationsAckAction(Request $request): JsonResponse
    {
        $this->dispatchCommand(new UpdateEmployeeNotificationLastElementCommand($request->request->get('type')));

        return new JsonResponse(true);
    }

    /**
     * Toggle one extra property value from a grid toggle column.
     *
     * This endpoint is designed for ToggleColumn async usage in BO grids.
     * It performs an UPSERT and toggles the value in SQL without doing a preliminary SELECT.
     *
     * Security: the legacy controller name is derived server-side from the entityName URL path
     * parameter (non-forgeable), NOT from any client-supplied value. This prevents privilege
     * escalation where an authenticated admin could bypass per-entity permission checks by
     * forging a _legacy_controller value they hold rights on.
     *
     * The shop context of shop-scoped properties is resolved from ShopContext (not from the
     * route): the writer receives the current ShopConstraint and toggles the matching row.
     *
     * @param string $entityName
     * @param int $entityId
     * @param string $moduleName normalized module name, can be "_core" for core properties
     * @param string $propertyName
     */
    #[AdminSecurity("is_granted('ROLE_EMPLOYEE')")]
    public function toggleExtraPropertyAction(
        string $entityName,
        int $entityId,
        string $moduleName,
        string $propertyName,
    ): JsonResponse {
        // Derive the legacy controller from the entityName URL path param (trusted, non-forgeable).
        // Never trust a _legacy_controller value coming from the request body/query string.
        $legacyController = self::legacyControllerFromEntityName($entityName);
        if (!$this->isGranted('update', $legacyController)) {
            return new JsonResponse([
                'status' => false,
                'message' => 'Access denied.',
            ], 403);
        }

        /** @var ExtraPropertyDefinitionRepositoryInterface $repository */
        $repository = $this->container->get(ExtraPropertyDefinitionRepositoryInterface::class);

        // '_core' is the display sentinel for core properties; the DB stores null.
        $resolvedModuleName = ExtraPropertyDefinition::CORE_MODULE_KEY === $moduleName ? null : $moduleName;

        // (entity, module, property) is unique across scopes — the definition carries its own scope.
        $matched = $repository->findDefinitionByModuleAndField($entityName, $resolvedModuleName, $propertyName);
        if (null === $matched) {
            return new JsonResponse([
                'status' => false,
                'message' => $this->trans('Field not found.', [], 'Admin.Notifications.Error'),
            ], 404);
        }

        /** @var ExtraPropertyWriterInterface $writer */
        $writer = $this->container->get(ExtraPropertyWriterInterface::class);

        try {
            $writer->toggleExtraProperty($matched, $entityId, $this->getShopContext()->getShopConstraint());
        } catch (Throwable) {
            return new JsonResponse([
                'status' => false,
                'message' => $this->trans('An error occurred while saving.', [], 'Admin.Notifications.Error'),
            ], 500);
        }

        return new JsonResponse([
            'status' => true,
            'message' => $this->trans('Update successful.', [], 'Admin.Notifications.Success'),
        ]);
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
    public function paginationAction(Request $request, ?int $limit = 10, ?int $offset = 0, ?int $total = 0, string $view = 'full', string $prefix = ''): Response
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
        $limitChoices = $request->attributes->get('limit_choices', [10, 20, 50, 100, 300, 1000]);

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
    public function renderSidebarAction(
        Tools $tools,
        string $url,
        string $title = '',
        string $footer = '',
    ): Response {
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
    public function renderKpiRowAction(
        KpiRowInterface $kpiRow,
        KpiRowPresenter $kpiRowPresenter,
    ): Response {
        return $this->render('@PrestaShop/Admin/Common/Kpi/kpi_row.html.twig', [
            'kpiRow' => $kpiRowPresenter->present($kpiRow),
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
    public function resetSearchAction(
        AdminFilterRepository $adminFiltersRepository,
        string $controller = '',
        string $action = '',
        string $filterId = '',
    ): JsonResponse {
        $employeeId = $this->getEmployeeContext()->getEmployee()->getId();
        $shopId = $this->getShopContext()->getId();

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
        GridDefinitionFactoryProvider $gridDefinitionFactoryCollection,
        Request $request,
        string $gridDefinitionFactoryServiceId,
        string $redirectRoute,
        array $redirectQueryParamsToKeep = [],
    ) {
        $definitionFactory = $gridDefinitionFactoryCollection->getFactory($gridDefinitionFactoryServiceId);

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
            return $this->buildSearchResponse(
                $definitionFactory,
                $request,
                $filterId,
                $redirectRoute,
                $redirectQueryParamsToKeep,
            );
        }

        // Legacy grid definition which use controller/action as filter keys (and no scope for parameters)
        /** @var ControllerResponseBuilder $controllerResponseBuilder */
        $controllerResponseBuilder = $this->container->get(ControllerResponseBuilder::class);

        return $controllerResponseBuilder->buildSearchResponse(
            $definitionFactory,
            $request,
            $redirectRoute,
            $redirectQueryParamsToKeep
        );
    }

    /**
     * Derives the BO legacy controller name for a given entity name.
     *
     * Applies standard English pluralization rules to match PS controller naming conventions:
     * - consonant + 'y' → 'ies'  (category → AdminCategories)
     * - 's', 'x', 'z', 'sh', 'ch' → append 'es'  (address → AdminAddresses)
     * - everything else → append 's'  (product → AdminProducts)
     *
     * Used server-side to verify employee permissions without trusting any
     * client-supplied value (e.g. for the extra-property toggle endpoint).
     */
    private static function legacyControllerFromEntityName(string $entityName): string
    {
        $length = strlen($entityName);
        if ($length > 1) {
            $last = strtolower($entityName[$length - 1]);
            $prev = strtolower($entityName[$length - 2]);

            // consonant + 'y' → 'ies'
            if ('y' === $last && !in_array($prev, ['a', 'e', 'i', 'o', 'u'], true)) {
                return 'Admin' . ucfirst(substr($entityName, 0, -1)) . 'ies';
            }

            // 's', 'x', 'z', 'sh', 'ch' → 'es'
            if ('s' === $last || 'x' === $last || 'z' === $last
                || ('h' === $last && in_array($prev, ['s', 'c'], true))) {
                return 'Admin' . ucfirst($entityName) . 'es';
            }
        }

        return 'Admin' . ucfirst($entityName) . 's';
    }

    /**
     * @param Request $request
     *
     * @return RedirectResponse
     */
    #[AdminSecurity("is_granted('update', request.get('_legacy_controller'))")]
    public function updatePositionAction(
        Request $request,
        PositionDefinitionProvider $positionDefinitionProvider,
    ): RedirectResponse {
        $positionsData = [
            'positions' => $request->request->all('positions'),
        ];

        $positionDefinition = $positionDefinitionProvider->getPositionDefinition($request->attributes->get('position_definition'));
        try {
            $this->updateGridPosition($positionDefinition, $positionsData);
            $this->addFlash('success', $this->trans('Successful update', [], 'Admin.Notifications.Success'));
        } catch (PositionUpdateException $e) {
            $errors = [$e->toArray()];
            $this->addFlashErrors($errors);
        }

        return $this->redirectToRoute($request->attributes->get('redirect_route'));
    }
}
