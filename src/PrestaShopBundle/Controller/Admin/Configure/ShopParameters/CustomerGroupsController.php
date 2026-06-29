<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\Controller\Admin\Configure\ShopParameters;

use Exception;
use PrestaShop\PrestaShop\Core\Domain\Customer\Group\Command\BulkDeleteCustomerGroupCommand;
use PrestaShop\PrestaShop\Core\Domain\Customer\Group\Command\DeleteCustomerGroupCommand;
use PrestaShop\PrestaShop\Core\Domain\Customer\Group\Command\ToggleCustomerGroupShowPricesCommand;
use PrestaShop\PrestaShop\Core\Domain\Customer\Group\Exception\BulkDeleteCustomerGroupException;
use PrestaShop\PrestaShop\Core\Domain\Customer\Group\Exception\CannotDeleteGroupException;
use PrestaShop\PrestaShop\Core\Domain\Customer\Group\Exception\CannotToggleCustomerGroupShowPricesException;
use PrestaShop\PrestaShop\Core\Domain\Customer\Group\Exception\GroupNotFoundException;
use PrestaShop\PrestaShop\Core\Form\Handler;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\Builder\FormBuilderInterface;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\Handler\FormHandlerInterface;
use PrestaShop\PrestaShop\Core\Grid\GridFactoryInterface;
use PrestaShop\PrestaShop\Core\Group\Provider\DefaultGroupsProviderInterface;
use PrestaShop\PrestaShop\Core\Search\Filters\CustomerGroupsFilters;
use PrestaShopBundle\Controller\Admin\PrestaShopAdminController;
use PrestaShopBundle\Security\Attribute\AdminSecurity;
use PrestaShopBundle\Security\Attribute\DemoRestricted;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Controller responsible for "Configure > Shop Parameters > Customer Settings > Groups" page.
 */
class CustomerGroupsController extends PrestaShopAdminController
{
    #[AdminSecurity("is_granted('read', request.get('_legacy_controller'))", message: 'Access denied.')]
    public function indexAction(
        Request $request,
        CustomerGroupsFilters $filters,
        #[Autowire(service: 'prestashop.core.grid.factory.customer_groups')]
        GridFactoryInterface $customerGroupsGridFactory,
        #[Autowire(service: 'prestashop.admin.customer_group.default_groups.form_handler')]
        Handler $defaultGroupsFormHandler,
        #[Autowire(service: 'prestashop.adapter.group.provider.default_groups_provider')]
        DefaultGroupsProviderInterface $defaultGroupsProvider,
    ): Response {
        $customerGroupsGrid = $customerGroupsGridFactory->getGrid($filters);
        $defaultGroupsForm = $defaultGroupsFormHandler->getForm();

        $defaultGroups = $defaultGroupsProvider->getGroups();

        return $this->render('@PrestaShop/Admin/Configure/ShopParameters/CustomerSettings/Groups/index.html.twig', [
            'customerGroupsGrid' => $this->presentGrid($customerGroupsGrid),
            'defaultGroupsForm' => $defaultGroupsForm->createView(),
            'layoutTitle' => $this->trans('Groups', [], 'Admin.Navigation.Menu'),
            'help_link' => $this->generateSidebarLink($request->attributes->get('_legacy_controller')),
            'enableSidebar' => true,
            'unidentifiedGroupName' => $defaultGroups->getVisitorsGroup()->getName(),
            'guestGroupName' => $defaultGroups->getGuestsGroup()->getName(),
            'customerGroupName' => $defaultGroups->getCustomersGroup()->getName(),
        ]);
    }

    #[AdminSecurity("is_granted('create', request.get('_legacy_controller'))", redirectRoute: 'admin_customer_groups_index', message: 'You need permission to create this.')]
    public function createAction(
        Request $request,
        #[Autowire(service: 'prestashop.core.form.identifiable_object.builder.customer_group_form_builder')]
        FormBuilderInterface $formBuilder,
        #[Autowire(service: 'prestashop.core.form.identifiable_object.handler.customer_group_form_handler')]
        FormHandlerInterface $formHandler,
    ): Response {
        $form = $formBuilder->getForm();
        $form->handleRequest($request);

        try {
            $result = $formHandler->handle($form);
            if ($result->getIdentifiableObjectId() !== null) {
                $this->addFlash('success', $this->trans('Successful creation.', [], 'Admin.Notifications.Success'));

                return $this->redirectToRoute('admin_customer_groups_index');
            }
        } catch (Exception $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages()));
        }

        return $this->render('@PrestaShop/Admin/Configure/ShopParameters/CustomerSettings/Groups/create.html.twig', [
            'customerGroupForm' => $form->createView(),
            'layoutTitle' => $this->trans('New group', [], 'Admin.Shopparameters.Feature'),
            'help_link' => $this->generateSidebarLink($request->attributes->get('_legacy_controller')),
            'enableSidebar' => true,
        ]);
    }

    #[AdminSecurity("is_granted('update', request.get('_legacy_controller'))", redirectRoute: 'admin_customer_groups_index', message: 'You need permission to edit this.')]
    public function editAction(
        int $groupId,
        Request $request,
        #[Autowire(service: 'prestashop.core.form.identifiable_object.builder.customer_group_form_builder')]
        FormBuilderInterface $formBuilder,
        #[Autowire(service: 'prestashop.core.form.identifiable_object.handler.customer_group_form_handler')]
        FormHandlerInterface $formHandler,
    ): Response {
        try {
            $form = $formBuilder->getFormFor($groupId);
        } catch (Exception $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages()));

            return $this->redirectToRoute('admin_customer_groups_index');
        }

        $form->handleRequest($request);

        try {
            $result = $formHandler->handleFor($groupId, $form);
            if ($result->isSubmitted() && $result->isValid()) {
                $this->addFlash('success', $this->trans('Successful update.', [], 'Admin.Notifications.Success'));

                return $this->redirectToRoute('admin_customer_groups_index');
            }
        } catch (Exception $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages()));
        }

        $formData = $form->getData();
        $langId = $this->getLanguageContext()->getId();
        $names = $formData['name'] ?? [];
        $groupName = $names[$langId] ?? reset($names) ?: '';

        return $this->render('@PrestaShop/Admin/Configure/ShopParameters/CustomerSettings/Groups/edit.html.twig', [
            'customerGroupForm' => $form->createView(),
            'groupId' => $groupId,
            'layoutTitle' => $this->trans('Edit: %name%', ['%name%' => $groupName], 'Admin.Actions'),
            'help_link' => $this->generateSidebarLink($request->attributes->get('_legacy_controller')),
            'enableSidebar' => true,
        ]);
    }

    #[DemoRestricted(redirectRoute: 'admin_customer_groups_index')]
    #[AdminSecurity("is_granted('update', request.get('_legacy_controller'))", redirectRoute: 'admin_customer_groups_index', message: 'You need permission to edit this.')]
    public function saveDefaultGroupsAction(
        Request $request,
        #[Autowire(service: 'prestashop.admin.customer_group.default_groups.form_handler')]
        Handler $defaultGroupsFormHandler,
    ): RedirectResponse {
        $defaultGroupsForm = $defaultGroupsFormHandler->getForm();
        $defaultGroupsForm->handleRequest($request);

        if ($defaultGroupsForm->isSubmitted()) {
            $errors = $defaultGroupsFormHandler->save($defaultGroupsForm->getData());
            if (empty($errors)) {
                $this->addFlash('success', $this->trans('Update successful', [], 'Admin.Notifications.Success'));
            } else {
                $this->addFlashErrors($errors);
            }
        }

        return $this->redirectToRoute('admin_customer_groups_index');
    }

    #[DemoRestricted(redirectRoute: 'admin_customer_groups_index')]
    #[AdminSecurity("is_granted('update', request.get('_legacy_controller'))", redirectRoute: 'admin_customer_groups_index', message: 'You need permission to edit this.')]
    public function toggleShowPricesAction(int $groupId): RedirectResponse
    {
        try {
            $this->dispatchCommand(new ToggleCustomerGroupShowPricesCommand($groupId));
            $this->addFlash('success', $this->trans('The status has been successfully updated.', [], 'Admin.Notifications.Success'));
        } catch (Exception $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages()));
        }

        return $this->redirectToRoute('admin_customer_groups_index');
    }

    #[DemoRestricted(redirectRoute: 'admin_customer_groups_index')]
    #[AdminSecurity("is_granted('delete', request.get('_legacy_controller'))", redirectRoute: 'admin_customer_groups_index', message: 'You need permission to delete this.')]
    public function deleteAction(int $groupId): RedirectResponse
    {
        try {
            $this->dispatchCommand(new DeleteCustomerGroupCommand($groupId));
            $this->addFlash('success', $this->trans('Successful deletion', [], 'Admin.Notifications.Success'));
        } catch (Exception $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages()));
        }

        return $this->redirectToRoute('admin_customer_groups_index');
    }

    #[DemoRestricted(redirectRoute: 'admin_customer_groups_index')]
    #[AdminSecurity("is_granted('delete', request.get('_legacy_controller'))", redirectRoute: 'admin_customer_groups_index', message: 'You need permission to delete this.')]
    public function bulkDeleteAction(Request $request): RedirectResponse
    {
        $groupIds = array_map('intval', $request->request->all('customer_groups_title_bulk'));

        try {
            $this->dispatchCommand(new BulkDeleteCustomerGroupCommand($groupIds));
            $this->addFlash('success', $this->trans('The selection has been successfully deleted.', [], 'Admin.Notifications.Success'));
        } catch (BulkDeleteCustomerGroupException $e) {
            foreach ($e->getExceptions() as $exception) {
                $this->addFlash('error', $this->getErrorMessageForException($exception, $this->getErrorMessages()));
            }
        } catch (Exception $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages()));
        }

        return $this->redirectToRoute('admin_customer_groups_index');
    }

    private function getErrorMessages(): array
    {
        return [
            GroupNotFoundException::class => $this->trans('The object cannot be loaded (or found).', [], 'Admin.Notifications.Error'),
            CannotDeleteGroupException::class => $this->trans('An error occurred while deleting the object.', [], 'Admin.Notifications.Error'),
            CannotToggleCustomerGroupShowPricesException::class => $this->trans('An error occurred while updating the status.', [], 'Admin.Notifications.Error'),
        ];
    }
}
