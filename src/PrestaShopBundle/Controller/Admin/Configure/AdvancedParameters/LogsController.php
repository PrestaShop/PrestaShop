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
use PrestaShopBundle\Form\Admin\Configure\AdvancedParameters\Logs\FilterLogsByAttributeType;
use PrestaShopBundle\Entity\Repository\LogRepository;
use Symfony\Component\HttpFoundation\RedirectResponse;
use PrestaShopBundle\Security\Voter\PageVoter;
use Symfony\Component\HttpFoundation\Request;

/**
 * Responsible of "Configure > Advanced Parameters > Logs" page display
 */
class LogsController extends FrameworkBundleAdminController
{
    /**
     * @var string The controller name for routing.
     */
    const CONTROLLER_NAME = 'AdminLogs';

    /**
     * @param \Symfony\Component\HttpFoundation\Request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request)
    {
        $searchParametersForm = $this->createForm(FilterLogsByAttributeType::class, $request->get('filters', array()));
        $logsByEmailForm = $this->getFormHandler()->getForm();

        $filters = $this->get('prestashop.core.admin.search_parameters')->getFiltersFromRequest($request, array(
            'limit' => 10,
            'offset' => 0,
            'orderBy' => 'id_log',
            'sortOrder' => 'desc',
            'filters' => array()
        ));

        $twigValues = array(
            'layoutHeaderToolbarBtn' => [],
            'layoutTitle' => $this->get('translator')->trans('Logs', array(), 'Admin.Navigation.Menu'),
            'requireAddonsSearch' => true,
            'requireBulkActions' => false,
            'showContentHeader' => true,
            'enableSidebar' => true,
            'orderBy' => $filters['orderBy'],
            'sortOrder' => $filters['sortOrder'],
            'help_link' => $this->generateSidebarLink('AdminLogs'),
            'logsByEmailForm' => $logsByEmailForm->createView(),
            'searchParametersForm' => $searchParametersForm->createView(),
            'logsSum' => count($this->getLogRepository()->findAll()),
            'logs' => $this->getLogRepository()->findAllWithEmployeeInformation($filters),
            'sql_query' => $this->getLogRepository()->findAllWithEmployeeInformationQuery($filters),
            'sql_manager_add_link' => $this->get('prestashop.adapter.legacy.context')->getAdminLink(
                'AdminRequestSql',
                true,
                array(
                'addrequest_sql' => 1
            )),
        );

        return $this->render('@AdvancedParameters/LogsPage/logs.html.twig', $twigValues);
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function searchAction(Request $request)
    {
        if ($this->isDemoModeEnabled()) {
            $this->addFlash('error', $this->getDemoModeErrorMessage());

            return $this->redirectToRoute('admin_logs');
        }

        $searchParametersForm = $this->createForm(FilterLogsByAttributeType::class);
        $searchParametersForm->handleRequest($request);
        $filters = array();

        if (!in_array(
            $this->authorizationLevel($this::CONTROLLER_NAME),
            array(
                PageVoter::LEVEL_READ,
                PageVoter::LEVEL_UPDATE,
                PageVoter::LEVEL_CREATE,
                PageVoter::LEVEL_DELETE,
            )
        )) {
            $this->addFlash('error', $this->trans('You do not have permission to update this.', 'Admin.Notifications.Error'));

            return $this->redirectToRoute('admin_logs');
        }

        $this->dispatchHook('actionAdminLogsControllerPostProcessBefore', array('controller' => $this));

        if ($searchParametersForm->isSubmitted()) {
            $filters = $searchParametersForm->getData();
        }

        return $this->redirectToRoute('admin_logs', array('filters' => $filters));
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     * @throws \Exception
     */
    public function processFormAction(Request $request)
    {
        if ($this->isDemoModeEnabled()) {
            $this->addFlash('error', $this->getDemoModeErrorMessage());

            return $this->redirectToRoute('admin_logs');
        }

        $logsByEmailForm = $this->getFormHandler()->getForm();
        $logsByEmailForm->handleRequest($request);

        if (!in_array(
            $this->authorizationLevel($this::CONTROLLER_NAME),
            array(
                PageVoter::LEVEL_READ,
                PageVoter::LEVEL_UPDATE,
                PageVoter::LEVEL_CREATE,
                PageVoter::LEVEL_DELETE,
            )
        )) {
            $this->addFlash('error', $this->trans('You do not have permission to update this.', 'Admin.Notifications.Error'));

            return $this->redirectToRoute('admin_logs');
        }

        $this->dispatchHook('actionAdminLogsControllerPostProcessBefore', array('controller' => $this));

        if ($logsByEmailForm->isSubmitted()) {
            $data = $logsByEmailForm->getData();

            $saveErrors = $this->getFormHandler()->save($data);

            if (0 === count($saveErrors)) {
                $this->addFlash('success', $this->trans('Successful update.', 'Admin.Notifications.Success'));

                return $this->redirectToRoute('admin_logs');
            }

            $this->flashErrors($saveErrors);
        }

        return $this->redirectToRoute('admin_logs');
    }

    /**
     * @return RedirectResponse
     * @throws \Doctrine\DBAL\Exception\InvalidArgumentException
     */
    public function deleteAllAction()
    {
        $this->getLogRepository()->deleteAll();

        return $this->redirectToRoute('admin_logs');
    }

    /**
     * @return FormHandlerInterface the form handler to set the severity level.
     */
    private function getFormHandler()
    {
        return $this->get('prestashop.adapter.logs.form_handler');
    }

    /**
     * @return LogRepository the repository to retrieve logs from database.
     */
    private function getLogRepository()
    {
        return $this->get('prestashop.core.admin.log.repository');
    }
}
