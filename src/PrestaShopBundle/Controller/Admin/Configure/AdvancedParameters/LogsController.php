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

use PrestaShop\PrestaShop\Core\Form\FormHandlerInterface;
use PrestaShop\PrestaShop\Core\Grid\Definition\Factory\GridDefinitionFactoryInterface;
use PrestaShop\PrestaShop\Core\Grid\Definition\Factory\LogGridDefinitionFactory;
use PrestaShop\PrestaShop\Core\Grid\GridFactoryInterface;
use PrestaShop\PrestaShop\Core\Search\Filters\LogsFilters;
use PrestaShopBundle\Controller\Admin\PrestaShopAdminController;
use PrestaShopBundle\Entity\Repository\LogRepository;
use PrestaShopBundle\Security\Attribute\AdminSecurity;
use PrestaShopBundle\Security\Attribute\DemoRestricted;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Responsible for "Configure > Advanced Parameters > Logs" page display.
 */
class LogsController extends PrestaShopAdminController
{
    /**
     * @param LogsFilters $filters the list of filters from the request
     *
     * @return Response
     */
    #[AdminSecurity("is_granted('read', request.get('_legacy_controller'))", message: 'Access denied.')]
    public function indexAction(
        LogsFilters $filters,
        #[Autowire(service: 'prestashop.core.grid.log_factory')]
        GridFactoryInterface $gridLogFactory,
        #[Autowire(service: 'prestashop.adapter.logs.form_handler')]
        FormHandlerInterface $formHandler,
    ): Response {
        $grid = $gridLogFactory->getGrid($filters);
        $logsByEmailForm = $formHandler->getForm();

        return $this->render('@PrestaShop/Admin/Configure/AdvancedParameters/LogsPage/index.html.twig', [
            'layoutHeaderToolbarBtn' => [],
            'layoutTitle' => $this->trans('Logs', [], 'Admin.Navigation.Menu'),
            'requireBulkActions' => false,
            'showContentHeader' => true,
            'enableSidebar' => true,
            'help_link' => $this->generateSidebarLink('AdminLogs'),
            'logsByEmailForm' => $logsByEmailForm->createView(),
            'grid' => $this->presentGrid($grid),
        ]);
    }

    /**
     * @param Request $request
     *
     * @return RedirectResponse
     */
    #[DemoRestricted(redirectRoute: 'admin_logs_index')]
    #[AdminSecurity("is_granted('read', request.get('_legacy_controller')) && is_granted('update', request.get('_legacy_controller')) && is_granted('create', request.get('_legacy_controller')) && is_granted('delete', request.get('_legacy_controller'))", message: 'You do not have permission to update this.', redirectRoute: 'admin_logs_index')]
    public function searchAction(
        Request $request,
        #[Autowire(service: 'prestashop.core.grid.definition.factory.logs')]
        GridDefinitionFactoryInterface $definitionFactory
    ): RedirectResponse {
        $this->dispatchHookWithParameters('actionAdminLogsControllerPostProcessBefore', ['controller' => $this]);

        return $this->buildSearchResponse(
            $definitionFactory,
            $request,
            LogGridDefinitionFactory::GRID_ID,
            'admin_logs_index',
        );
    }

    /**
     * @param Request $request
     *
     * @return RedirectResponse
     */
    #[DemoRestricted(redirectRoute: 'admin_logs_index')]
    #[AdminSecurity("is_granted('update', request.get('_legacy_controller')) && is_granted('create', request.get('_legacy_controller')) && is_granted('delete', request.get('_legacy_controller'))", message: 'You do not have permission to update this.', redirectRoute: 'admin_logs_index')]
    public function saveSettingsAction(
        Request $request,
        #[Autowire(service: 'prestashop.adapter.logs.form_handler')]
        FormHandlerInterface $formHandler,
    ) {
        $logsByEmailForm = $formHandler->getForm();
        $logsByEmailForm->handleRequest($request);

        $this->dispatchHookWithParameters('actionAdminLogsControllerPostProcessBefore', ['controller' => $this]);

        if ($logsByEmailForm->isSubmitted()) {
            $data = $logsByEmailForm->getData();

            $saveErrors = $formHandler->save($data);

            if (0 === count($saveErrors)) {
                $this->addFlash('success', $this->trans('Successful update', [], 'Admin.Notifications.Success'));

                return $this->redirectToRoute('admin_logs_index');
            }

            $this->addFlashErrors($saveErrors);
        }

        return $this->redirectToRoute('admin_logs_index');
    }

    /**
     * @return RedirectResponse
     *
     * @throws \Doctrine\DBAL\Exception\InvalidArgumentException
     */
    #[AdminSecurity("is_granted('delete', request.get('_legacy_controller'))", message: 'You do not have permission to update this.', redirectRoute: 'admin_logs_index')]
    public function deleteAllAction(
        LogRepository $logRepository,
    ): RedirectResponse {
        $logRepository->deleteAll();

        $this->addFlash('success', $this->trans('Successful update', [], 'Admin.Notifications.Success'));

        return $this->redirectToRoute('admin_logs_index');
    }
}
