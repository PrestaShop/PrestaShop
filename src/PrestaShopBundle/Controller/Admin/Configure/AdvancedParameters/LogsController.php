<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
        #[Autowire(service: 'prestashop.adapter.logs_database.form_handler')]
        FormHandlerInterface $databaseFormHandler,
    ): Response {
        $grid = $gridLogFactory->getGrid($filters);
        $logsByEmailForm = $formHandler->getForm();
        $databaseForm = $databaseFormHandler->getForm();

        return $this->render('@PrestaShop/Admin/Configure/AdvancedParameters/LogsPage/index.html.twig', [
            'layoutHeaderToolbarBtn' => [],
            'layoutTitle' => $this->trans('Logs', [], 'Admin.Navigation.Menu'),
            'requireBulkActions' => false,
            'showContentHeader' => true,
            'enableSidebar' => true,
            'help_link' => $this->generateSidebarLink('AdminLogs'),
            'logsByEmailForm' => $logsByEmailForm->createView(),
            'databaseForm' => $databaseForm->createView(),
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
     * @param Request $request
     *
     * @return RedirectResponse
     */
    #[DemoRestricted(redirectRoute: 'admin_logs_index')]
    #[AdminSecurity("is_granted('update', request.get('_legacy_controller')) && is_granted('create', request.get('_legacy_controller')) && is_granted('delete', request.get('_legacy_controller'))", message: 'You do not have permission to update this.', redirectRoute: 'admin_logs_index')]
    public function saveDatabaseSettingsAction(
        Request $request,
        #[Autowire(service: 'prestashop.adapter.logs_database.form_handler')]
        FormHandlerInterface $formHandler,
    ) {
        $databaseForm = $formHandler->getForm();
        $databaseForm->handleRequest($request);

        if ($databaseForm->isSubmitted()) {
            $data = $databaseForm->getData();

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
