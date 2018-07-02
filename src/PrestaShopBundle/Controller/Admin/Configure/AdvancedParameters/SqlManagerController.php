<?php
/**
 * 2007-2017 PrestaShop
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

use PrestaShop\PrestaShop\Core\Form\FormHandlerInterface;
use PrestaShop\PrestaShop\Core\Grid\Search\TemporarySearchCriteria;
use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use PrestaShopBundle\Security\Annotation\AdminSecurity;
use PrestaShopBundle\Security\Annotation\DemoRestricted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Responsible of "Configure > Advanced Parameters > Database -> SQL Manager" page
 */
class SqlManagerController extends FrameworkBundleAdminController
{
    /**
     * Show list of saved SQL's
     *
     * @AdminSecurity(
     *     "is_granted(['read','update', 'create','delete'], request.get('_legacy_controller')~'_')",
     *      message="Access denied."
     * )
     *
     * @param Request $request
     *
     * @return array|Response
     */
    public function listAction(Request $request)
    {
        // handle "Export to SQL manager" action from legacy pagees
        if ($request->query->has('addrequest_sql')) {
           return $this->forward('PrestaShopBundle:Admin\Configure\AdvancedParameters\SqlManager:create');
        }

        $legacyController = $request->attributes->get('_legacy_controller');

        // temporary search criteria class, to be removed
        $searchCriteria = new TemporarySearchCriteria($request);

        $gridLogFactory = $this->get('prestashop.core.grid.factory.request_sql');
        $grid = $gridLogFactory->createUsingSearchCriteria($searchCriteria);

        $gridPresenter = $this->get('prestashop.core.grid.presenter.grid_presenter');
        $presentedGrid = $gridPresenter->present($grid);

        $settingsForm = $this->getSettingsFormHandler()->getForm();

        return $this->render('@PrestaShop/Admin/Configure/AdvancedParameters/SqlManager/list.html.twig', [
            'layoutHeaderToolbarBtn' => [
                'add' => [
                    'href' => $this->generateUrl('admin_sql_manager_create'),
                    'desc' => $this->trans('Add new SQL query', 'Admin.Advparameters.Feature'),
                    'icon' => 'add_circle_outline',
                ],
            ],
            'layoutTitle' => $this->trans('SQL Manager', 'Admin.Navigation.Menu'),
            'requireAddonsSearch' => true,
            'enableSidebar' => true,
            'help_link' => $this->generateSidebarLink($legacyController),
            'sqlManagerSettingsForm' => $settingsForm->createView(),
            'requestSqlGrid' => $presentedGrid,
        ]);
    }

    /**
     * Process Request SQL settings save
     *
     * @DemoRestricted(redirectRoute="admin_sql_manager")
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
            $data = $settingForm->getData();

            if (!$errors = $handler->save($data)) {
                $this->addFlash('success', $this->trans('Successful update.', 'Admin.Notifications.Success'));

                return $this->redirectToRoute('admin_sql_manager');
            }

            $this->flashErrors($errors);
        }

        return $this->redirectToRoute('admin_sql_manager');
    }

    /**
     * Show Request SQL create page
     *
     * @AdminSecurity(
     *     "is_granted(['create'], request.get('_legacy_controller')~'_')",
     *      message="You do not have permission to create this."
     * )
     *
     * @Template("@PrestaShop/Admin/Configure/AdvancedParameters/SqlManager/form.html.twig")
     *
     * @param Request $request
     *
     * @return array|RedirectResponse
     */
    public function createAction(Request $request)
    {
        $formHandler = $this->get('prestashop.admin.request_sql.form_handler');
        $dataProvider = $this->get('prestashop.adapter.sql_manager.request_sql_data_provider');

        $requestSqlForm = $formHandler->getForm();
        if ($request->request->has('sql')) {
            $requestSqlForm->setData([
                'request_sql' => [
                    'sql' => $request->request->get('sql'),
                    'name' => $request->request->get('name'),
                ],
            ]);
        }
        $requestSqlForm->handleRequest($request);

        if ($requestSqlForm->isSubmitted()) {
            if ($this->isDemoModeEnabled()) {
                $this->addFlash('error', $this->getDemoModeErrorMessage());

                return $this->redirectToRoute('admin_sql_manager');
            }

            $requestSqlData = $requestSqlForm->getData();

            if (!$errors = $formHandler->save($requestSqlData)) {
                $this->addFlash('success', $this->trans('Successful creation.', 'Admin.Notifications.Success'));

                return $this->redirectToRoute('admin_sql_manager');
            }

            foreach ($errors as $error) {
                $this->addFlash('error', $error);
            }
        }

        $templateParams = [
            'requestSqlForm' => $requestSqlForm->createView(),
            'dbTableNames' => $dataProvider->getTables(),
        ];

        return array_merge(
            $this->getTemplateParams($request, false),
            $templateParams
        );
    }

    /**
     * Show Request SQL edit page
     *
     * @AdminSecurity(
     *     "is_granted(['update'], request.get('_legacy_controller')~'_')",
     *     message="You do not have permission to edit this."
     * )
     *
     * @Template("@PrestaShop/Admin/Configure/AdvancedParameters/SqlManager/form.html.twig")
     *
     * @param int     $requestSqlId
     * @param Request $request
     *
     * @return array|RedirectResponse
     */
    public function editAction($requestSqlId, Request $request)
    {
        $formHandler = $this->get('prestashop.admin.request_sql.form_handler');
        $dataProvider = $this->get('prestashop.adapter.sql_manager.request_sql_data_provider');

        $requestSql = $dataProvider->getRequestSql($requestSqlId);
        if (!$requestSql) {
            $this->addFlash('error', $this->trans('The object cannot be loaded (or found)', 'Admin.Notifications.Error'));

            return $this->redirectToRoute('admin_sql_manager');
        }

        $requestSqlForm = $formHandler->getForm();
        $requestSqlForm->setData(['request_sql' => $requestSql]);
        $requestSqlForm->handleRequest($request);

        if ($requestSqlForm->isSubmitted()) {
            if ($this->isDemoModeEnabled()) {
                $this->addFlash('error', $this->getDemoModeErrorMessage());

                return $this->redirectToRoute('admin_sql_manager');
            }

            $data = $requestSqlForm->getData();

            if (!$errors = $formHandler->save($data)) {
                $this->addFlash('success', $this->trans('Successful update.', 'Admin.Notifications.Success'));

                return $this->redirectToRoute('admin_sql_manager');
            }

            foreach ($errors as $error) {
                $this->addFlash('error', $error);
            }
        }

        $templateParams = [
            'requestSqlForm' => $requestSqlForm->createView(),
            'dbTableNames' => $dataProvider->getTables(),
        ];

        return array_merge(
            $this->getTemplateParams($request),
            $templateParams
        );
    }

    /**
     * Delete selected Request SQL
     *
     * @AdminSecurity(
     *     "is_granted(['delete'], request.get('_legacy_controller')~'_')",
     *     message="You do not have permission to edit this."
     * )
     * @DemoRestricted(redirectRoute="admin_sql_manager")
     *
     * @param int $requestSqlId ID of selected Request SQL
     *
     * @return RedirectResponse
     */
    public function deleteAction($requestSqlId)
    {
        $requestSqlDataProvider = $this->get('prestashop.adapter.sql_manager.request_sql_data_provider');
        if (!$requestSql = $requestSqlDataProvider->getRequestSql($requestSqlId)) {
            $this->addFlash('error', $this->trans('The object cannot be loaded (or found)', 'Admin.Notifications.Error'));

            return $this->redirectToRoute('admin_sql_manager');
        }

        $requestSqlManager = $this->get('prestashop.adapter.sql_manager.request_sql_manager');
        if (!$requestSqlManager->delete($requestSqlId)) {
            $this->addFlash('error', $this->trans('An error occurred while deleting the object.', 'Admin.Notifications.Error'));

            return $this->redirectToRoute('admin_sql_manager');
        }

        $this->addFlash('success', $this->trans('Successful deletion', 'Admin.Notifications.Success'));

        return $this->redirectToRoute('admin_sql_manager');
    }

    /**
     * View Request SQL query data
     *
     * @AdminSecurity(
     *     "is_granted(['read'], request.get('_legacy_controller')~'_')",
     *     message="You do not have permission to view this."
     * )
     *
     * @Template("@PrestaShop/Admin/Configure/AdvancedParameters/SqlManager/view.html.twig")
     *
     * @param Request $request
     * @param int     $requestSqlId
     *
     * @return array|RedirectResponse
     */
    public function viewAction(Request $request, $requestSqlId)
    {
        $requestSqlDataProvider = $this->get('prestashop.adapter.sql_manager.request_sql_data_provider');
        $requestSqlResult = $requestSqlDataProvider->getRequestSqlResult($requestSqlId);

        if (null === $requestSqlResult) {
            $this->addFlash('error', $this->trans('The object cannot be loaded (or found)', 'Admin.Notifications.Error'));

            return $this->redirectToRoute('admin_sql_manager');
        }

        $templateParams = [
            'requestSqlResult' => $requestSqlResult,
        ];

        return array_merge(
            $this->getTemplateParams($request, false),
            $templateParams
        );
    }

    /**
     * Export Request SQL data
     *
     * @AdminSecurity(
     *     "is_granted(['read'], request.get('_legacy_controller')~'_')",
     *     message="Access denied."
     * )
     * @DemoRestricted(redirectRoute="admin_sql_manager")
     *
     * @param int $requestSqlId Request SQL id
     *
     * @return RedirectResponse|BinaryFileResponse
     */
    public function exportAction($requestSqlId)
    {
        $requestSqlExporter = $this->get('prestashop.adapter.sql_manager.request_sql_exporter');
        $response = $requestSqlExporter->export($requestSqlId);

        if (null === $response) {
            $this->addFlash('error', $this->trans('The object cannot be loaded (or found)', 'Admin.Notifications.Error'));

            return $this->redirectToRoute('admin_sql_manager');
        }

        return $response;
    }

    /**
     * Get MySQL table columns data
     *
     * @param string $mySqlTableName Database tabe name
     *
     * @return JsonResponse
     */
    public function tableColumnsAction($mySqlTableName)
    {
        $dataProvider = $this->get('prestashop.adapter.sql_manager.request_sql_data_provider');

        $columns = [];

        $tableColumns = $dataProvider->getTableColumns($mySqlTableName);
        foreach ($tableColumns as $tableColumn) {
            $columns[] = [
                'name' => $tableColumn['Field'],
                'type' => $tableColumn['Type'],
            ];
        }

        return $this->json(['columns' => $columns]);
    }

    /**
     * Get request sql repository
     *
     * @return \PrestaShopBundle\Entity\Repository\RequestSqlRepository
     */
    protected function getRepository()
    {
        return $this->get('prestashop.core.admin.request_sql.repository');
    }

    /**
     * Get Request SQL settings form handler
     *
     * @return FormHandlerInterface
     */
    protected function getSettingsFormHandler()
    {
        return $this->get('prestashop.admin.sql_manager_settings.form_handler');
    }

    /**
     * Get required template parameters needed for all responses that renders content
     *
     * @param Request $request
     * @param bool    $withHeaderBtn
     *
     * @return array
     */
    protected function getTemplateParams(Request $request, $withHeaderBtn = true)
    {
        $legacyController = $request->attributes->get('_legacy_controller');

        $params = [
            'layoutHeaderToolbarBtn' => [],
            'layoutTitle' => $this->trans('SQL Manager', 'Admin.Navigation.Menu'),
            'requireAddonsSearch' => true,
            'enableSidebar' => true,
            'help_link' => $this->generateSidebarLink($legacyController),
        ];

        if ($withHeaderBtn) {
            $params['layoutHeaderToolbarBtn']['add'] = [
                'href' => $this->generateUrl('admin_sql_manager_create'),
                'desc' => $this->trans('Add new SQL query', 'Admin.Advparameters.Feature'),
                'icon' => 'add_circle_outline',
            ];
        }

        return $params;
    }
}
