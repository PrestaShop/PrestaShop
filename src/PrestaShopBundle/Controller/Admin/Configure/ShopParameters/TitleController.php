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

declare(strict_types=1);

namespace PrestaShopBundle\Controller\Admin\Configure\ShopParameters;

use Exception;
use PrestaShop\PrestaShop\Core\Domain\Title\Command\BulkDeleteTitleCommand;
use PrestaShop\PrestaShop\Core\Domain\Title\Command\DeleteTitleCommand;
use PrestaShop\PrestaShop\Core\Domain\Title\Exception\TitleException;
use PrestaShop\PrestaShop\Core\Domain\Title\Exception\TitleNotFoundException;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\Builder\FormBuilderInterface;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\Handler\FormHandlerInterface;
use PrestaShop\PrestaShop\Core\Search\Filters\TitleFilters;
use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use PrestaShopBundle\Security\Attribute\AdminSecurity;
use PrestaShopBundle\Security\Attribute\DemoRestricted;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Controller responsible for "Configure > Shop Parameters > Customer Settings > Titles" page.
 */
class TitleController extends FrameworkBundleAdminController
{
    /**
     * Show customer titles page.
     *
     * @param Request $request
     * @param TitleFilters $filters
     *
     * @return Response
     */
    #[AdminSecurity("is_granted('read', request.get('_legacy_controller'))", message: 'Access denied.')]
    public function indexAction(Request $request, TitleFilters $filters): Response
    {
        $titleGridFactory = $this->get('prestashop.core.grid.factory.title');
        $titleGrid = $titleGridFactory->getGrid($filters);

        return $this->render('@PrestaShop/Admin/Configure/ShopParameters/CustomerSettings/Title/index.html.twig', [
            'titleGrid' => $this->presentGrid($titleGrid),
            'layoutTitle' => $this->trans('Titles', 'Admin.Navigation.Menu'),
            'help_link' => $this->generateSidebarLink($request->attributes->get('_legacy_controller')),
            'enableSidebar' => true,
        ]);
    }

    /**
     * Displays and handles title form.
     *
     * @param Request $request
     *
     * @return Response
     */
    #[AdminSecurity("is_granted('create', request.get('_legacy_controller'))", redirectRoute: 'admin_title_index', message: 'You need permission to create this.')]
    public function createAction(Request $request): Response
    {
        $titleForm = $this->getFormBuilder()->getForm();
        $titleForm->handleRequest($request);

        try {
            $handlerResult = $this->getFormHandler()->handle($titleForm);
            if ($handlerResult->isSubmitted() && $handlerResult->isValid()) {
                $this->addFlash('success', $this->trans('Successful creation', 'Admin.Notifications.Success'));

                return $this->redirectToRoute('admin_title_index');
            }
        } catch (TitleException $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages()));
        }

        return $this->render('@PrestaShop/Admin/Configure/ShopParameters/CustomerSettings/Title/create.html.twig', [
            'enableSidebar' => true,
            'titleForm' => $titleForm->createView(),
            'help_link' => $this->generateSidebarLink($request->attributes->get('_legacy_controller')),
        ]);
    }

    /**
     * Displays title form.
     *
     * @param int $titleId
     * @param Request $request
     *
     * @return Response
     */
    #[AdminSecurity("is_granted('update', request.get('_legacy_controller'))", redirectRoute: 'admin_title_index', message: 'You need permission to edit this.')]
    public function editAction(int $titleId, Request $request): Response
    {
        $titleForm = null;

        try {
            $titleForm = $this->getFormBuilder()->getFormFor((int) $titleId);
            $titleForm->handleRequest($request);
            $result = $this->getFormHandler()->handleFor((int) $titleId, $titleForm);
            if ($result->isSubmitted() && $result->isValid()) {
                $this->addFlash('success', $this->trans('Update successful', 'Admin.Notifications.Success'));

                return $this->redirectToRoute('admin_title_index');
            }
        } catch (TitleNotFoundException $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages()));

            return $this->redirectToRoute('admin_title_index');
        } catch (Exception $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages()));
        }

        return $this->render('@PrestaShop/Admin/Configure/ShopParameters/CustomerSettings/Title/edit.html.twig', [
            'enableSidebar' => true,
            'layoutTitle' => $this->trans(
                'Edit: %value%',
                'Admin.Actions',
                [
                    '%value%' => $titleForm->getData()['name'][$this->getContextLangId()],
                ]
            ),
            'titleForm' => $titleForm->createView(),
            'help_link' => $this->generateSidebarLink($request->attributes->get('_legacy_controller')),
        ]);
    }

    /**
     * Deletes title.
     *
     * @param int $titleId
     *
     * @return RedirectResponse
     */
    #[DemoRestricted(redirectRoute: 'admin_title_index')]
    #[AdminSecurity("is_granted('delete', request.get('_legacy_controller'))", redirectRoute: 'admin_title_index', message: 'You need permission to delete this.')]
    public function deleteAction(int $titleId): RedirectResponse
    {
        try {
            $this->getCommandBus()->handle(new DeleteTitleCommand($titleId));
            $this->addFlash(
                'success',
                $this->trans('Successful deletion', 'Admin.Notifications.Success')
            );
        } catch (TitleException $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages()));
        }

        return $this->redirectToRoute('admin_title_index');
    }

    /**
     * Deletes titles in bulk action
     *
     * @param Request $request
     *
     * @return RedirectResponse
     */
    #[DemoRestricted(redirectRoute: 'admin_title_index')]
    #[AdminSecurity("is_granted('delete', request.get('_legacy_controller'))", redirectRoute: 'admin_title_index')]
    public function bulkDeleteAction(Request $request): RedirectResponse
    {
        $titleIds = $this->getBulkTitlesFromRequest($request);

        try {
            $this->getCommandBus()->handle(new BulkDeleteTitleCommand($titleIds));
            $this->addFlash(
                'success',
                $this->trans('Successful deletion', 'Admin.Notifications.Success')
            );
        } catch (TitleException $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages()));
        }

        return $this->redirectToRoute('admin_title_index');
    }

    /**
     * @param Request $request
     *
     * @return array<int, int>
     */
    private function getBulkTitlesFromRequest(Request $request): array
    {
        $titleIds = $request->request->all('title_title_bulk');

        foreach ($titleIds as $i => $titleId) {
            $titleIds[$i] = (int) $titleId;
        }

        return $titleIds;
    }

    /**
     * @return array<string, string>
     */
    private function getErrorMessages(): array
    {
        return [
            TitleNotFoundException::class => $this->trans(
                'The object cannot be loaded (or found).',
                'Admin.Notifications.Error'
            ),
        ];
    }

    /**
     * @return FormHandlerInterface
     */
    protected function getFormHandler(): FormHandlerInterface
    {
        return $this->get('prestashop.core.form.identifiable_object.handler.title_form_handler');
    }

    /**
     * @return FormBuilderInterface
     */
    protected function getFormBuilder(): FormBuilderInterface
    {
        return $this->get('prestashop.core.form.identifiable_object.builder.title_form_builder');
    }
}
