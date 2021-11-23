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

namespace PrestaShopBundle\Controller\Admin\Configure\AdvancedParameters;

use Exception;
use PrestaShop\PrestaShop\Core\Domain\SqlManagement\Command\BulkDeleteSqlRequestCommand;
use PrestaShop\PrestaShop\Core\Domain\SqlManagement\Command\DeleteSqlRequestCommand;
use PrestaShop\PrestaShop\Core\Domain\SqlManagement\DatabaseTableFields;
use PrestaShop\PrestaShop\Core\Domain\SqlManagement\DatabaseTablesList;
use PrestaShop\PrestaShop\Core\Domain\SqlManagement\Exception\CannotDeleteSqlRequestException;
use PrestaShop\PrestaShop\Core\Domain\SqlManagement\Exception\SqlRequestConstraintException;
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
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\Builder\FormBuilderInterface;
use PrestaShop\PrestaShop\Core\Search\Filters\RequestSqlFilters;
use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use PrestaShopBundle\Security\Annotation\AdminSecurity;
use PrestaShopBundle\Security\Annotation\DemoRestricted;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\File\Stream;
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
     * @AdminSecurity("is_granted('read', request.get('_legacy_controller'))")
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

        $settingsForm = $this->getSettingsFormHandler()->getForm();

        return $this->render('@PrestaShop/Admin/Configure/AdvancedParameters/RequestSql/index.html.twig', [
            'layoutHeaderToolbarBtn' => [
                'add' => [
                    'href' => $this->generateUrl('admin_sql_requests_create'),
                    'desc' => $this->trans('Add new SQL query', 'Admin.Advparameters.Feature'),
                    'icon' => 'add_circle_outline',
                ],
            ],
            'layoutTitle' => $this->trans('SQL Manager', 'Admin.Navigation.Menu'),
            'enableSidebar' => true,
            'help_link' => $this->generateSidebarLink($request->attributes->get('_legacy_controller')),
            'requestSqlSettingsForm' => $settingsForm->createView(),
            'requestSqlGrid' => $this->presentGrid($grid),
        ]);
    }

    /**
     * @deprecated since 1.7.8 and will be removed in next major. Use CommonController:searchGridAction instead
     *
     * @AdminSecurity("is_granted('read', request.get('_legacy_controller'))", redirectRoute="admin_sql_requests_index")
     * @DemoRestricted(redirectRoute="admin_sql_requests_index")
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

        return $this->redirectToRoute('admin_sql_requests_index', ['filters' => $filters]);
    }

    /**
     * Process Request SQL settings save.
     *
     * @DemoRestricted(redirectRoute="admin_sql_requests_index")
     * @AdminSecurity(
     *     "is_granted('update', request.get('_legacy_controller')) && is_granted('create', request.get('_legacy_controller')) && is_granted('delete', request.get('_legacy_controller'))",
     *      redirectRoute="admin_sql_requests_index"
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

        return $this->redirectToRoute('admin_sql_requests_index');
    }

    /**
     * Show Request SQL create page.
     *
     * @AdminSecurity(
     *     "is_granted('create', request.get('_legacy_controller'))",
     *      message="You do not have permission to create this.",
     *      redirectRoute="admin_sql_requests_index"
     * )
     *
     * @param Request $request
     *
     * @return Response
     */
    public function createAction(Request $request)
    {
        $data = $this->getSqlRequestDataFromRequest($request);

        $sqlRequestForm = $this->getSqlRequestFormBuilder()->getForm($data);
        $sqlRequestForm->handleRequest($request);

        try {
            $result = $this->getSqlRequestFormHandler()->handle($sqlRequestForm);

            if (null !== $result->getIdentifiableObjectId()) {
                $this->addFlash('success', $this->trans('Successful creation.', 'Admin.Notifications.Success'));

                return $this->redirectToRoute('admin_sql_requests_index');
            }
        } catch (SqlRequestException $e) {
            $this->addFlash('error', $this->handleException($e));
        }

        return $this->render('@PrestaShop/Admin/Configure/AdvancedParameters/RequestSql/create.html.twig', [
            'layoutTitle' => $this->trans('SQL Manager', 'Admin.Navigation.Menu'),
            'enableSidebar' => true,
            'help_link' => $this->generateSidebarLink($request->attributes->get('_legacy_controller')),
            'requestSqlForm' => $sqlRequestForm->createView(),
            'dbTableNames' => $this->getDatabaseTables(),
            'multistoreInfoTip' => $this->trans(
                'Note that this feature is available in all shops context only. It will be added to all your stores.',
                'Admin.Notifications.Info'
            ),
            'multistoreIsUsed' => $this->get('prestashop.adapter.multistore_feature')->isUsed(),
        ]);
    }

    /**
     * Show Request SQL edit page.
     *
     * @DemoRestricted(redirectRoute="admin_sql_requests_index")
     * @AdminSecurity(
     *     "is_granted('update', request.get('_legacy_controller'))",
     *     message="You do not have permission to edit this.",
     *     redirectRoute="admin_sql_requests_index"
     * )
     *
     * @param int $sqlRequestId
     * @param Request $request
     *
     * @return Response
     */
    public function editAction($sqlRequestId, Request $request)
    {
        $sqlRequestForm = $this->getSqlRequestFormBuilder()->getFormFor($sqlRequestId);
        $sqlRequestForm->handleRequest($request);

        try {
            $result = $this->getSqlRequestFormHandler()->handleFor($sqlRequestId, $sqlRequestForm);

            if ($result->isSubmitted() && $result->isValid()) {
                $this->addFlash('success', $this->trans('Successful update.', 'Admin.Notifications.Success'));

                return $this->redirectToRoute('admin_sql_requests_index');
            }
        } catch (SqlRequestNotFoundException $e) {
            $this->addFlash(
                'error',
                $this->trans('The object cannot be loaded (or found)', 'Admin.Notifications.Error')
            );

            return $this->redirectToRoute('admin_sql_requests_index');
        } catch (SqlRequestException $e) {
            $this->addFlash('error', $this->handleException($e));
        }

        return $this->render('@PrestaShop/Admin/Configure/AdvancedParameters/RequestSql/edit.html.twig', [
            'layoutTitle' => $this->trans('SQL Manager', 'Admin.Navigation.Menu'),
            'enableSidebar' => true,
            'help_link' => $this->generateSidebarLink($request->attributes->get('_legacy_controller')),
            'requestSqlForm' => $sqlRequestForm->createView(),
            'dbTableNames' => $this->getDatabaseTables(),
        ]);
    }

    /**
     * Delete selected Request SQL.
     *
     * @AdminSecurity(
     *     "is_granted('delete', request.get('_legacy_controller'))",
     *     message="You do not have permission to delete this.",
     *     redirectRoute="admin_sql_requests_index"
     * )
     * @DemoRestricted(redirectRoute="admin_sql_requests_index")
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
            $this->addFlash('error', $this->handleException($e));
        }

        return $this->redirectToRoute('admin_sql_requests_index');
    }

    /**
     * Process bulk action delete of RequestSql's.
     *
     * @AdminSecurity(
     *     "is_granted('delete', request.get('_legacy_controller'))",
     *     message="You do not have permission to delete this.",
     *     redirectRoute="admin_sql_requests_index"
     * )
     * @DemoRestricted(redirectRoute="admin_sql_requests_index")
     *
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function deleteBulkAction(Request $request)
    {
        try {
            $requestSqlIds = $request->request->get('sql_request_bulk');
            $bulkDeleteSqlRequestCommand = new BulkDeleteSqlRequestCommand($requestSqlIds);

            $this->getCommandBus()->handle($bulkDeleteSqlRequestCommand);

            $this->addFlash(
                'success',
                $this->trans('The selection has been successfully deleted.', 'Admin.Notifications.Success')
            );
        } catch (SqlRequestException $e) {
            $this->addFlash('error', $this->handleException($e));
        }

        return $this->redirectToRoute('admin_sql_requests_index');
    }

    /**
     * View Request SQL query data.
     *
     * @AdminSecurity(
     *     "is_granted('read', request.get('_legacy_controller'))",
     *     message="You do not have permission to view this.",
     *     redirectRoute="admin_sql_requests_index"
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

            return $this->redirectToRoute('admin_sql_requests_index');
        }

        return $this->render('@PrestaShop/Admin/Configure/AdvancedParameters/RequestSql/view.html.twig', [
            'layoutHeaderToolbarBtn' => [],
            'layoutTitle' => $this->trans('SQL Manager', 'Admin.Navigation.Menu'),
            'enableSidebar' => true,
            'help_link' => $this->generateSidebarLink($request->attributes->get('_legacy_controller')),
            'sqlRequestResult' => $sqlRequestExecutionResult,
        ]);
    }

    /**
     * Export Request SQL data.
     *
     * @AdminSecurity(
     *     "is_granted('read', request.get('_legacy_controller'))",
     *     redirectRoute="admin_sql_requests_index"
     * )
     * @DemoRestricted(redirectRoute="admin_sql_requests_index")
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

            return $this->redirectToRoute('admin_sql_requests_index');
        }

        $stream = new Stream($exportedFile->getPathname());
        $response = new BinaryFileResponse($stream);
        $response->setCharset($sqlRequestSettings->getFileEncoding());
        $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, $exportedFile->getFilename());

        return $response;
    }

    /**
     * Get MySQL table columns data.
     *
     * @AdminSecurity(
     *     "is_granted('read', request.get('_legacy_controller'))",
     *     redirectRoute="admin_sql_requests_index"
     * )
     *
     * @param string $mySqlTableName Database table name
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
     * Get request SQL repository.
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
    protected function getSettingsFormHandler(): FormHandlerInterface
    {
        return $this->get('prestashop.admin.request_sql_settings.form_handler');
    }

    /**
     * @return \PrestaShop\PrestaShop\Core\Form\IdentifiableObject\Handler\FormHandlerInterface
     */
    protected function getSqlRequestFormHandler()
    {
        return $this->get('prestashop.core.form.identifiable_object.sql_request_form_handler');
    }

    /**
     * @return FormBuilderInterface
     */
    protected function getSqlRequestFormBuilder()
    {
        return $this->get('prestashop.core.form.builder.sql_request_form_builder');
    }

    /**
     * When "Export to SQL Manager" feature is used,
     * it adds "name" and "sql" to request's POST data
     * which is used as default form data
     * when creating SqlRequest.
     *
     * @param Request $request
     *
     * @return array
     */
    protected function getSqlRequestDataFromRequest(Request $request)
    {
        if ($request->request->has('sql') || $request->request->has('name')) {
            return [
                'sql' => $request->request->get('sql'),
                'name' => $request->request->get('name'),
            ];
        }

        return [];
    }

    /**
     * Get human readable error for exception.
     *
     * @param SqlRequestException $e
     *
     * @return string Error message
     */
    protected function handleException(SqlRequestException $e)
    {
        $code = $e->getCode();
        $type = get_class($e);

        $exceptionMessages = [
            SqlRequestNotFoundException::class => $this->trans('The object cannot be loaded (or found)', 'Admin.Notifications.Error'),
            SqlRequestConstraintException::class => $e->getMessage(),
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
