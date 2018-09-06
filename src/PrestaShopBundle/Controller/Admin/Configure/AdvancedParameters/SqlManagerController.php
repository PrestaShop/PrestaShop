<?php
/**
 * 2007-2017 PrestaShop.
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
 * @copyright 2007-2017 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShopBundle\Controller\Admin\Configure\AdvancedParameters;

use Exception;
use PrestaShop\PrestaShop\Core\Domain\SqlManagement\Command\BulkDeleteSqlRequestCommand;
use PrestaShop\PrestaShop\Core\Domain\SqlManagement\Command\DeleteSqlRequestCommand;
use PrestaShop\PrestaShop\Core\Domain\SqlManagement\DatabaseTableFields;
use PrestaShop\PrestaShop\Core\Domain\SqlManagement\DatabaseTablesList;
use PrestaShop\PrestaShop\Core\Domain\SqlManagement\Exception\CannotDeleteSqlRequestException;
use PrestaShop\PrestaShop\Core\Domain\SqlManagement\Exception\SqlRequestException;
use PrestaShop\PrestaShop\Core\Domain\SqlManagement\Exception\SqlRequestNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\SqlManagement\Query\GetDatabaseTableFieldsList;
use PrestaShop\PrestaShop\Core\Domain\SqlManagement\Query\GetDatabaseTablesList;
use PrestaShop\PrestaShop\Core\Domain\SqlManagement\Query\GetSqlRequestExecutionResult;
use PrestaShop\PrestaShop\Core\Domain\SqlManagement\Query\GetSqlRequestSettings;
use PrestaShop\PrestaShop\Core\Domain\SqlManagement\SqlRequestExecutionResult;
use PrestaShop\PrestaShop\Core\Domain\SqlManagement\SqlRequestSettings;
use PrestaShop\PrestaShop\Core\Domain\SqlManagement\ValueObject\SqlRequestId;
use PrestaShop\PrestaShop\Core\Export\Exception\FileWritingException;
use PrestaShop\PrestaShop\Core\Form\FormHandlerInterface;
use PrestaShop\PrestaShop\Core\Search\Filters\RequestSqlFilters;
use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use PrestaShopBundle\Form\Admin\Configure\AdvancedParameters\RequestSql\SqlRequestFormHandler;
use PrestaShopBundle\Security\Annotation\AdminSecurity;
use PrestaShopBundle\Security\Annotation\DemoRestricted;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

/**
 * Responsible of "Configure > Advanced Parameters > Database -> SQL Manager" page.
 */
class SqlManagerController extends FrameworkBundleAdminController
{
    /**
     * Show list of saved SQL's.
     *
     * @AdminSecurity(
     *     "is_granted(['read','update', 'create','delete'], request.get('_legacy_controller')~'_')",
     *      message="Access denied."
     * )
     *
     * @param Request $request
     * @param RequestSqlFilters $filters
     *
     * @return Response
     */
    public function indexAction(Request $request, RequestSqlFilters $filters)
    {
        // handle "Export to SQL manager" action from legacy pages
        if ($request->query->has('addrequest_sql')) {
            return $this->forward('PrestaShopBundle:Admin\Configure\AdvancedParameters\RequestSql:create');
        }

        $gridLogFactory = $this->get('prestashop.core.grid.factory.request_sql');
        $grid = $gridLogFactory->getGrid($filters);

        $gridPresenter = $this->get('prestashop.core.grid.presenter.grid_presenter');
        $presentedGrid = $gridPresenter->present($grid);

        $settingsForm = $this->getSettingsFormHandler()->getForm();

        return $this->render('@PrestaShop/Admin/Configure/AdvancedParameters/RequestSql/list.html.twig', [
            'layoutHeaderToolbarBtn' => [
                'add' => [
                    'href' => $this->generateUrl('admin_sql_request_create'),
                    'desc' => $this->trans('Add new SQL query', 'Admin.Advparameters.Feature'),
                    'icon' => 'add_circle_outline',
                ],
            ],
            'layoutTitle' => $this->trans('SQL Manager', 'Admin.Navigation.Menu'),
            'requireAddonsSearch' => true,
            'enableSidebar' => true,
            'help_link' => $this->generateSidebarLink($request->attributes->get('_legacy_controller')),
            'requestSqlSettingsForm' => $settingsForm->createView(),
            'requestSqlGrid' => $presentedGrid,
        ]);
    }

    /**
     * @AdminSecurity("is_granted(['read', 'update', 'create', 'delete'], request.get('_legacy_controller')~'_')", message="You do not have permission to update this.", redirectRoute="admin_logs")
     * @DemoRestricted(redirectRoute="admin_logs")
     *
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function searchAction(Request $request)
    {
        $definitionFactory = $this->get('prestashop.core.grid.definition.factory.request_sql');
        $emailLogsDefinition = $definitionFactory->getDefinition();

        $gridFilterFormFactory = $this->get('prestashop.core.grid.filter.form_factory');
        $filtersForm = $gridFilterFormFactory->create($emailLogsDefinition);
        $filtersForm->handleRequest($request);

        $filters = [];

        if ($filtersForm->isSubmitted()) {
            $filters = $filtersForm->getData();
        }

        return $this->redirectToRoute('admin_sql_request', ['filters' => $filters]);
    }

    /**
     * Process Request SQL settings save.
     *
     * @DemoRestricted(redirectRoute="admin_sql_request")
     * @AdminSecurity(
     *     "is_granted(['update', 'create','delete'], request.get('_legacy_controller')~'_')",
     *      message="You do not have permission to edit this."
     * )
     *
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function processFormAction(Request $request)
    {
        $handler = $this->getSettingsFormHandler();
        $settingForm = $handler->getForm();
        $settingForm->handleRequest($request);

        if ($settingForm->isSubmitted()) {
            if (!$errors = $handler->save($settingForm->getData())) {
                $this->addFlash('success', $this->trans('Successful update.', 'Admin.Notifications.Success'));
            } else {
                $this->flashErrors($errors);
            }
        }

        return $this->redirectToRoute('admin_sql_request');
    }

    /**
     * Show Request SQL create page.
     *
     * @AdminSecurity(
     *     "is_granted(['create'], request.get('_legacy_controller')~'_')",
     *      message="You do not have permission to create this."
     * )
     *
     * @param Request $request
     *
     * @return Response
     */
    public function createAction(Request $request)
    {
        $requestSqlFormHandler = $this->getRequestSqlFormHandler();

        $requestSqlData = [];

        // handle "Export to SQL Manager" action data
        if ($request->request->has('sql')) {
            $requestSqlData['request_sql'] = [
                'sql' => $request->request->get('sql'),
                'name' => $request->request->get('name'),
            ];
        }

        $requestSqlForm = $requestSqlFormHandler->getForm();
        $requestSqlForm->setData($requestSqlData);
        $requestSqlForm->handleRequest($request);

        if ($requestSqlForm->isSubmitted()) {
            if ($this->isDemoModeEnabled()) {
                $this->addFlash('error', $this->getDemoModeErrorMessage());

                return $this->redirectToRoute('admin_sql_request');
            }

            $errors = $requestSqlFormHandler->save($requestSqlForm->getData());

            if (empty($errors)) {
                $this->addFlash('success', $this->trans('Successful creation.', 'Admin.Notifications.Success'));

                return $this->redirectToRoute('admin_sql_request');
            }

            $this->flashErrors($errors);
        }

        return $this->render('@PrestaShop/Admin/Configure/AdvancedParameters/RequestSql/form.html.twig', [
            'layoutTitle' => $this->trans('SQL Manager', 'Admin.Navigation.Menu'),
            'requireAddonsSearch' => true,
            'enableSidebar' => true,
            'help_link' => $this->generateSidebarLink($request->attributes->get('_legacy_controller')),
            'requestSqlForm' => $requestSqlForm->createView(),
            'dbTableNames' => $this->getDatabaseTables(),
        ]);
    }

    /**
     * Show Request SQL edit page.
     *
     * @AdminSecurity(
     *     "is_granted(['update'], request.get('_legacy_controller')~'_')",
     *     message="You do not have permission to edit this."
     * )
     *
     * @param int $sqlRequestId
     * @param Request $request
     *
     * @return Response
     */
    public function editAction($sqlRequestId, Request $request)
    {
        $requestSqlFormHandler = $this->getRequestSqlFormHandler();

        $editRequestSqlForm = $requestSqlFormHandler->getFormFor($sqlRequestId);
        $editRequestSqlForm->handleRequest($request);

        if ($editRequestSqlForm->isSubmitted()) {
            if ($this->isDemoModeEnabled()) {
                $this->addFlash('error', $this->getDemoModeErrorMessage());

                return $this->redirectToRoute('admin_sql_request');
            }

            $errors = $requestSqlFormHandler->save($editRequestSqlForm->getData());

            if (empty($errors)) {
                $this->addFlash(
                    'success',
                    $this->trans('Successful update.', 'Admin.Notifications.Success')
                );

                return $this->redirectToRoute('admin_sql_request');
            }

            $this->flashErrors($errors);
        }

        return $this->render('@PrestaShop/Admin/Configure/AdvancedParameters/RequestSql/form.html.twig', [
            'layoutTitle' => $this->trans('SQL Manager', 'Admin.Navigation.Menu'),
            'requireAddonsSearch' => true,
            'enableSidebar' => true,
            'help_link' => $this->generateSidebarLink($request->attributes->get('_legacy_controller')),
            'requestSqlForm' => $editRequestSqlForm->createView(),
            'dbTableNames' => $this->getDatabaseTables(),
        ]);
    }

    /**
     * Delete selected Request SQL.
     *
     * @AdminSecurity(
     *     "is_granted(['delete'], request.get('_legacy_controller')~'_')",
     *     message="You do not have permission to edit this."
     * )
     * @DemoRestricted(redirectRoute="admin_sql_request")
     *
     * @param int $sqlRequestId ID of selected Request SQL
     *
     * @return RedirectResponse
     */
    public function deleteAction($sqlRequestId)
    {
        try {
            $deleteSqlRequestCommand = new DeleteSqlRequestCommand(
                new SqlRequestId($sqlRequestId)
            );

            $this->getCommandBus()->handle($deleteSqlRequestCommand);

            $this->addFlash('success', $this->trans('Successful deletion', 'Admin.Notifications.Success'));
        } catch (SqlRequestException $e) {
            $this->addFlash('error', $this->handleDeleteException($e));
        }

        return $this->redirectToRoute('admin_sql_request');
    }

    /**
     * Process bulk action delete of RequestSql's.
     *
     * @AdminSecurity(
     *     "is_granted(['delete'], request.get('_legacy_controller')~'_')",
     *     message="You do not have permission to edit this."
     * )
     * @DemoRestricted(redirectRoute="admin_sql_request")
     *
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function deleteBulkAction(Request $request)
    {
        try {
            $requestSqlIds = $request->request->get('request_sql_bulk');
            $bulkDeleteSqlRequestCommand = new BulkDeleteSqlRequestCommand($requestSqlIds);

            $this->getCommandBus()->handle($bulkDeleteSqlRequestCommand);

            $this->addFlash(
                'success',
                $this->trans('The selection has been successfully deleted.', 'Admin.Notifications.Success')
            );
        } catch (SqlRequestException $e) {
            $this->addFlash('error', $this->handleDeleteException($e));
        }

        return $this->redirectToRoute('admin_sql_request');
    }

    /**
     * View Request SQL query data.
     *
     * @AdminSecurity(
     *     "is_granted(['read'], request.get('_legacy_controller')~'_')",
     *     message="You do not have permission to view this."
     * )
     *
     * @param Request $request
     * @param int $sqlRequestId
     *
     * @return Response
     */
    public function viewAction(Request $request, $sqlRequestId)
    {
        try {
            $query = new GetSqlRequestExecutionResult($sqlRequestId);

            $sqlRequestExecutionResult = $this->getQueryBus()->handle($query);
        } catch (SqlRequestException $e) {
            $this->addFlash('error', $this->handleViewException($e));

            return $this->redirectToRoute('admin_sql_request');
        }

        return $this->render('@PrestaShop/Admin/Configure/AdvancedParameters/RequestSql/view.html.twig', [
            'layoutHeaderToolbarBtn' => [],
            'layoutTitle' => $this->trans('SQL Manager', 'Admin.Navigation.Menu'),
            'requireAddonsSearch' => true,
            'enableSidebar' => true,
            'help_link' => $this->generateSidebarLink($request->attributes->get('_legacy_controller')),
            'sqlRequestResult' => $sqlRequestExecutionResult,
        ]);
    }

    /**
     * Export Request SQL data.
     *
     * @AdminSecurity(
     *     "is_granted(['read'], request.get('_legacy_controller'))",
     *     message="Access denied."
     * )
     * @DemoRestricted(redirectRoute="admin_sql_request")
     *
     * @param int $sqlRequestId Request SQL id
     *
     * @return RedirectResponse|BinaryFileResponse
     */
    public function exportAction($sqlRequestId)
    {
        $requestSqlExporter = $this->get('prestashop.core.sql_manager.exporter.sql_request_exporter');

        try {
            $query = new GetSqlRequestExecutionResult($sqlRequestId);
            /** @var SqlRequestExecutionResult $sqlRequestExecutionResult */
            $sqlRequestExecutionResult = $this->getQueryBus()->handle($query);

            $exportedFile = $requestSqlExporter->exportToFile(
                $query->getSqlRequestId(),
                $sqlRequestExecutionResult
            );

            /** @var SqlRequestSettings $sqlRequestSettings */
            $sqlRequestSettings = $this->getQueryBus()->handle(new GetSqlRequestSettings());
        } catch (SqlRequestException $e) {
            $this->addFlash('error', $this->handleExportException($e));

            return $this->redirectToRoute('admin_sql_request');
        }

        $response = new BinaryFileResponse($exportedFile->getPathname());
        $response->setCharset($sqlRequestSettings->getFileEncoding());
        $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, $exportedFile->getFilename());

        return $response;
    }

    /**
     * Get MySQL table columns data.
     *
     * @param string $mySqlTableName Database tabe name
     *
     * @return JsonResponse
     */
    public function ajaxTableColumnsAction($mySqlTableName)
    {
        $query = new GetDatabaseTableFieldsList($mySqlTableName);
        /** @var DatabaseTableFields $databaseFields */
        $databaseFields = $this->getQueryBus()->handle($query);

        return $this->json(['columns' => $databaseFields->getFields()]);
    }

    /**
     * Get request sql repository.
     *
     * @return \PrestaShopBundle\Entity\Repository\RequestSqlRepository
     */
    protected function getRepository()
    {
        return $this->get('prestashop.core.admin.request_sql.repository');
    }

    /**
     * Get Request SQL settings form handler.
     *
     * @return FormHandlerInterface
     */
    protected function getSettingsFormHandler()
    {
        return $this->get('prestashop.admin.request_sql_settings.form_handler');
    }

    /**
     * @return SqlRequestFormHandler
     */
    protected function getRequestSqlFormHandler()
    {
        return $this->get('prestashop.admin.request_sql.form_handler');
    }

    /**
     * Get human readable error for exception.
     *
     * @param SqlRequestException $e
     *
     * @return string Error message
     */
    protected function handleDeleteException(SqlRequestException $e)
    {
        $code = $e->getCode();
        $type = get_class($e);

        $exceptionMessages = [
            SqlRequestNotFoundException::class => $this->trans('The object cannot be loaded (or found)', 'Admin.Notifications.Error'),
            SqlRequestException::class => $this->trans('An error occurred while deleting the object.', 'Admin.Notifications.Error'),
        ];

        $deleteExceptionMessages = [
            CannotDeleteSqlRequestException::CANNOT_SINGLE_DELETE => $this->trans('An error occurred while deleting the object.', 'Admin.Notifications.Error'),
            CannotDeleteSqlRequestException::CANNOT_BULK_DELETE => $this->trans('An error occurred while deleting this selection.', 'Admin.Notifications.Error'),
        ];

        if (CannotDeleteSqlRequestException::class === $type
            && isset($deleteExceptionMessages[$code])
        ) {
            return $deleteExceptionMessages[$code];
        }

        if (isset($exceptionMessages[$type])) {
            return $exceptionMessages[$type];
        }

        return $this->getFallbackErrorMessage($type, $code);
    }

    /**
     * Get error message when exception occurs on View action.
     *
     * @param SqlRequestException $e
     *
     * @return string
     */
    protected function handleViewException(SqlRequestException $e)
    {
        $type = get_class($e);

        $exceptionMessages = [
            SqlRequestNotFoundException::class => $this->trans('The object cannot be loaded (or found)', 'Admin.Notifications.Error'),
        ];

        if (isset($exceptionMessages[$type])) {
            return $exceptionMessages[$type];
        }

        return $this->getFallbackErrorMessage($type, $e->getCode());
    }

    /**
     * @param Exception $e
     *
     * @return string Error message
     */
    protected function handleExportException(Exception $e)
    {
        $type = get_class($e);

        if ($e instanceof FileWritingException) {
            return $this->handleApplicationExportException($e);
        }

        if ($e instanceof SqlRequestException) {
            return $this->handleDomainExportException($e);
        }

        return $this->getFallbackErrorMessage($type, $e->getCode());
    }

    /**
     * @param FileWritingException $e
     *
     * @return string Error message
     */
    protected function handleApplicationExportException(FileWritingException $e)
    {
        $code = $e->getCode();

        $applicationErrors = [
            FileWritingException::CANNOT_OPEN_FILE_FOR_WRITING => $this->trans('Cannot open export file for writing', 'Admin.Notifications.Error'),
        ];

        if (isset($applicationErrors[$code])) {
            return $applicationErrors[$code];
        }

        return $this->getFallbackErrorMessage(get_class($e), $code);
    }

    /**
     * @param SqlRequestException $e
     *
     * @return string
     */
    protected function handleDomainExportException(SqlRequestException $e)
    {
        $type = get_class($e);

        $domainErrors = [
            SqlRequestNotFoundException::class => $this->trans('The object cannot be loaded (or found)', 'Admin.Notifications.Error'),
        ];

        if (isset($domainErrors[$type])) {
            return $domainErrors[$type];
        }

        return $this->getFallbackErrorMessage($type, $e->getCode());
    }

    /**
     * @return string[] Array of database tables
     */
    protected function getDatabaseTables()
    {
        /** @var DatabaseTablesList $databaseTablesList */
        $databaseTablesList = $this->getQueryBus()->handle(new GetDatabaseTablesList());

        return $databaseTablesList->getTables();
    }
}
