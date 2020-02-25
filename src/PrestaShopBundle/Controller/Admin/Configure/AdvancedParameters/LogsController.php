<?php
/**
 * 2007-2019 PrestaShop SA and Contributors
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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShopBundle\Controller\Admin\Configure\AdvancedParameters;

use PrestaShop\PrestaShop\Core\Form\FormHandlerInterface;
use PrestaShop\PrestaShop\Core\Search\Filters\LogsFilters;
use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use PrestaShopBundle\Entity\Repository\LogRepository;
use PrestaShopBundle\Security\Annotation\AdminSecurity;
use PrestaShopBundle\Security\Annotation\DemoRestricted;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Responsible of "Configure > Advanced Parameters > Logs" page display.
 */
class LogsController extends FrameworkBundleAdminController
{
    /**
     * @var string the controller name for routing
     */
    const CONTROLLER_NAME = 'AdminLogs';

    /**
     * @AdminSecurity("is_granted('read', request.get('_legacy_controller'))", message="Access denied.")
     *
     * @param LogsFilters $filters the list of filters from the request
     *
     * @return Response
     */
    public function indexAction(LogsFilters $filters)
    {
        $gridLogFactory = $this->get('prestashop.core.grid.log_factory');
        $grid = $gridLogFactory->getGrid($filters);

        $logsByEmailForm = $this->getFormHandler()->getForm();

        return $this->render('@PrestaShop/Admin/Configure/AdvancedParameters/LogsPage/index.html.twig', [
            'layoutHeaderToolbarBtn' => [],
            'layoutTitle' => $this->trans('Logs', 'Admin.Navigation.Menu'),
            'requireAddonsSearch' => true,
            'requireBulkActions' => false,
            'showContentHeader' => true,
            'enableSidebar' => true,
            'help_link' => $this->generateSidebarLink('AdminLogs'),
            'logsByEmailForm' => $logsByEmailForm->createView(),
            'grid' => $this->presentGrid($grid),
        ]);
    }

    /**
     * @AdminSecurity("is_granted(['read', 'update', 'create', 'delete'], request.get('_legacy_controller'))", message="You do not have permission to update this.", redirectRoute="admin_logs_index")
     * @DemoRestricted(redirectRoute="admin_logs_index")
     *
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function searchAction(Request $request)
    {
        $definitionFactory = $this->get('prestashop.core.grid.definition.factory.logs');
        $logsDefinition = $definitionFactory->getDefinition();

        $gridFilterFormFactory = $this->get('prestashop.core.grid.filter.form_factory');
        $searchParametersForm = $gridFilterFormFactory->create($logsDefinition);

        $searchParametersForm->handleRequest($request);
        $filters = array();

        $this->dispatchHook('actionAdminLogsControllerPostProcessBefore', ['controller' => $this]);

        if ($searchParametersForm->isSubmitted()) {
            $filters = $searchParametersForm->getData();
        }

        return $this->redirectToRoute('admin_logs_index', ['filters' => $filters]);
    }

    /**
     * @AdminSecurity("is_granted(['update', 'create','delete'], request.get('_legacy_controller'))", message="You do not have permission to update this.", redirectRoute="admin_logs_index")
     * @DemoRestricted(redirectRoute="admin_logs_index")
     *
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function saveSettingsAction(Request $request)
    {
        $logsByEmailForm = $this->getFormHandler()->getForm();
        $logsByEmailForm->handleRequest($request);

        $this->dispatchHook('actionAdminLogsControllerPostProcessBefore', array('controller' => $this));

        if ($logsByEmailForm->isSubmitted()) {
            $data = $logsByEmailForm->getData();

            $saveErrors = $this->getFormHandler()->save($data);

            if (0 === count($saveErrors)) {
                $this->addFlash('success', $this->trans('Successful update.', 'Admin.Notifications.Success'));

                return $this->redirectToRoute('admin_logs_index');
            }

            $this->flashErrors($saveErrors);
        }

        return $this->redirectToRoute('admin_logs_index');
    }

    /**
     * @AdminSecurity("is_granted('delete', request.get('_legacy_controller'))", message="You do not have permission to update this.", redirectRoute="admin_logs_index")
     *
     * @return RedirectResponse
     *
     * @throws \Doctrine\DBAL\Exception\InvalidArgumentException
     */
    public function deleteAllAction()
    {
        $this->getLogRepository()->deleteAll();

        $this->addFlash('success', $this->trans('Successful update.', 'Admin.Notifications.Success'));

        return $this->redirectToRoute('admin_logs_index');
    }

    /**
     * @return FormHandlerInterface the form handler to set the severity level
     */
    private function getFormHandler()
    {
        return $this->get('prestashop.adapter.logs.form_handler');
    }

    /**
     * @return LogRepository the repository to retrieve logs from database
     */
    private function getLogRepository()
    {
        return $this->get('prestashop.core.admin.log.repository');
    }
}
