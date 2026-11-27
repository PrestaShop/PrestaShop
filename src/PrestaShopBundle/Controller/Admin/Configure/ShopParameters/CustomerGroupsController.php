<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\Controller\Admin\Configure\ShopParameters;

use PrestaShop\PrestaShop\Adapter\LegacyContext;
use PrestaShop\PrestaShop\Core\Domain\Customer\Group\Command\BulkDeleteCustomerGroupsCommand;
use PrestaShop\PrestaShop\Core\Domain\Customer\Group\Command\DeleteCustomerGroupCommand;
use PrestaShop\PrestaShop\Core\Domain\Customer\Group\Exception\BulkCustomerGroupException;
use PrestaShop\PrestaShop\Core\Domain\Customer\Group\Exception\CannotDeleteGroupException;
use PrestaShop\PrestaShop\Core\Domain\Customer\Group\Exception\GroupConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Customer\Group\Exception\GroupException;
use PrestaShop\PrestaShop\Core\Domain\Customer\Group\Exception\GroupNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Customer\Group\Query\GetCustomerGroupForEditing;
use PrestaShop\PrestaShop\Core\Domain\Customer\Group\Query\GetDefaultCustomerGroups;
use PrestaShop\PrestaShop\Core\Domain\Customer\Group\QueryResult\DefaultCustomerGroups;
use PrestaShop\PrestaShop\Core\Domain\Customer\Group\QueryResult\EditableCustomerGroup;
use PrestaShop\PrestaShop\Core\Feature\FeatureInterface;
use PrestaShop\PrestaShop\Core\Form\FormHandlerInterface as ConfigurationFormHandlerInterface;
use PrestaShop\PrestaShop\Core\Grid\GridFactoryInterface;
use PrestaShop\PrestaShop\Core\Search\Filters\CustomerGroupsFilters;
use PrestaShopBundle\Controller\Admin\PrestaShopAdminController;
use PrestaShopBundle\Controller\BulkActionsTrait;
use PrestaShopBundle\Security\Attribute\AdminSecurity;
use PrestaShopBundle\Security\Attribute\DemoRestricted;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

/**
 * Controller responsible for "Configure > Shop Parameters > Customer Settings > Groups" page.
 */
class CustomerGroupsController extends PrestaShopAdminController
{
    use BulkActionsTrait;

    #[AdminSecurity("is_granted('read', request.get('_legacy_controller'))", message: 'Access denied.')]
    public function indexAction(
        Request $request,
        CustomerGroupsFilters $filters,
        #[Autowire(service: 'prestashop.core.grid.factory.customer_groups')]
        GridFactoryInterface $customerGroupsGridFactory,
        #[Autowire(service: 'prestashop.adapter.feature.group_feature')]
        FeatureInterface $groupFeature,
        #[Autowire(service: 'prestashop.admin.customer_groups.default_groups.form_handler')]
        ConfigurationFormHandlerInterface $defaultGroupsFormHandler,
    ): Response {
        $templateParameters = [
            'layoutTitle' => $this->trans('Groups', [], 'Admin.Navigation.Menu'),
            'help_link' => $this->generateSidebarLink($request->attributes->get('_legacy_controller')),
            'enableSidebar' => true,
            'showDisabledFeatureWarning' => !$groupFeature->isActive(),
        ];

        if ($groupFeature->isActive()) {
            $templateParameters['customerGroupsGrid'] = $this->presentGrid($customerGroupsGridFactory->getGrid($filters));
            $templateParameters['defaultGroupNames'] = $this->getDefaultGroupNames();
            $templateParameters['defaultGroupsForm'] = $defaultGroupsFormHandler->getForm()->createView();
            $templateParameters['layoutHeaderToolbarBtn'] = [
                'add' => [
                    'href' => $this->generateUrl('admin_customer_groups_create'),
                    'desc' => $this->trans('Add new group', [], 'Admin.Shopparameters.Feature'),
                    'icon' => 'add_circle_outline',
                ],
            ];
        }

        return $this->render('@PrestaShop/Admin/Configure/ShopParameters/CustomerSettings/Groups/index.html.twig', $templateParameters);
    }

    #[DemoRestricted(redirectRoute: 'admin_customer_groups_index')]
    #[AdminSecurity("is_granted('update', request.get('_legacy_controller'))", redirectRoute: 'admin_customer_groups_index')]
    public function saveDefaultGroupsAction(
        Request $request,
        #[Autowire(service: 'prestashop.admin.customer_groups.default_groups.form_handler')]
        ConfigurationFormHandlerInterface $defaultGroupsFormHandler,
    ): RedirectResponse {
        $defaultGroupsForm = $defaultGroupsFormHandler->getForm();
        $defaultGroupsForm->handleRequest($request);

        if ($defaultGroupsForm->isSubmitted()) {
            if (!$defaultGroupsForm->isValid()) {
                $this->addFlashFormErrors($defaultGroupsForm, true);

                return $this->redirectToRoute('admin_customer_groups_index');
            }

            $errors = $defaultGroupsFormHandler->save($defaultGroupsForm->getData());

            if (empty($errors)) {
                $this->addFlash('success', $this->trans('Update successful', [], 'Admin.Notifications.Success'));
            } else {
                $this->addFlashErrors($errors);
            }
        }

        return $this->redirectToRoute('admin_customer_groups_index');
    }

    #[AdminSecurity("is_granted('create', request.get('_legacy_controller'))", redirectRoute: 'admin_customer_groups_index', message: 'You need permission to create this.')]
    public function createAction(LegacyContext $legacyContext): Response
    {
        return $this->redirect($legacyContext->getAdminLink('AdminGroups', true, ['addgroup' => '']));
    }

    #[AdminSecurity("is_granted('update', request.get('_legacy_controller'))", redirectRoute: 'admin_customer_groups_index', message: 'You need permission to edit this.')]
    public function editAction(int $groupId, LegacyContext $legacyContext): Response
    {
        return $this->redirect($legacyContext->getAdminLink('AdminGroups', true, ['updategroup' => '', 'id_group' => $groupId]));
    }

    #[AdminSecurity("is_granted('read', request.get('_legacy_controller'))", redirectRoute: 'admin_customer_groups_index')]
    public function viewAction(int $groupId, LegacyContext $legacyContext): Response
    {
        return $this->redirect($legacyContext->getAdminLink('AdminGroups', true, ['viewgroup' => '', 'id_group' => $groupId]));
    }

    #[DemoRestricted(redirectRoute: 'admin_customer_groups_index')]
    #[AdminSecurity("is_granted('delete', request.get('_legacy_controller'))", redirectRoute: 'admin_customer_groups_index', message: 'You need permission to delete this.')]
    public function deleteAction(int $groupId): RedirectResponse
    {
        try {
            $this->dispatchCommand(new DeleteCustomerGroupCommand($groupId));
            $this->addFlash('success', $this->trans('Successful deletion', [], 'Admin.Notifications.Success'));
        } catch (GroupException $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages()));
        }

        return $this->redirectToRoute('admin_customer_groups_index');
    }

    #[DemoRestricted(redirectRoute: 'admin_customer_groups_index')]
    #[AdminSecurity("is_granted('delete', request.get('_legacy_controller'))", redirectRoute: 'admin_customer_groups_index', message: 'You need permission to delete this.')]
    public function bulkDeleteAction(Request $request): RedirectResponse
    {
        $customerGroupIds = $this->getBulkActionIds($request, 'customer_groups_bulk');
        if (empty($customerGroupIds)) {
            $this->addFlash('info', $this->trans('You must select at least one element to delete.', [], 'Admin.Notifications.Error'));

            return $this->redirectToRoute('admin_customer_groups_index');
        }

        try {
            $this->dispatchCommand(new BulkDeleteCustomerGroupsCommand($customerGroupIds));
            $this->addFlash('success', $this->trans('The selection has been successfully deleted.', [], 'Admin.Notifications.Success'));
        } catch (BulkCustomerGroupException $e) {
            $errorMessages = $this->getErrorMessages();
            $this->addFlashErrors(array_unique(array_map(
                fn (Throwable $exception): string => $this->getErrorMessageForException($exception, $errorMessages),
                $e->getExceptions()
            )));
        } catch (GroupException $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages()));
        }

        return $this->redirectToRoute('admin_customer_groups_index');
    }

    /**
     * @return array{visitors: string, guests: string, customers: string}
     */
    private function getDefaultGroupNames(): array
    {
        /** @var DefaultCustomerGroups $defaultGroups */
        $defaultGroups = $this->dispatchQuery(new GetDefaultCustomerGroups($this->getShopContext()->getShopConstraint()));

        return [
            'visitors' => $this->getGroupName($defaultGroups->getVisitorsGroupId()),
            'guests' => $this->getGroupName($defaultGroups->getGuestsGroupId()),
            'customers' => $this->getGroupName($defaultGroups->getCustomersGroupId()),
        ];
    }

    private function getGroupName(int $groupId): string
    {
        /** @var EditableCustomerGroup $group */
        $group = $this->dispatchQuery(new GetCustomerGroupForEditing($groupId));
        $names = $group->getLocalizedNames();

        return $names[$this->getLanguageContext()->getId()] ?? (string) reset($names);
    }

    private function getErrorMessages(): array
    {
        return [
            BulkCustomerGroupException::class => [
                BulkCustomerGroupException::FAILED_BULK_DELETE => $this->trans(
                    'An error occurred while deleting this selection.',
                    [],
                    'Admin.Notifications.Error'
                ),
            ],
            CannotDeleteGroupException::class => [
                CannotDeleteGroupException::GROUP_USED_AS_DEFAULT => $this->trans(
                    'You cannot delete a group that is used as the visitors, guests or customers default group.',
                    [],
                    'Admin.Shopparameters.Notification'
                ),
                0 => $this->trans('An error occurred while deleting the object.', [], 'Admin.Notifications.Error'),
            ],
            GroupConstraintException::class => $this->trans(
                'The object cannot be loaded (the identifier is missing or invalid)',
                [],
                'Admin.Notifications.Error'
            ),
            GroupException::class => $this->trans('An unexpected error occurred.', [], 'Admin.Notifications.Error'),
            GroupNotFoundException::class => $this->trans(
                'The object cannot be loaded (or found).',
                [],
                'Admin.Notifications.Error'
            ),
        ];
    }
}
