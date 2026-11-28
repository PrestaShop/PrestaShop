<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\Controller\Admin\Configure\AdvancedParameters;

use Exception;
use PrestaShop\PrestaShop\Adapter\Feature\MultistoreFeature;
use PrestaShop\PrestaShop\Core\Domain\Shop\Command\DeleteShopUrlCommand;
use PrestaShop\PrestaShop\Core\Domain\Shop\Command\ToggleShopUrlMainCommand;
use PrestaShop\PrestaShop\Core\Domain\Shop\Command\ToggleShopUrlStatusCommand;
use PrestaShop\PrestaShop\Core\Domain\Shop\Exception\CannotAddShopUrlException;
use PrestaShop\PrestaShop\Core\Domain\Shop\Exception\CannotDeleteShopUrlException;
use PrestaShop\PrestaShop\Core\Domain\Shop\Exception\CannotUpdateShopUrlException;
use PrestaShop\PrestaShop\Core\Domain\Shop\Exception\ShopException;
use PrestaShop\PrestaShop\Core\Domain\Shop\Exception\ShopNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Shop\Exception\ShopUrlConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Shop\Exception\ShopUrlNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Shop\Query\GetShopTree;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\Builder\FormBuilderInterface;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\Handler\FormHandlerInterface;
use PrestaShop\PrestaShop\Core\Grid\GridFactoryInterface;
use PrestaShop\PrestaShop\Core\Search\Filters\ShopUrlFilters;
use PrestaShopBundle\Controller\Admin\PrestaShopAdminController;
use PrestaShopBundle\Controller\Attribute\AllShopContext;
use PrestaShopBundle\Security\Attribute\AdminSecurity;
use PrestaShopBundle\Security\Attribute\DemoRestricted;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

#[AllShopContext]
class ShopUrlController extends PrestaShopAdminController
{
    #[AdminSecurity("is_granted('read', request.get('_legacy_controller'))")]
    public function indexAction(
        Request $request,
        ShopUrlFilters $filters,
        MultistoreFeature $multistoreFeature,
        #[Autowire(service: 'prestashop.core.grid.factory.shop_url')]
        GridFactoryInterface $shopUrlGridFactory,
    ): Response {
        if (!$multistoreFeature->isActive()) {
            return $this->redirectToMultistoreActivation();
        }

        return $this->render('@PrestaShop/Admin/Configure/AdvancedParameters/ShopUrl/index.html.twig', [
            'shopUrlGrid' => $this->presentGrid($shopUrlGridFactory->getGrid($filters)),
            'shopTree' => $this->dispatchQuery(new GetShopTree()),
            'enableSidebar' => true,
            'help_link' => $this->generateSidebarLink($request->attributes->get('_legacy_controller')),
        ]);
    }

    #[AdminSecurity("is_granted('create', request.get('_legacy_controller'))", redirectRoute: 'admin_shop_urls_index')]
    public function createAction(
        Request $request,
        MultistoreFeature $multistoreFeature,
        #[Autowire(service: 'prestashop.core.form.identifiable_object.builder.shop_url_form_builder')]
        FormBuilderInterface $shopUrlFormBuilder,
        #[Autowire(service: 'prestashop.core.form.identifiable_object.handler.shop_url_form_handler')]
        FormHandlerInterface $shopUrlFormHandler,
    ): Response {
        if (!$multistoreFeature->isActive()) {
            return $this->redirectToMultistoreActivation();
        }

        $shopUrlForm = $shopUrlFormBuilder->getForm(array_filter(['shop_id' => $request->query->getInt('shopId')]));
        $shopUrlForm->handleRequest($request);

        try {
            $result = $shopUrlFormHandler->handle($shopUrlForm);

            if (null !== $result->getIdentifiableObjectId()) {
                $this->addFlash('success', $this->trans('Successful creation', [], 'Admin.Notifications.Success'));

                return $this->redirectToRoute('admin_shop_urls_index');
            }
        } catch (Exception $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages($e)));
        }

        return $this->render('@PrestaShop/Admin/Configure/AdvancedParameters/ShopUrl/create.html.twig', [
            'shopUrlForm' => $shopUrlForm->createView(),
            'shopTree' => $this->dispatchQuery(new GetShopTree()),
            'enableSidebar' => true,
            'help_link' => $this->generateSidebarLink($request->attributes->get('_legacy_controller')),
            'layoutTitle' => $this->trans('Add a new URL', [], 'Admin.Advparameters.Feature'),
        ]);
    }

    #[AdminSecurity("is_granted('update', request.get('_legacy_controller'))", redirectRoute: 'admin_shop_urls_index')]
    public function editAction(
        int $shopUrlId,
        Request $request,
        MultistoreFeature $multistoreFeature,
        #[Autowire(service: 'prestashop.core.form.identifiable_object.builder.shop_url_form_builder')]
        FormBuilderInterface $shopUrlFormBuilder,
        #[Autowire(service: 'prestashop.core.form.identifiable_object.handler.shop_url_form_handler')]
        FormHandlerInterface $shopUrlFormHandler,
    ): Response {
        if (!$multistoreFeature->isActive()) {
            return $this->redirectToMultistoreActivation();
        }

        try {
            $shopUrlForm = $shopUrlFormBuilder->getFormFor($shopUrlId);
        } catch (Exception $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages($e)));

            return $this->redirectToRoute('admin_shop_urls_index');
        }

        try {
            $shopUrlForm->handleRequest($request);
            $result = $shopUrlFormHandler->handleFor($shopUrlId, $shopUrlForm);

            if ($result->isSubmitted() && $result->isValid()) {
                $this->addFlash('success', $this->trans('Successful update', [], 'Admin.Notifications.Success'));

                return $this->redirectToRoute('admin_shop_urls_index');
            }
        } catch (Exception $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages($e)));
        }

        return $this->render('@PrestaShop/Admin/Configure/AdvancedParameters/ShopUrl/edit.html.twig', [
            'shopUrlForm' => $shopUrlForm->createView(),
            'shopTree' => $this->dispatchQuery(new GetShopTree()),
            'enableSidebar' => true,
            'help_link' => $this->generateSidebarLink($request->attributes->get('_legacy_controller')),
            'layoutTitle' => $this->trans('Edit: %value%', ['%value%' => $shopUrlForm->getData()['domain']], 'Admin.Actions'),
        ]);
    }

    #[DemoRestricted(redirectRoute: 'admin_shop_urls_index')]
    #[AdminSecurity("is_granted('update', request.get('_legacy_controller'))", redirectRoute: 'admin_shop_urls_index')]
    public function toggleStatusAction(int $shopUrlId): RedirectResponse
    {
        try {
            $this->dispatchCommand(new ToggleShopUrlStatusCommand($shopUrlId));
            $this->addFlash('success', $this->trans('The status has been successfully updated.', [], 'Admin.Notifications.Success'));
        } catch (ShopException $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages($e)));
        }

        return $this->redirectToRoute('admin_shop_urls_index');
    }

    #[DemoRestricted(redirectRoute: 'admin_shop_urls_index')]
    #[AdminSecurity("is_granted('update', request.get('_legacy_controller'))", redirectRoute: 'admin_shop_urls_index')]
    public function toggleMainAction(int $shopUrlId): RedirectResponse
    {
        try {
            $this->dispatchCommand(new ToggleShopUrlMainCommand($shopUrlId));
            $this->addFlash('success', $this->trans('Successful update', [], 'Admin.Notifications.Success'));
        } catch (ShopException $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages($e)));
        }

        return $this->redirectToRoute('admin_shop_urls_index');
    }

    #[DemoRestricted(redirectRoute: 'admin_shop_urls_index')]
    #[AdminSecurity("is_granted('delete', request.get('_legacy_controller'))", redirectRoute: 'admin_shop_urls_index')]
    public function deleteAction(int $shopUrlId): RedirectResponse
    {
        try {
            $this->dispatchCommand(new DeleteShopUrlCommand($shopUrlId));
            $this->addFlash('success', $this->trans('Successful deletion', [], 'Admin.Notifications.Success'));
        } catch (ShopException $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages($e)));
        }

        return $this->redirectToRoute('admin_shop_urls_index');
    }

    private function redirectToMultistoreActivation(): RedirectResponse
    {
        $this->addFlash('error', $this->trans('Access denied.', [], 'Admin.Notifications.Error'));

        return $this->redirectToRoute('admin_preferences');
    }

    private function getErrorMessages(Exception $exception): array
    {
        $cannotDisableMainUrl = $this->trans('You cannot disable the Main URL.', [], 'Admin.Notifications.Error');

        return [
            ShopUrlNotFoundException::class => $this->trans('The object cannot be loaded (or found).', [], 'Admin.Notifications.Error'),
            ShopNotFoundException::class => $this->trans('The object cannot be loaded (or found).', [], 'Admin.Notifications.Error'),
            CannotAddShopUrlException::class => $this->trans('An error occurred while creating the object.', [], 'Admin.Notifications.Error'),
            CannotUpdateShopUrlException::class => $this->trans('An error occurred while updating the object.', [], 'Admin.Notifications.Error'),
            CannotDeleteShopUrlException::class => [
                CannotDeleteShopUrlException::FAILED_DELETE => $this->trans('An error occurred while deleting the object.', [], 'Admin.Notifications.Error'),
                CannotDeleteShopUrlException::MAIN_URL => $this->trans('You cannot delete the main URL of a shop.', [], 'Admin.Advparameters.Notification'),
            ],
            ShopUrlConstraintException::class => [
                ShopUrlConstraintException::INVALID_DOMAIN => $this->getInvalidFieldMessage($this->trans('Domain', [], 'Admin.Advparameters.Feature')),
                ShopUrlConstraintException::INVALID_DOMAIN_SSL => $this->getInvalidFieldMessage($this->trans('SSL Domain', [], 'Admin.Advparameters.Feature')),
                ShopUrlConstraintException::INVALID_PHYSICAL_URI => $this->getInvalidFieldMessage($this->trans('Physical URL', [], 'Admin.Advparameters.Feature')),
                ShopUrlConstraintException::INVALID_VIRTUAL_URI => $this->trans('A shop virtual URL cannot be "%URL%"', ['%URL%' => $exception instanceof ShopUrlConstraintException ? $exception->getInvalidVirtualUri() : ''], 'Admin.Notifications.Error'),
                ShopUrlConstraintException::URL_ALREADY_USED => $this->trans('A shop URL that uses this domain already exists.', [], 'Admin.Notifications.Error'),
                ShopUrlConstraintException::MAIN_URL_MUST_BE_ACTIVE => $cannotDisableMainUrl,
                ShopUrlConstraintException::MAIN_URL_CANNOT_BE_UNSET => $this->trans('You cannot change a main URL to a non-main URL. You have to set another URL as your Main URL for the selected shop.', [], 'Admin.Notifications.Error'),
            ],
        ];
    }

    private function getInvalidFieldMessage(string $fieldName): string
    {
        return $this->trans('The %s field is invalid.', [sprintf('"%s"', $fieldName)], 'Admin.Notifications.Error');
    }
}
