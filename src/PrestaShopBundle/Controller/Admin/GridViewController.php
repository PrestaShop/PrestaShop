<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\Controller\Admin;

use PrestaShop\PrestaShop\Adapter\GridView\GridViewProvider;
use PrestaShop\PrestaShop\Core\Domain\GridView\Command\DeleteGridViewCommand;
use PrestaShop\PrestaShop\Core\Domain\GridView\Command\DuplicateGridViewCommand;
use PrestaShop\PrestaShop\Core\Domain\GridView\Exception\GridViewAccessDeniedException;
use PrestaShop\PrestaShop\Core\Domain\GridView\Exception\GridViewException;
use PrestaShop\PrestaShop\Core\Domain\GridView\Exception\GridViewLimitReachedException;
use PrestaShop\PrestaShop\Core\Domain\GridView\Exception\GridViewNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\GridView\ValueObject\GridViewId;
use PrestaShop\PrestaShop\Core\FeatureFlag\FeatureFlagSettings;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\Builder\FormBuilderInterface;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\Handler\FormHandlerInterface;
use PrestaShop\PrestaShop\Core\Grid\View\GridViewCounter;
use PrestaShop\PrestaShop\Core\Grid\View\GridViewCsvExporter;
use PrestaShop\PrestaShop\Core\Grid\View\GridViewsPresenter;
use PrestaShopBundle\Component\CsvResponse;
use PrestaShopBundle\Security\Attribute\AdminSecurity;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
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

    private const VIEW_FORM_NAME = 'grid_view';

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
     * @param RouterInterface $router
     * @param FormBuilderInterface $gridViewFormBuilder
     * @param FormHandlerInterface $gridViewFormHandler
     *
     * @return JsonResponse
     */
    #[AdminSecurity("is_granted('ROLE_EMPLOYEE')")]
    public function createAction(
        string $gridId,
        Request $request,
        RouterInterface $router,
        #[Autowire(service: 'prestashop.core.form.identifiable_object.builder.grid_view_form_builder')]
        FormBuilderInterface $gridViewFormBuilder,
        #[Autowire(service: 'prestashop.core.form.identifiable_object.handler.grid_view_form_handler')]
        FormHandlerInterface $gridViewFormHandler,
    ): JsonResponse {
        $this->assertFeatureIsEnabled();
        $this->assertValidGridId($gridId);

        // The dynamic date rule fields depend on the date filters the client submitted
        $submittedData = $request->request->all(self::VIEW_FORM_NAME);
        $form = $gridViewFormBuilder->getForm([], [
            'active_date_filters' => $this->getSubmittedDateRuleFields($submittedData),
        ]);
        $form->handleRequest($request);

        if (!$form->isSubmitted() || !$form->isValid()) {
            return $this->formErrorResponse($form);
        }

        $data = $form->getData();
        $this->assertGridIdMatches($gridId, (string) ($data['grid_id'] ?? ''));
        $this->assertCanReadGridRoute((string) ($data['controller_route'] ?? ''), $router);

        try {
            $gridViewFormHandler->handle($form);
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
     * @param FormBuilderInterface $gridViewFormBuilder
     * @param FormHandlerInterface $gridViewFormHandler
     *
     * @return Response
     */
    #[AdminSecurity("is_granted('ROLE_EMPLOYEE')")]
    public function editAction(
        int $gridViewId,
        Request $request,
        #[Autowire(service: 'prestashop.core.form.identifiable_object.builder.grid_view_form_builder')]
        FormBuilderInterface $gridViewFormBuilder,
        #[Autowire(service: 'prestashop.core.form.identifiable_object.handler.grid_view_form_handler')]
        FormHandlerInterface $gridViewFormHandler,
    ): Response {
        $this->assertFeatureIsEnabled();

        try {
            $form = $gridViewFormBuilder->getFormFor($gridViewId);
        } catch (GridViewException $e) {
            throw $this->httpExceptionFor($e, 'This view cannot be edited.');
        }
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if (!$form->isValid()) {
                return $this->formErrorResponse($form);
            }

            try {
                $gridViewFormHandler->handleFor($gridViewId, $form);
            } catch (GridViewNotFoundException|GridViewAccessDeniedException $e) {
                throw $this->httpExceptionFor($e, 'This view cannot be edited.');
            } catch (GridViewException $e) {
                return $this->gridViewExceptionResponse($e);
            }

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
     *
     * @return JsonResponse
     */
    #[AdminSecurity("is_granted('ROLE_EMPLOYEE')")]
    public function deleteAction(int $gridViewId): JsonResponse
    {
        $this->assertFeatureIsEnabled();

        try {
            $this->dispatchCommand(new DeleteGridViewCommand($gridViewId));
        } catch (GridViewNotFoundException|GridViewAccessDeniedException $e) {
            throw $this->httpExceptionFor($e, 'This view cannot be deleted.');
        } catch (GridViewException $e) {
            return $this->gridViewExceptionResponse($e);
        }

        return $this->json([
            'success' => true,
            'message' => $this->trans('Successful deletion', [], 'Admin.Notifications.Success'),
        ]);
    }

    /**
     * @param int $gridViewId
     * @param RouterInterface $router
     * @param GridViewProvider $gridViewProvider
     *
     * @return JsonResponse
     */
    #[AdminSecurity("is_granted('ROLE_EMPLOYEE')")]
    public function duplicateAction(
        int $gridViewId,
        RouterInterface $router,
        GridViewProvider $gridViewProvider,
    ): JsonResponse {
        $this->assertFeatureIsEnabled();

        try {
            $gridView = $gridViewProvider->getAccessibleGridView(new GridViewId($gridViewId));
            $this->assertCanReadGridRoute($gridView->getGridConfiguration()->getControllerRoute(), $router);

            $this->dispatchCommand(new DuplicateGridViewCommand(
                $gridViewId,
                $this->trans('Copy of %name%', ['%name%' => $gridView->getName()], 'Admin.Global')
            ));
        } catch (GridViewNotFoundException|GridViewAccessDeniedException $e) {
            throw $this->httpExceptionFor($e, 'This view cannot be duplicated.');
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
     * @param FormBuilderInterface $gridConfigurationFormBuilder
     * @param FormHandlerInterface $gridConfigurationFormHandler
     *
     * @return JsonResponse
     */
    #[AdminSecurity("is_granted('ROLE_EMPLOYEE')")]
    public function saveConfigurationAction(
        string $gridId,
        Request $request,
        #[Autowire(service: 'prestashop.core.form.identifiable_object.builder.grid_configuration_form_builder')]
        FormBuilderInterface $gridConfigurationFormBuilder,
        #[Autowire(service: 'prestashop.core.form.identifiable_object.handler.grid_configuration_form_handler')]
        FormHandlerInterface $gridConfigurationFormHandler,
    ): JsonResponse {
        $this->assertFeatureIsEnabled();
        $this->assertValidGridId($gridId);

        $form = $gridConfigurationFormBuilder->getForm();
        $form->handleRequest($request);

        if (!$form->isSubmitted() || !$form->isValid()) {
            return $this->formErrorResponse($form);
        }

        $this->assertGridIdMatches($gridId, (string) ($form->getData()['grid_id'] ?? ''));

        try {
            $gridConfigurationFormHandler->handle($form);
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
     * @param GridViewProvider $gridViewProvider
     * @param GridViewCsvExporter $gridViewCsvExporter
     *
     * @return CsvResponse
     */
    #[AdminSecurity("is_granted('ROLE_EMPLOYEE')")]
    public function exportAction(
        int $gridViewId,
        RouterInterface $router,
        GridViewProvider $gridViewProvider,
        GridViewCsvExporter $gridViewCsvExporter,
    ): CsvResponse {
        $this->assertFeatureIsEnabled();

        try {
            $gridView = $gridViewProvider->getAccessibleGridView(new GridViewId($gridViewId));
        } catch (GridViewException $e) {
            throw $this->httpExceptionFor($e, 'This view cannot be exported.');
        }
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
        $message = match (true) {
            $e instanceof GridViewLimitReachedException => $this->trans('You have reached the maximum number of views for this grid.', [], 'Admin.Notifications.Error'),
            default => $this->trans('An unexpected error occurred.', [], 'Admin.Notifications.Error'),
        };

        return $this->json([
            'success' => false,
            'message' => $message,
        ], Response::HTTP_BAD_REQUEST);
    }

    /**
     * Maps a domain exception raised while targeting a specific view to the matching HTTP exception.
     *
     * @param GridViewException $e
     * @param string $message
     *
     * @return NotFoundHttpException|AccessDeniedHttpException
     */
    private function httpExceptionFor(GridViewException $e, string $message): NotFoundHttpException|AccessDeniedHttpException
    {
        if ($e instanceof GridViewAccessDeniedException) {
            return new AccessDeniedHttpException($message, $e);
        }

        return new NotFoundHttpException($message, $e);
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
     * The grid id is carried both by the route and by the submitted form: the form value feeds
     * the command, so both must designate the same grid.
     *
     * @param string $routeGridId
     * @param string $formGridId
     *
     * @return void
     */
    private function assertGridIdMatches(string $routeGridId, string $formGridId): void
    {
        if ($routeGridId !== $formGridId) {
            throw new NotFoundHttpException(sprintf('Submitted grid id "%s" does not match the requested grid "%s"', $formGridId, $routeGridId));
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
