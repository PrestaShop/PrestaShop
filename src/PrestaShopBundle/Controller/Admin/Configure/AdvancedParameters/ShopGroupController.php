<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\Controller\Admin\Configure\AdvancedParameters;

use Exception;
use PrestaShop\PrestaShop\Adapter\Feature\MultistoreFeature;
use PrestaShop\PrestaShop\Core\Domain\Shop\Command\DeleteShopGroupCommand;
use PrestaShop\PrestaShop\Core\Domain\Shop\Exception\CannotAddShopGroupException;
use PrestaShop\PrestaShop\Core\Domain\Shop\Exception\CannotDeleteShopGroupException;
use PrestaShop\PrestaShop\Core\Domain\Shop\Exception\CannotUpdateShopGroupException;
use PrestaShop\PrestaShop\Core\Domain\Shop\Exception\ShopException;
use PrestaShop\PrestaShop\Core\Domain\Shop\Exception\ShopGroupConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Shop\Exception\ShopGroupNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Shop\Query\GetShopTree;
use PrestaShop\PrestaShop\Core\Form\FormHandlerInterface as ConfigurationFormHandlerInterface;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\Builder\FormBuilderInterface;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\Handler\FormHandlerInterface;
use PrestaShop\PrestaShop\Core\Grid\GridFactoryInterface;
use PrestaShop\PrestaShop\Core\Search\Filters\ShopGroupFilters;
use PrestaShopBundle\Controller\Admin\PrestaShopAdminController;
use PrestaShopBundle\Controller\Attribute\AllShopContext;
use PrestaShopBundle\Security\Attribute\AdminSecurity;
use PrestaShopBundle\Security\Attribute\DemoRestricted;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

#[AllShopContext]
class ShopGroupController extends PrestaShopAdminController
{
    #[AdminSecurity("is_granted('read', request.get('_legacy_controller'))")]
    public function indexAction(
        Request $request,
        ShopGroupFilters $filters,
        MultistoreFeature $multistoreFeature,
        #[Autowire(service: 'prestashop.core.grid.factory.shop_group')]
        GridFactoryInterface $shopGroupGridFactory,
        #[Autowire(service: 'prestashop.admin.multistore_options.form_handler')]
        ConfigurationFormHandlerInterface $multistoreOptionsFormHandler,
    ): Response {
        if (!$multistoreFeature->isActive()) {
            return $this->redirectToMultistoreActivation();
        }

        return $this->render('@PrestaShop/Admin/Configure/AdvancedParameters/ShopGroup/index.html.twig', [
            'shopGroupGrid' => $this->presentGrid($shopGroupGridFactory->getGrid($filters)),
            'multistoreOptionsForm' => $multistoreOptionsFormHandler->getForm()->createView(),
            'shopTree' => $this->dispatchQuery(new GetShopTree()),
            'enableSidebar' => true,
            'help_link' => $this->generateSidebarLink($request->attributes->get('_legacy_controller')),
        ]);
    }

    #[AdminSecurity("is_granted('create', request.get('_legacy_controller'))", redirectRoute: 'admin_shop_groups_index')]
    public function createAction(
        Request $request,
        MultistoreFeature $multistoreFeature,
        #[Autowire(service: 'prestashop.core.form.identifiable_object.builder.shop_group_form_builder')]
        FormBuilderInterface $shopGroupFormBuilder,
        #[Autowire(service: 'prestashop.core.form.identifiable_object.handler.shop_group_form_handler')]
        FormHandlerInterface $shopGroupFormHandler,
    ): Response {
        if (!$multistoreFeature->isActive()) {
            return $this->redirectToMultistoreActivation();
        }

        $shopGroupForm = $shopGroupFormBuilder->getForm();
        $shopGroupForm->handleRequest($request);

        try {
            $result = $shopGroupFormHandler->handle($shopGroupForm);

            if (null !== $result->getIdentifiableObjectId()) {
                $this->addFlash('success', $this->trans('Successful creation', [], 'Admin.Notifications.Success'));

                return $this->redirectToRoute('admin_shop_groups_index');
            }
        } catch (Exception $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages()));
        }

        return $this->render('@PrestaShop/Admin/Configure/AdvancedParameters/ShopGroup/create.html.twig', [
            'shopGroupForm' => $shopGroupForm->createView(),
            'shopTree' => $this->dispatchQuery(new GetShopTree()),
            'enableSidebar' => true,
            'help_link' => $this->generateSidebarLink($request->attributes->get('_legacy_controller')),
            'layoutTitle' => $this->trans('Add a new group of stores', [], 'Admin.Advparameters.Feature'),
        ]);
    }

    #[AdminSecurity("is_granted('update', request.get('_legacy_controller'))", redirectRoute: 'admin_shop_groups_index')]
    public function editAction(
        int $shopGroupId,
        Request $request,
        MultistoreFeature $multistoreFeature,
        #[Autowire(service: 'prestashop.core.form.identifiable_object.builder.shop_group_form_builder')]
        FormBuilderInterface $shopGroupFormBuilder,
        #[Autowire(service: 'prestashop.core.form.identifiable_object.handler.shop_group_form_handler')]
        FormHandlerInterface $shopGroupFormHandler,
    ): Response {
        if (!$multistoreFeature->isActive()) {
            return $this->redirectToMultistoreActivation();
        }

        try {
            $shopGroupForm = $shopGroupFormBuilder->getFormFor($shopGroupId);
        } catch (Exception $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages()));

            return $this->redirectToRoute('admin_shop_groups_index');
        }

        try {
            $shopGroupForm->handleRequest($request);
            $result = $shopGroupFormHandler->handleFor($shopGroupId, $shopGroupForm);

            if ($result->isSubmitted() && $result->isValid()) {
                $this->addFlash('success', $this->trans('Successful update', [], 'Admin.Notifications.Success'));

                return $this->redirectToRoute('admin_shop_groups_index');
            }
        } catch (Exception $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages()));
        }

        return $this->render('@PrestaShop/Admin/Configure/AdvancedParameters/ShopGroup/edit.html.twig', [
            'shopGroupForm' => $shopGroupForm->createView(),
            'shopTree' => $this->dispatchQuery(new GetShopTree()),
            'enableSidebar' => true,
            'help_link' => $this->generateSidebarLink($request->attributes->get('_legacy_controller')),
            'layoutTitle' => $this->trans('Editing store group "%name%"', ['%name%' => $shopGroupForm->getData()['name']], 'Admin.Navigation.Menu'),
        ]);
    }

    #[DemoRestricted(redirectRoute: 'admin_shop_groups_index')]
    #[AdminSecurity("is_granted('delete', request.get('_legacy_controller'))", redirectRoute: 'admin_shop_groups_index')]
    public function deleteAction(int $shopGroupId): RedirectResponse
    {
        try {
            $this->dispatchCommand(new DeleteShopGroupCommand($shopGroupId));
            $this->addFlash('success', $this->trans('Successful deletion', [], 'Admin.Notifications.Success'));
        } catch (ShopException $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages()));
        }

        return $this->redirectToRoute('admin_shop_groups_index');
    }

    #[DemoRestricted(redirectRoute: 'admin_shop_groups_index')]
    #[AdminSecurity("is_granted('update', request.get('_legacy_controller'))", redirectRoute: 'admin_shop_groups_index')]
    public function saveOptionsAction(
        Request $request,
        #[Autowire(service: 'prestashop.admin.multistore_options.form_handler')]
        ConfigurationFormHandlerInterface $multistoreOptionsFormHandler,
    ): RedirectResponse {
        $multistoreOptionsForm = $multistoreOptionsFormHandler->getForm();
        $multistoreOptionsForm->handleRequest($request);

        if ($multistoreOptionsForm->isSubmitted()) {
            $errors = $multistoreOptionsFormHandler->save($multistoreOptionsForm->getData());

            if (empty($errors)) {
                $this->addFlash('success', $this->trans('The settings have been successfully updated.', [], 'Admin.Notifications.Success'));
            } else {
                $this->addFlashErrors($errors);
            }
        }

        return $this->redirectToRoute('admin_shop_groups_index');
    }

    private function redirectToMultistoreActivation(): RedirectResponse
    {
        $this->addFlash('error', $this->trans('Access denied.', [], 'Admin.Notifications.Error'));

        return $this->redirectToRoute('admin_preferences');
    }

    private function getErrorMessages(): array
    {
        $shopGroupInUse = $this->trans('You cannot delete or disable a shop group in use.', [], 'Admin.Notifications.Error');

        return [
            ShopGroupNotFoundException::class => $this->trans('The object cannot be loaded (or found).', [], 'Admin.Notifications.Error'),
            CannotAddShopGroupException::class => $this->trans('An error occurred while creating the object.', [], 'Admin.Notifications.Error'),
            CannotUpdateShopGroupException::class => [
                CannotUpdateShopGroupException::SHARING_OPTIONS_LOCKED => $this->trans(
                    'Sharing options cannot be changed once there is more than one store.',
                    [],
                    'Admin.Advparameters.Notification'
                ),
                CannotUpdateShopGroupException::CANNOT_DISABLE_GROUP_WITH_SHOPS => $shopGroupInUse,
            ],
            CannotDeleteShopGroupException::class => [
                CannotDeleteShopGroupException::GROUP_HAS_SHOPS => $shopGroupInUse,
            ],
            ShopGroupConstraintException::class => [
                ShopGroupConstraintException::INVALID_NAME => $this->trans(
                    'The %s field is invalid.',
                    [sprintf('"%s"', $this->trans('Name of the store group', [], 'Admin.Advparameters.Feature'))],
                    'Admin.Notifications.Error'
                ),
                ShopGroupConstraintException::INVALID_COLOR => $this->trans(
                    'The %s field is invalid.',
                    [sprintf('"%s"', $this->trans('Color', [], 'Admin.Catalog.Feature'))],
                    'Admin.Notifications.Error'
                ),
                ShopGroupConstraintException::SHARE_ORDER_REQUIRES_SHARED_CUSTOMERS_AND_STOCK => $this->trans(
                    'Orders can only be shared when customers and available quantities are shared.',
                    [],
                    'Admin.Advparameters.Notification'
                ),
            ],
        ];
    }
}
