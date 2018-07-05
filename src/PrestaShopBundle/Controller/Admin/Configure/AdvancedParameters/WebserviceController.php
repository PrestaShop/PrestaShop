<?php
/**
 * 2007-2018 PrestaShop
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
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShopBundle\Controller\Admin\Configure\AdvancedParameters;

use PrestaShop\PrestaShop\Core\Form\FormHandlerInterface;
use PrestaShop\PrestaShop\Core\Grid\Search\WebserviceKeyGridSearchCriteria;
use PrestaShop\PrestaShop\Core\Webservice\WebserviceCanBeEnabledConfigurationChecker;
use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use PrestaShopBundle\Entity\Repository\WebserviceKeyRepository;
use PrestaShopBundle\Form\Admin\Configure\AdvancedParameters\Logs\FilterLogsByAttributeType;
use PrestaShopBundle\Security\Annotation\AdminSecurity;
use PrestaShopBundle\Security\Annotation\DemoRestricted;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\VarDumper\VarDumper;

/**
 * Responsible of "Configure > Advanced Parameters > Webservice" page display
 *
 * @todo: add unit tests
 */
class WebserviceController extends FrameworkBundleAdminController
{
    const CONTROLLER_NAME = 'AdminWebservice';

    /**
     * Displays the Webservice main page.
     * @AdminSecurity("is_granted('read', request.get('_legacy_controller')~'_')", message="Access denied.")
     *
     * @param FormInterface $form
     * @return Response
     */
    public function indexAction(FormInterface $form = null, Request $request)
    {
        /*
         * Listing TODO-list
         * - sort columns
         * - pagination
         * - "enabled"= drop-down yes/no
         * - 4 actions standard
         * - Bulk: select all, unselect all, delete selected
         * - foreach item: edit/delete
         */

        if('POST' === $request->getMethod()) {
            if ($request->request->has('form')) {
                $submittedForm =$request->request->get('form');
                if (array_key_exists('webservice_configuration', $submittedForm)) {
                    return $this->processFormAction($request);
                }
            }
        }

        $form = is_null($form) ? $this->getFormHandler()->getForm() : $form;

        $presentedGrid = $this->handleListingSearch($request);

        $configurationWarnings = $this->lookForWarnings($request);

        if (false === empty($configurationWarnings)) {
            foreach ($configurationWarnings as $warningMessage) {
                $this->addFlash('warning', $warningMessage);

            }
        }

        $twigValues = [
            'layoutTitle' => $this->trans('Webservice', 'Admin.Navigation.Menu'),
            'requireAddonsSearch' => false,
            'requireBulkActions' => false, // temporary
            'showContentHeader' => true,
            'enableSidebar' => true,
            'help_link' => $this->generateSidebarLink('AdminWebservice'),
            'requireFilterStatus' => false,
            'form' => $form->createView(),
            'grid' => $presentedGrid,
        ];

        return $this->render('@AdvancedParameters/WebservicePage/webservice.html.twig', $twigValues);

    }

    /**
     * Handles the webservice configuration form
     *
     * @param Request $request
     *
     * @return RedirectResponse
     */
    private function processFormAction(Request $request)
    {
        $this->dispatchHook('actionAdminAdminWebserviceControllerPostProcessBefore', array('controller' => $this));

        $form = $this->getFormHandler()->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $data = $form->getData();

            $saveErrors = $this->getFormHandler()->save($data);

            if (0 === count($saveErrors)) {
                $this->addFlash('success', $this->trans('Update successful', 'Admin.Notifications.Success'));

                return $this->redirectToRoute('admin_webservice');
            }

            $this->flashErrors($saveErrors);
        }

        return $this->redirectToRoute('admin_webservice');
    }

    /**
     * @param Request $request
     *
     * @return string[]
     */
    private function lookForWarnings(Request $request)
    {
        /** @var WebserviceCanBeEnabledConfigurationChecker $configurationChecker */
        $configurationChecker = $this->get('prestashop.core.configuration.webservice_can_be_enabled_configuration_checker');

        $warningMessages = $configurationChecker->analyseConfigurationForIssues($request);

        return $warningMessages;
    }

    /**
     * @param Request $request
     *
     * @return array
     */
    private function handleListingSearch(Request $request)
    {
        // temporary search criteria class, to be removed ? see TemporarySearchCriteria
        $searchCriteria = new WebserviceKeyGridSearchCriteria($request);

        $gridWebserviceKeyFactory = $this->get('prestashop.core.grid.webservice_key_factory');
        $grid = $gridWebserviceKeyFactory->createUsingSearchCriteria($searchCriteria);

        $gridPresenter = $this->get('prestashop.core.grid.presenter.grid_presenter');
        $presentedGrid = $gridPresenter->present($grid);

        return $presentedGrid;
    }

    /**
     * @return FormHandlerInterface
     */
    private function getFormHandler()
    {
        return $this->get('prestashop.adapter.webservice.form_handler');
    }
}
