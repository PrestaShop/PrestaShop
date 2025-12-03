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
use PrestaShop\PrestaShop\Core\Domain\Tag\Command\BulkDeleteTagCommand;
use PrestaShop\PrestaShop\Core\Domain\Tag\Command\DeleteTagCommand;
use PrestaShop\PrestaShop\Core\Domain\Tag\Exception\CannotAddTagException;
use PrestaShop\PrestaShop\Core\Domain\Tag\Exception\CannotUpdateTagException;
use PrestaShop\PrestaShop\Core\Domain\Tag\Exception\TagException;
use PrestaShop\PrestaShop\Core\Domain\Tag\Exception\TagNotFoundException;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\Builder\FormBuilderInterface;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\Handler\FormHandlerInterface;
use PrestaShop\PrestaShop\Core\Grid\GridFactoryInterface;
use PrestaShop\PrestaShop\Core\Search\Filters\TagFilters;
use PrestaShopBundle\Controller\Admin\PrestaShopAdminController;
use PrestaShopBundle\Security\Attribute\AdminSecurity;
use PrestaShopBundle\Security\Attribute\DemoRestricted;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Controller responsible for "Configure > Shop Parameters > Search > Tags" page.
 */
class TagController extends PrestaShopAdminController
{
    #[AdminSecurity("is_granted('read', request.get('_legacy_controller'))", message: 'Access denied.')]
    public function indexAction(
        Request $request,
        TagFilters $filters,
        #[Autowire(service: 'PrestaShop\PrestaShop\Core\Grid\Factory\TagFactory')]
        GridFactoryInterface $tagGridFactory,
    ): Response {
        $tagGrid = $tagGridFactory->getGrid($filters);

        return $this->render('@PrestaShop/Admin/Configure/ShopParameters/Tags/index.html.twig', [
            'tagGrid' => $this->presentGrid($tagGrid),
            'layoutTitle' => $this->trans('Tags', [], 'Admin.Navigation.Menu'),
            'layoutHeaderToolbarBtn' => $this->getToolbarButtons(),
            'help_link' => $this->generateSidebarLink($request->attributes->get('_legacy_controller')),
            'enableSidebar' => true,
        ]);
    }

    #[AdminSecurity("is_granted('create', request.get('_legacy_controller'))", redirectRoute: 'admin_tags_index')]
    public function createAction(
        Request $request,
        #[Autowire(service: 'prestashop.core.form.identifiable_object.builder.tag_form_builder')]
        FormBuilderInterface $formBuilder,
        #[Autowire(service: 'prestashop.core.form.identifiable_object.tag_form_handler')]
        FormHandlerInterface $formHandler,
    ): Response {
        $tagForm = $formBuilder->getForm();
        $tagForm->handleRequest($request);

        try {
            $handlerResult = $formHandler->handle($tagForm);
            if (null !== $handlerResult->getIdentifiableObjectId()) {
                $this->addFlash('success', $this->trans('Successful creation', [], 'Admin.Notifications.Success'));

                return $this->redirectToRoute('admin_tags_index');
            }
        } catch (Exception $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages()));
        }

        return $this->render(
            '@PrestaShop/Admin/Configure/ShopParameters/Tags/create.html.twig',
            [
                'layoutTitle' => $this->trans('New tag', [], 'Admin.Navigation.Menu'),
                'tagForm' => $tagForm->createView(),
                'multistoreIsUsed' => $this->getShopContext()->isMultiShopUsed(),
                'multistoreInfoTip' => $this->trans(
                    'Note that this page is available in all shops context only, this is why your context has just switched.',
                    [],
                    'Admin.Notifications.Info'
                ),
            ]
        );
    }

    #[AdminSecurity("is_granted('update', request.get('_legacy_controller'))", redirectRoute: 'admin_tags_index')]
    public function editAction(
        Request $request,
        int $tagId,
        #[Autowire(service: 'prestashop.core.form.identifiable_object.builder.tag_form_builder')]
        FormBuilderInterface $formBuilder,
        #[Autowire(service: 'prestashop.core.form.identifiable_object.tag_form_handler')]
        FormHandlerInterface $formHandler,
    ): Response {
        $tagForm = $formBuilder->getFormFor($tagId);
        $tagForm->handleRequest($request);

        try {
            $handlerResult = $formHandler->handleFor($tagId, $tagForm);

            if (null !== $handlerResult->getIdentifiableObjectId()) {
                $this->addFlash('success', $this->trans('Successful update', [], 'Admin.Notifications.Success'));

                return $this->redirectToRoute('admin_tags_edit', ['tagId' => $tagId]);
            }
        } catch (Exception $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages()));
        }

        $formData = $tagForm->getData();

        return $this->render('@PrestaShop/Admin/Configure/ShopParameters/Tags/edit.html.twig', [
            'layoutTitle' => $this->trans('Editing tag "%name%"', ['%name%' => $formData['name']], 'Admin.Navigation.Menu'),
            'tagForm' => $tagForm->createView(),
            'multistoreIsUsed' => $this->getShopContext()->isMultiShopUsed(),
            'multistoreInfoTip' => $this->trans(
                'Note that this page is available in all shops context only, this is why your context has just switched.',
                [],
                'Admin.Notifications.Info'
            ),
        ]);
    }

    #[DemoRestricted(redirectRoute: 'admin_tags_index')]
    #[AdminSecurity("is_granted('delete', request.get('_legacy_controller'))", message: 'You need permission to delete this.', redirectRoute: 'admin_tags_index')]
    public function deleteAction(int $tagId): RedirectResponse
    {
        try {
            $this->dispatchCommand(new DeleteTagCommand($tagId));
            $this->addFlash(
                'success',
                $this->trans('Successful deletion', [], 'Admin.Notifications.Success')
            );
        } catch (TagException $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages()));
        }

        return $this->redirectToRoute('admin_tags_index');
    }

    #[DemoRestricted(redirectRoute: 'admin_tags_index')]
    #[AdminSecurity("is_granted('delete', request.get('_legacy_controller'))", redirectRoute: 'admin_tags_index')]
    public function bulkDeleteAction(Request $request): RedirectResponse
    {
        $tagIds = $this->getBulkTagsFromRequest($request);

        try {
            $this->dispatchCommand(new BulkDeleteTagCommand($tagIds));
            $this->addFlash(
                'success',
                $this->trans('Successful deletion', [], 'Admin.Notifications.Success')
            );
        } catch (TagException $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages()));
        }

        return $this->redirectToRoute('admin_tags_index');
    }

    private function getBulkTagsFromRequest(Request $request): array
    {
        $tagIds = $request->request->all('tag_tag_bulk');

        foreach ($tagIds as $i => $tagId) {
            $tagIds[$i] = (int) $tagId;
        }

        return $tagIds;
    }

    /**
     * @return array
     */
    protected function getToolbarButtons(): array
    {
        $toolbarButtons = [];

        $toolbarButtons['addTag'] = [
            'href' => $this->generateUrl('admin_tags_create'),
            'desc' => $this->trans('Add new tag', [], 'Admin.Actions'),
            'icon' => 'add_circle_outline',
            'class' => 'btn-primary',
        ];

        return $toolbarButtons;
    }

    /**
     * Provides translated error messages for exceptions
     *
     * @return array
     */
    private function getErrorMessages()
    {
        return [
            TagNotFoundException::class => $this->trans(
                'The object cannot be loaded (or found).',
                [],
                'Admin.Notifications.Error'
            ),
            CannotAddTagException::class => $this->trans(
                'An error occurred while creating the tag.',
                [],
                'Admin.Advparameters.Notification'
            ),
            CannotUpdateTagException::class => $this->trans(
                'An error occurred while updating the tag.',
                [],
                'Admin.Advparameters.Notification'
            ),
        ];
    }
}
