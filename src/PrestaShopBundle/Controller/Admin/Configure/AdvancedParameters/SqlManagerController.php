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
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
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

        $form = $formHandler->getForm();
        $form->setData([
            'request_sql' => $dataProvider->getRequestSql($id),
        ]);
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
        ];

        return $this->getTemplateParams($request) + $params;
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
                'desc' => $this->trans('New SQL query', 'Admin.Actions'),
                'icon' => 'add_circle_outline',
            ];
        }

        return $params;
    }
}
