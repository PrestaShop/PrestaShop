<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\Controller\Admin;

use PrestaShop\PrestaShop\Core\FeatureFlag\FeatureFlagSettings;
use PrestaShop\PrestaShop\Core\Grid\Exception\GridViewException;
use PrestaShop\PrestaShop\Core\Grid\View\GridState;
use PrestaShop\PrestaShop\Core\Grid\View\GridViewCounter;
use PrestaShop\PrestaShop\Core\Grid\View\GridViewCsvExporter;
use PrestaShop\PrestaShop\Core\Grid\View\GridViewHandler;
use PrestaShop\PrestaShop\Core\Grid\View\GridViewsPanelPresenter;
use PrestaShop\PrestaShop\Core\Grid\View\GridViewsPresenter;
use PrestaShopBundle\Component\CsvResponse;
use PrestaShopBundle\Entity\AdminGridView;
use PrestaShopBundle\Entity\Repository\AdminGridViewRepository;
use PrestaShopBundle\Form\Admin\Grid\GridConfigurationType;
use PrestaShopBundle\Form\Admin\Grid\GridViewType;
use PrestaShopBundle\Security\Attribute\AdminSecurity;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\RouterInterface;

class GridViewController extends PrestaShopAdminController
{
    private const GRID_ID_REGEX = '/^[a-zA-Z0-9_-]+$/';

    /**
     * @param string $gridId
     * @param Request $request
     * @param RouterInterface $router
     * @param GridViewsPresenter $gridViewsPresenter
     *
     * @return Response
     */
    #[AdminSecurity("is_granted('ROLE_EMPLOYEE')")]
    public function listAction(
        string $gridId,
        Request $request,
        RouterInterface $router,
        GridViewsPresenter $gridViewsPresenter,
    ): Response {
        $this->assertFeatureIsEnabled();
        $this->assertValidGridId($gridId);

        $route = (string) $request->query->get('route', '');
        $this->assertCanReadGridRoute($route, $router);

        return $this->render('@PrestaShop/Admin/Common/Grid/GridViews/views.html.twig', [
            'grid_views' => $gridViewsPresenter->presentViews($gridId, $route),
            'grid_id' => $gridId,
            'selected_view_id' => $request->query->getInt('selected'),
        ]);
    }

    /**
     * @param string $gridId
     * @param Request $request
     * @param RouterInterface $router
     * @param GridViewsPresenter $gridViewsPresenter
     * @param GridViewCounter $gridViewCounter
     *
     * @return JsonResponse
     */
    #[AdminSecurity("is_granted('ROLE_EMPLOYEE')")]
    public function countsAction(
        string $gridId,
        Request $request,
        RouterInterface $router,
        GridViewsPresenter $gridViewsPresenter,
        GridViewCounter $gridViewCounter,
    ): JsonResponse {
        $this->assertFeatureIsEnabled();
        $this->assertValidGridId($gridId);

        $route = (string) $request->query->get('route', '');
        $this->assertCanReadGridRoute($route, $router);

        return $this->json([
            'counts' => $gridViewCounter->countRecords($gridViewsPresenter->findVisibleViews($gridId, $route)),
        ]);
    }

    /**
     * @param string $gridId
     * @param Request $request
     * @param FormFactoryInterface $formFactory
     * @param GridViewHandler $gridViewHandler
     *
     * @return JsonResponse
     */
    #[AdminSecurity("is_granted('ROLE_EMPLOYEE')")]
    public function createAction(
        string $gridId,
        Request $request,
        RouterInterface $router,
        FormFactoryInterface $formFactory,
        GridViewHandler $gridViewHandler,
    ): JsonResponse {
        $this->assertFeatureIsEnabled();
        $this->assertValidGridId($gridId);

        $formName = GridViewsPanelPresenter::SAVE_FORM_NAME_PREFIX . $gridId;
        $submittedData = $request->request->all($formName);

        $form = $formFactory->createNamed($formName, GridViewType::class, null, [
            'active_date_filters' => $this->getSubmittedDateRuleFields($submittedData),
        ]);
        $form->handleRequest($request);

        if (!$form->isSubmitted() || !$form->isValid()) {
            return $this->formErrorResponse($form);
        }

        $this->assertCanReadGridRoute((string) ($form->getData()['controller_route'] ?? ''), $router);

        try {
            $gridViewHandler->createFromPersistedFilters($gridId, $form->getData());
        } catch (GridViewException $e) {
            return $this->gridViewExceptionResponse($e);
        }

        return $this->json([
            'success' => true,
            'message' => $this->trans('Successful creation', [], 'Admin.Notifications.Success'),
        ]);
    }

    /**
     * @param int $gridViewId
     * @param Request $request
     * @param FormFactoryInterface $formFactory
     * @param AdminGridViewRepository $gridViewRepository
     * @param GridViewHandler $gridViewHandler
     *
     * @return Response
     */
    #[AdminSecurity("is_granted('ROLE_EMPLOYEE')")]
    public function editAction(
        int $gridViewId,
        Request $request,
        FormFactoryInterface $formFactory,
        AdminGridViewRepository $gridViewRepository,
        GridViewHandler $gridViewHandler,
    ): Response {
        $this->assertFeatureIsEnabled();

        $gridView = $this->getOwnGridView($gridViewId, $gridViewRepository);
        $gridId = $gridView->getGridConfiguration()->getGridId();

        $form = $formFactory->createNamed(
            GridViewsPanelPresenter::SAVE_FORM_NAME_PREFIX . $gridId,
            GridViewType::class,
            [
                'name' => $gridView->getName(),
                'shared' => $gridView->isShared(),
                'dynamic_date_rules' => $gridView->getDynamicDateRules() ?? [],
            ],
            [
                'with_grid_context' => false,
                'active_date_filters' => $this->getStoredDateRuleFields($gridView),
            ]
        );
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if (!$form->isValid()) {
                return $this->formErrorResponse($form);
            }

            $gridViewHandler->update($gridView, $form->getData());

            return $this->json([
                'success' => true,
                'message' => $this->trans('Successful update', [], 'Admin.Notifications.Success'),
            ]);
        }

        return $this->json([
            'success' => true,
            'content' => $this->renderView('@PrestaShop/Admin/Common/Grid/GridViews/form.html.twig', [
                'grid_view_form' => $form->createView(),
                'form_action' => $this->generateUrl('admin_grid_views_edit', ['gridViewId' => $gridViewId]),
            ]),
        ]);
    }

    /**
     * @param int $gridViewId
     * @param AdminGridViewRepository $gridViewRepository
     * @param GridViewHandler $gridViewHandler
     *
     * @return JsonResponse
     */
    #[AdminSecurity("is_granted('ROLE_EMPLOYEE')")]
    public function deleteAction(
        int $gridViewId,
        AdminGridViewRepository $gridViewRepository,
        GridViewHandler $gridViewHandler,
    ): JsonResponse {
        $this->assertFeatureIsEnabled();

        $gridView = $this->getOwnGridView($gridViewId, $gridViewRepository);
        $gridViewHandler->delete($gridView);

        return $this->json([
            'success' => true,
            'message' => $this->trans('Successful deletion', [], 'Admin.Notifications.Success'),
        ]);
    }

    /**
     * @param int $gridViewId
     * @param AdminGridViewRepository $gridViewRepository
     * @param GridViewHandler $gridViewHandler
     *
     * @return JsonResponse
     */
    #[AdminSecurity("is_granted('ROLE_EMPLOYEE')")]
    public function duplicateAction(
        int $gridViewId,
        RouterInterface $router,
        AdminGridViewRepository $gridViewRepository,
        GridViewHandler $gridViewHandler,
    ): JsonResponse {
        $this->assertFeatureIsEnabled();

        $gridView = $this->getAccessibleGridView($gridViewId, $gridViewRepository);
        $this->assertCanReadGridRoute($gridView->getGridConfiguration()->getControllerRoute(), $router);

        try {
            $gridViewHandler->duplicate(
                $gridView,
                $this->trans('Copy of %name%', ['%name%' => $gridView->getName()], 'Admin.Global')
            );
        } catch (GridViewException $e) {
            return $this->gridViewExceptionResponse($e);
        }

        return $this->json([
            'success' => true,
            'message' => $this->trans('Successful duplication', [], 'Admin.Notifications.Success'),
        ]);
    }

    /**
     * @param string $gridId
     * @param Request $request
     * @param FormFactoryInterface $formFactory
     * @param GridViewHandler $gridViewHandler
     *
     * @return JsonResponse
     */
    #[AdminSecurity("is_granted('ROLE_EMPLOYEE')")]
    public function saveConfigurationAction(
        string $gridId,
        Request $request,
        FormFactoryInterface $formFactory,
        GridViewHandler $gridViewHandler,
    ): JsonResponse {
        $this->assertFeatureIsEnabled();
        $this->assertValidGridId($gridId);

        $form = $formFactory->createNamed(
            GridViewsPanelPresenter::CONFIGURATION_FORM_NAME_PREFIX . $gridId,
            GridConfigurationType::class
        );
        $form->handleRequest($request);

        if (!$form->isSubmitted() || !$form->isValid()) {
            return $this->formErrorResponse($form);
        }

        try {
            $gridViewHandler->saveConfiguration($gridId, $form->getData());
        } catch (GridViewException $e) {
            return $this->gridViewExceptionResponse($e);
        }

        return $this->json([
            'success' => true,
            'message' => $this->trans('Update successful', [], 'Admin.Notifications.Success'),
        ]);
    }

    /**
     * @param int $gridViewId
     * @param RouterInterface $router
     * @param AdminGridViewRepository $gridViewRepository
     * @param GridViewCsvExporter $gridViewCsvExporter
     *
     * @return CsvResponse
     */
    #[AdminSecurity("is_granted('ROLE_EMPLOYEE')")]
    public function exportAction(
        int $gridViewId,
        RouterInterface $router,
        AdminGridViewRepository $gridViewRepository,
        GridViewCsvExporter $gridViewCsvExporter,
    ): CsvResponse {
        $this->assertFeatureIsEnabled();

        $gridView = $this->getAccessibleGridView($gridViewId, $gridViewRepository);
        $this->assertCanReadGridRoute($gridView->getGridConfiguration()->getControllerRoute(), $router);

        try {
            $exportedData = $gridViewCsvExporter->export($gridView);
        } catch (GridViewException $e) {
            throw new NotFoundHttpException('This view cannot be exported.', $e);
        }

        $fileNameSlug = trim(preg_replace('/[^a-zA-Z0-9_-]+/', '-', $gridView->getName()) ?? '', '-');

        return (new CsvResponse())
            ->setData($exportedData['rows_provider'])
            ->setModeType(CsvResponse::MODE_OFFSET)
            ->setLimit(GridViewCsvExporter::CHUNK_SIZE)
            ->setHeadersData($exportedData['headers'])
            ->setFileName('grid_view_' . ('' !== $fileNameSlug ? $fileNameSlug . '_' : '') . date('Y-m-d_His') . '.csv')
        ;
    }

    /**
     * @param int $gridViewId
     * @param AdminGridViewRepository $repository
     *
     * @return AdminGridView
     */
    private function getGridView(int $gridViewId, AdminGridViewRepository $repository): AdminGridView
    {
        $gridView = $repository->find($gridViewId);

        if (null === $gridView) {
            throw new NotFoundHttpException(sprintf('Grid view %d was not found', $gridViewId));
        }

        return $gridView;
    }

    /**
     * @param int $gridViewId
     * @param AdminGridViewRepository $repository
     *
     * @return AdminGridView
     */
    private function getAccessibleGridView(int $gridViewId, AdminGridViewRepository $repository): AdminGridView
    {
        $gridView = $this->getGridView($gridViewId, $repository);
        $configuration = $gridView->getGridConfiguration();
        $employee = $this->getEmployeeContext()->getEmployee();

        $isOwn = null !== $employee && $configuration->getEmployeeId() === $employee->getId();
        if ((!$isOwn && !$gridView->isShared())
            || !$this->getEmployeeContext()->hasAuthorizationOnShop($configuration->getShopId())
        ) {
            throw new AccessDeniedHttpException('You cannot use this view.');
        }

        return $gridView;
    }

    /**
     * @param int $gridViewId
     * @param AdminGridViewRepository $repository
     *
     * @return AdminGridView
     */
    private function getOwnGridView(int $gridViewId, AdminGridViewRepository $repository): AdminGridView
    {
        $gridView = $this->getGridView($gridViewId, $repository);
        $configuration = $gridView->getGridConfiguration();
        $employee = $this->getEmployeeContext()->getEmployee();

        if (null === $employee
            || $configuration->getEmployeeId() !== $employee->getId()
            || !$this->getEmployeeContext()->hasAuthorizationOnShop($configuration->getShopId())
        ) {
            throw new AccessDeniedHttpException('You cannot modify this view.');
        }

        return $gridView;
    }

    /**
     * @return array<string, array{id: string, name: string}>
     */
    private function getSubmittedDateRuleFields(array $submittedData): array
    {
        $dateRuleFields = [];
        $submittedFields = array_slice(array_keys($submittedData['dynamic_date_rules'] ?? []), 0, 20);

        foreach ($submittedFields as $field) {
            $field = (string) $field;
            if (preg_match(self::GRID_ID_REGEX, $field)) {
                $dateRuleFields[$field] = ['id' => $field, 'name' => $field];
            }
        }

        return $dateRuleFields;
    }

    /**
     * @return array<string, array{id: string, name: string}>
     */
    private function getStoredDateRuleFields(AdminGridView $gridView): array
    {
        $searchCriteria = json_decode($gridView->getFilters(), true) ?: [];
        $gridState = GridState::fromArray($gridView->getGridState() ?? []);

        $columnNames = [];
        foreach ($gridState->columns as $column) {
            $columnNames[$column->id] = $column->name;
        }

        $dateRuleFields = [];
        foreach ($searchCriteria['filters'] ?? [] as $field => $value) {
            if (!is_array($value) || (!isset($value['from']) && !isset($value['to']))) {
                continue;
            }

            $dateRuleFields[$field] = [
                'id' => $field,
                'name' => $columnNames[$field] ?? $field,
            ];
        }

        return $dateRuleFields;
    }

    /**
     * @param FormInterface $form
     *
     * @return JsonResponse
     */
    private function formErrorResponse(FormInterface $form): JsonResponse
    {
        return $this->json([
            'success' => false,
            'message' => (string) $form->getErrors(true, false),
        ], Response::HTTP_BAD_REQUEST);
    }

    /**
     * @param GridViewException $e
     *
     * @return JsonResponse
     */
    private function gridViewExceptionResponse(GridViewException $e): JsonResponse
    {
        $message = match ($e->getCode()) {
            GridViewException::VIEW_LIMIT_REACHED => $this->trans('You have reached the maximum number of views for this grid.', [], 'Admin.Notifications.Error'),
            default => $this->trans('An unexpected error occurred.', [], 'Admin.Notifications.Error'),
        };

        return $this->json([
            'success' => false,
            'message' => $message,
        ], Response::HTTP_BAD_REQUEST);
    }

    /**
     * @param string $gridId
     *
     * @return void
     */
    private function assertValidGridId(string $gridId): void
    {
        if (!preg_match(self::GRID_ID_REGEX, $gridId)) {
            throw new NotFoundHttpException('Invalid grid id');
        }
    }

    /**
     * @param string $route
     * @param RouterInterface $router
     *
     * @return void
     */
    private function assertCanReadGridRoute(string $route, RouterInterface $router): void
    {
        if (empty($route)) {
            throw new NotFoundHttpException('Missing grid route');
        }

        $gridRoute = $router->getRouteCollection()->get($route);
        if (null === $gridRoute) {
            throw new NotFoundHttpException(sprintf('Unknown route "%s"', $route));
        }

        $legacyController = $gridRoute->getDefault('_legacy_controller');
        if (is_string($legacyController) && '' !== $legacyController && !$this->isGranted('read', $legacyController)) {
            throw new AccessDeniedHttpException('You are not allowed to view this page.');
        }
    }

    /**
     * @return void
     */
    private function assertFeatureIsEnabled(): void
    {
        if (!$this->getFeatureFlagStateChecker()->isEnabled(FeatureFlagSettings::FEATURE_FLAG_GRID_VIEWS)) {
            throw new NotFoundHttpException('Advanced grid filters feature is disabled');
        }

        if (!$this->getShopContext()->isSingleShopContext()) {
            throw new NotFoundHttpException('Grid views are only available in single shop context');
        }
    }
}
