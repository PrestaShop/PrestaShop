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
use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use PrestaShopBundle\Form\Admin\Configure\AdvancedParameters\SqlManager\FilterRequestSqlType;
use PrestaShopBundle\Security\Voter\PageVoter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Responsible of "Configure > Advanced Parameters > Database -> SQL Manager" page
 */
class SqlManagerController extends FrameworkBundleAdminController
{
    /**
     * Show list of saved SQL's
     *
     * @Template("@PrestaShop/Admin/Configure/AdvancedParameters/SqlManager/list.html.twig")
     *
     * @param Request $request
     *
     * @return array
     */
    public function listAction(Request $request)
    {
        $searchForm = $this->createForm(FilterRequestSqlType::class, []);
        $searchForm->handleRequest($request);

        $filters = $this->get('prestashop.core.admin.search_parameters')->getFiltersFromRequest($request, [
            'limit' => 10,
            'offset' => 0,
            'orderBy' => 'id_request_sql',
            'sortOrder' => 'desc',
            'filters' => $searchForm->getData(),
        ]);

        $repository = $this->getRepository();
        $requestSqls = $repository->findByFilters($filters);
        $requestSqlsCount = $repository->getCount();

        $settingsForm = $this->getSettingsFormHandler()->getForm();

        $data = [
            'request_sqls' => $requestSqls,
            'request_sqls_count' => $requestSqlsCount,
            'order_by' => $filters['orderBy'],
            'order_way' => $filters['sortOrder'],
            'filters' => $filters,
            'search_form' => $searchForm->createView(),
            'settings_form' => $settingsForm->createView(),
        ];

        return $this->getTemplateParams($request) + $data;
    }

    /**
     * Process Request SQL settings save
     *
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function settingsAction(Request $request)
    {
        $handler = $this->getSettingsFormHandler();
        $form = $handler->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $data = $form->getData();

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

        $form = $formHandler->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $data = $form->getData();

            if (!$errors = $formHandler->save($data)) {
                $this->addFlash('success', $this->trans('Successful creation.', 'Admin.Notifications.Success'));

                return $this->redirectToRoute('admin_sql_manager');
            }

            foreach ($errors as $error) {
                $this->addFlash('error', $error);
            }
        }

        $params = [
            'requestSqlForm' => $form->createView(),
            'dbTableNames' => $dataProvider->getTables(),
        ];

        return $this->getTemplateParams($request, false) + $params;
    }

    /**
     * Show Request SQL edit page
     *
     * @Template("@PrestaShop/Admin/Configure/AdvancedParameters/SqlManager/form.html.twig")
     *
     * @param int $id
     * @param Request $request
     *
     * @return array|RedirectResponse
     */
    public function editAction($id, Request $request)
    {
        $formHandler = $this->get('prestashop.admin.request_sql.form_handler');
        $dataProvider = $this->get('prestashop.adapter.sql_manager.request_sql_data_provider');

        $requestSql = $dataProvider->getRequestSql($id);
        if (!$requestSql) {
            $this->addFlash('error', $this->trans('The object cannot be loaded (or found)', 'Admin.Notifications.Error'));

            return $this->redirectToRoute('admin_sql_manager');
        }

        $form = $formHandler->getForm();
        $form->setData(['request_sql' => $requestSql]);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $data = $form->getData();

            if (!$errors = $formHandler->save($data)) {
                $this->addFlash('success', $this->trans('Successful update.', 'Admin.Notifications.Success'));

                return $this->redirectToRoute('admin_sql_manager');
            }

            foreach ($errors as $error) {
                $this->addFlash('error', $error);
            }
        }

        $params = [
            'requestSqlForm' => $form->createView(),
            'dbTableNames' => $dataProvider->getTables(),
        ];

        return $this->getTemplateParams($request) + $params;
    }

    /**
     * Delete selected Request SQL
     *
     * @param int $id           ID of selected Request SQL
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function deleteAction($id, Request $request)
    {
        $legacyController = $request->attributes->get('_legacy_controller');

        if ($this->isDemoModeEnabled()) {
            $this->addFlash('error', $this->getDemoModeErrorMessage());

            return $this->redirectToRoute('admin_sql_manager');
        }

        if ($this->authorizationLevel($legacyController) != PageVoter::LEVEL_DELETE) {
            $this->addFlash('error', $this->trans('You do not have permission to delete this.', 'Admin.Notifications.Error'));

            return $this->redirectToRoute('admin_sql_manager');
        }

        $requestSqlDataProvider = $this->get('prestashop.adapter.sql_manager.request_sql_data_provider');
        if (!$requestSql = $requestSqlDataProvider->getRequestSql($id)) {
            $this->addFlash('error', $this->trans('The object cannot be loaded (or found)', 'Admin.Notifications.Error'));

            return $this->redirectToRoute('admin_sql_manager');
        }

        $requestSqlManager = $this->get('prestashop.adapter.sql_manager.request_sql_manager');
        if (!$requestSqlManager->delete($id)) {
            $this->addFlash('error', $this->trans('An error occurred while deleting the object.', 'Admin.Notifications.Error'));

            return $this->redirectToRoute('admin_sql_manager');
        }

        $this->addFlash('success', $this->trans('Successful deletion', 'Admin.Notifications.Success'));

        return $this->redirectToRoute('admin_sql_manager');
    }

    /**
     * View Request SQL query data
     *
     * @Template("@PrestaShop/Admin/Configure/AdvancedParameters/SqlManager/view.html.twig")
     *
     * @param Request $request
     * @param int $id
     *
     * @return array|RedirectResponse
     */
    public function viewAction(Request $request, $id)
    {
        $requestSqlDataProvider = $this->get('prestashop.adapter.sql_manager.request_sql_data_provider');
        $result = $requestSqlDataProvider->getRequestSqlResult($id);

        if (null === $result) {
            $this->addFlash('error', $this->trans('The object cannot be loaded (or found)', 'Admin.Notifications.Error'));

            return $this->redirectToRoute('admin_sql_manager');
        }

        $params = [
            'requestSqlView' => $result,
        ];

        return $this->getTemplateParams($request, false) + $params;
    }

    /**
     * Export Request SQL data
     *
     * @param int $id   Request SQL id
     *
     * @return RedirectResponse
     */
    public function exportAction($id)
    {
        $requestSqlExporter = $this->get('prestashop.adapter.sql_manager.request_sql_exporter');
        $response = $requestSqlExporter->export($id);

        if (null === $response) {
            $this->addFlash('error', $this->trans('The object cannot be loaded (or found)', 'Admin.Notifications.Error'));

            return $this->redirectToRoute('admin_sql_manager');
        }

        return $response;
    }

    /**
     * Get table columns data
     *
     * @param string $table     Database tabe name
     *
     * @return JsonResponse
     */
    public function tableColumnsAction($table)
    {
        $dataProvider = $this->get('prestashop.adapter.sql_manager.request_sql_data_provider');

        $columns = [];

        $tableColumns = $dataProvider->getTableColumns($table);
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
     * @param bool $withHeaderBtn
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
