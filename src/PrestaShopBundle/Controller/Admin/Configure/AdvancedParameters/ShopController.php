<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\Controller\Admin\Configure\AdvancedParameters;

use Exception;
use PrestaShop\PrestaShop\Adapter\Feature\MultistoreFeature;
use PrestaShop\PrestaShop\Core\Domain\Category\Exception\CategoryNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Shop\Command\DeleteShopCommand;
use PrestaShop\PrestaShop\Core\Domain\Shop\Exception\CannotAddShopException;
use PrestaShop\PrestaShop\Core\Domain\Shop\Exception\CannotDeleteShopException;
use PrestaShop\PrestaShop\Core\Domain\Shop\Exception\CannotUpdateShopException;
use PrestaShop\PrestaShop\Core\Domain\Shop\Exception\ShopConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Shop\Exception\ShopException;
use PrestaShop\PrestaShop\Core\Domain\Shop\Exception\ShopGroupNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Shop\Exception\ShopNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Shop\Query\GetShopTree;
use PrestaShop\PrestaShop\Core\Domain\Shop\Query\SearchShops;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\Builder\FormBuilderInterface;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\Handler\FormHandlerInterface;
use PrestaShop\PrestaShop\Core\Grid\GridFactoryInterface;
use PrestaShop\PrestaShop\Core\Search\Filters\ShopFilters;
use PrestaShopBundle\Controller\Admin\PrestaShopAdminController;
use PrestaShopBundle\Controller\Attribute\AllShopContext;
use PrestaShopBundle\Security\Attribute\AdminSecurity;
use PrestaShopBundle\Security\Attribute\DemoRestricted;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Admin controller to manage shops.
 */
class ShopController extends PrestaShopAdminController
{
    #[AllShopContext]
    #[AdminSecurity("is_granted('read', request.get('_legacy_controller'))")]
    public function indexAction(
        Request $request,
        ShopFilters $filters,
        MultistoreFeature $multistoreFeature,
        #[Autowire(service: 'prestashop.core.grid.factory.shop')]
        GridFactoryInterface $shopGridFactory,
    ): Response {
        if (!$multistoreFeature->isActive()) {
            return $this->redirectToMultistoreActivation();
        }

        return $this->render('@PrestaShop/Admin/Configure/AdvancedParameters/Shop/index.html.twig', [
            'shopGrid' => $this->presentGrid($shopGridFactory->getGrid($filters)),
            'shopTree' => $this->dispatchQuery(new GetShopTree()),
            'enableSidebar' => true,
            'help_link' => $this->generateSidebarLink($request->attributes->get('_legacy_controller')),
        ]);
    }

    #[AllShopContext]
    #[AdminSecurity("is_granted('create', request.get('_legacy_controller'))", redirectRoute: 'admin_shops_index')]
    public function createAction(
        Request $request,
        MultistoreFeature $multistoreFeature,
        #[Autowire(service: 'prestashop.core.form.identifiable_object.builder.shop_form_builder')]
        FormBuilderInterface $shopFormBuilder,
        #[Autowire(service: 'prestashop.core.form.identifiable_object.handler.shop_form_handler')]
        FormHandlerInterface $shopFormHandler,
    ): Response {
        if (!$multistoreFeature->isActive()) {
            return $this->redirectToMultistoreActivation();
        }

        $shopForm = $shopFormBuilder->getForm(array_filter(['shop_group_id' => $request->query->getInt('shopGroupId')]));
        $shopForm->handleRequest($request);

        try {
            $result = $shopFormHandler->handle($shopForm);

            if (null !== $result->getIdentifiableObjectId()) {
                $this->addFlash('success', $this->trans('Successful creation', [], 'Admin.Notifications.Success'));

                return $this->redirectToRoute('admin_shops_index');
            }
        } catch (Exception $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages()));
        }

        return $this->render('@PrestaShop/Admin/Configure/AdvancedParameters/Shop/create.html.twig', [
            'shopForm' => $shopForm->createView(),
            'shopTree' => $this->dispatchQuery(new GetShopTree()),
            'enableSidebar' => true,
            'help_link' => $this->generateSidebarLink($request->attributes->get('_legacy_controller')),
            'layoutTitle' => $this->trans('Add new shop', [], 'Admin.Shopparameters.Feature'),
        ]);
    }

    #[AllShopContext]
    #[AdminSecurity("is_granted('update', request.get('_legacy_controller'))", redirectRoute: 'admin_shops_index')]
    public function editAction(
        int $shopId,
        Request $request,
        MultistoreFeature $multistoreFeature,
        #[Autowire(service: 'prestashop.core.form.identifiable_object.builder.shop_form_builder')]
        FormBuilderInterface $shopFormBuilder,
        #[Autowire(service: 'prestashop.core.form.identifiable_object.handler.shop_form_handler')]
        FormHandlerInterface $shopFormHandler,
    ): Response {
        if (!$multistoreFeature->isActive()) {
            return $this->redirectToMultistoreActivation();
        }

        try {
            $shopForm = $shopFormBuilder->getFormFor($shopId);
        } catch (Exception $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages()));

            return $this->redirectToRoute('admin_shops_index');
        }

        try {
            $shopForm->handleRequest($request);
            $result = $shopFormHandler->handleFor($shopId, $shopForm);

            if ($result->isSubmitted() && $result->isValid()) {
                $this->addFlash('success', $this->trans('Successful update', [], 'Admin.Notifications.Success'));

                return $this->redirectToRoute('admin_shops_index');
            }
        } catch (Exception $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages()));
        }

        return $this->render('@PrestaShop/Admin/Configure/AdvancedParameters/Shop/edit.html.twig', [
            'shopForm' => $shopForm->createView(),
            'shopTree' => $this->dispatchQuery(new GetShopTree()),
            'enableSidebar' => true,
            'help_link' => $this->generateSidebarLink($request->attributes->get('_legacy_controller')),
            'layoutTitle' => $this->trans('Edit: %value%', ['%value%' => $shopForm->getData()['name']], 'Admin.Actions'),
        ]);
    }

    #[AllShopContext]
    #[DemoRestricted(redirectRoute: 'admin_shops_index')]
    #[AdminSecurity("is_granted('delete', request.get('_legacy_controller'))", redirectRoute: 'admin_shops_index')]
    public function deleteAction(int $shopId, MultistoreFeature $multistoreFeature): RedirectResponse
    {
        if (!$multistoreFeature->isActive()) {
            return $this->redirectToMultistoreActivation();
        }

        try {
            $this->dispatchCommand(new DeleteShopCommand($shopId));
            $this->addFlash('success', $this->trans('Successful deletion', [], 'Admin.Notifications.Success'));
        } catch (ShopException $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages()));
        }

        return $this->redirectToRoute('admin_shops_index');
    }

    /**
     * Search for shops by query.
     *
     * @param string $searchTerm
     *
     * @return JsonResponse
     */
    public function searchAction(string $searchTerm): JsonResponse
    {
        try {
            $result = [];
            $result['shops'] = $this->dispatchQuery(new SearchShops((string) $searchTerm));
            $statusCode = empty($result['shops']) ? Response::HTTP_NOT_FOUND : Response::HTTP_OK;
        } catch (Exception $e) {
            return $this->json(
                ['message' => $this->getErrorMessageForException($e)],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }

        return $this->json($result['shops'], $statusCode);
    }

    private function redirectToMultistoreActivation(): RedirectResponse
    {
        $this->addFlash('error', $this->trans('Access denied.', [], 'Admin.Notifications.Error'));

        return $this->redirectToRoute('admin_preferences');
    }

    private function getErrorMessages(): array
    {
        $notFound = $this->trans('The object cannot be loaded (or found).', [], 'Admin.Notifications.Error');

        return [
            CategoryNotFoundException::class => $notFound,
            ShopGroupNotFoundException::class => $notFound,
            ShopNotFoundException::class => $notFound,
            CannotAddShopException::class => $this->trans('An error occurred while creating the object.', [], 'Admin.Notifications.Error'),
            CannotUpdateShopException::class => [
                CannotUpdateShopException::FAILED_UPDATE => $this->trans('An error occurred while updating the object.', [], 'Admin.Notifications.Error'),
                CannotUpdateShopException::SHOP_GROUP_LOCKED => $this->trans(
                    'You can\'t edit the shop group because the current shop belongs to a group with the "share" option enabled.',
                    [],
                    'Admin.Shopparameters.Help'
                ),
                CannotUpdateShopException::INVALID_TARGET_SHOP_GROUP => $this->trans(
                    'You can only move your shop to a shop group with all "share" options disabled -- or to a shop group with no customers/orders.',
                    [],
                    'Admin.Shopparameters.Notification'
                ),
            ],
            CannotDeleteShopException::class => [
                CannotDeleteShopException::FAILED_DELETE => $this->trans('An error occurred while deleting the object.', [], 'Admin.Notifications.Error'),
                CannotDeleteShopException::DEFAULT_SHOP => $this->trans('You cannot delete this item.', [], 'Admin.Notifications.Error'),
                CannotDeleteShopException::HAS_DEPENDENCIES => $this->trans(
                    'You cannot delete this shop (customer and/or order dependency).',
                    [],
                    'Admin.Shopparameters.Notification'
                ),
            ],
            ShopConstraintException::class => [
                ShopConstraintException::INVALID_NAME => $this->getInvalidFieldMessage($this->trans('Store name', [], 'Admin.Shopparameters.Feature')),
                ShopConstraintException::INVALID_COLOR => $this->getInvalidFieldMessage($this->trans('Color', [], 'Admin.Catalog.Feature')),
                ShopConstraintException::INVALID_THEME => $this->getInvalidFieldMessage($this->trans('Theme', [], 'Admin.Design.Feature')),
                ShopConstraintException::NAME_ALREADY_USED_IN_GROUP => $this->trans(
                    'You cannot have two stores with the same name in the same group.',
                    [],
                    'Admin.Advparameters.Notification'
                ),
                ShopConstraintException::INVALID_ROOT_CATEGORY => $this->getInvalidFieldMessage($this->trans('Category root', [], 'Admin.Catalog.Feature')),
                ShopConstraintException::ROOT_CATEGORY_NOT_ASSOCIATED => $this->trans(
                    'You need to select at least the root category.',
                    [],
                    'Admin.Shopparameters.Notification'
                ),
            ],
        ];
    }

    private function getInvalidFieldMessage(string $fieldName): string
    {
        return $this->trans('The %s field is invalid.', [sprintf('"%s"', $fieldName)], 'Admin.Notifications.Error');
    }
}
