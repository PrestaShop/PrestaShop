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

use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use PrestaShopBundle\Form\Admin\Configure\RequestSql\FilterRequestSqlType;
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

        $settingsForm = $this->getSettingsFromHandler()->getForm();

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

        $data = [
            'request_sqls' => $requestSqls,
            'request_sqls_count' => $requestSqlsCount,
            'order_by' => $filters['orderBy'],
            'order_way' => $filters['sortOrder'],
            'search_form' => $searchForm->createView(),
            'settings_form' => $settingsForm->createView(),
        ];

        return $this->getTemplateParams($request, true) + $data;
    }

    /**
     * Process Request SQL settings save
     *
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function processSettingsAction(Request $request)
    {
        $handler = $this->getSettingsFromHandler();
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
     */
    public function createAction(Request $request)
    {

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
     * @return \PrestaShopBundle\Form\Admin\Configure\RequestSql\RequestSqlSettingsFormHandler
     */
    protected function getSettingsFromHandler()
    {
        return $this->get('prestashop.admin.request_sql_settings.form_handler');
    }

    /**
     * @param Request $request
     * @param bool $withHeaderBtn
     *
     * @return array
     */
    protected function getTemplateParams(Request $request, $withHeaderBtn = false)
    {
        $legacyController = $request->attributes->get('_legacy_controller');

        $params = [
            'layoutTitle' => $this->trans('SQL Manager', 'Admin.Navigation.Menu'),
            'requireAddonsSearch' => true,
            'enableSidebar' => true,
            'help_link' => $this->generateSidebarLink($legacyController),
        ];

        if ($withHeaderBtn) {
            $params['layoutHeaderToolbarBtn'] = [
                'add' => [
                    'href' => $this->generateUrl('admin_sql_manager_create'),
                    'desc' => $this->trans('New SQL query', 'Admin.Actions'),
                    'icon' => 'add_circle_outline',
                ],
            ];
        }

        return $params;
    }
}
