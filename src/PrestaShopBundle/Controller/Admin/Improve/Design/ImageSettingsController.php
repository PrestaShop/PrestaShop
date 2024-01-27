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

namespace PrestaShopBundle\Controller\Admin\Improve\Design;

use Exception;
use PrestaShop\PrestaShop\Core\Domain\ImageSettings\Command\BulkDeleteImageTypeCommand;
use PrestaShop\PrestaShop\Core\Domain\ImageSettings\Command\DeleteImagesFromTypeCommand;
use PrestaShop\PrestaShop\Core\Domain\ImageSettings\Command\DeleteImageTypeCommand;
use PrestaShop\PrestaShop\Core\Domain\ImageSettings\Command\RegenerateThumbnailsCommand;
use PrestaShop\PrestaShop\Core\Domain\ImageSettings\Exception\ImageTypeNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\ImageSettings\Query\GetImageTypeForEditing;
use PrestaShop\PrestaShop\Core\Domain\ImageSettings\QueryResult\EditableImageType;
use PrestaShop\PrestaShop\Core\Search\Filters\ImageTypeFilters;
use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use PrestaShopBundle\Form\Admin\Improve\Design\ImageSettings\DeleteImageTypeType;
use PrestaShopBundle\Form\Admin\Improve\Design\ImageSettings\RegenerateThumbnailsType;
use PrestaShopBundle\Security\Attribute\AdminSecurity;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Responsible for Image Settings actions in Back Office
 */
class ImageSettingsController extends FrameworkBundleAdminController
{
    /**
     * Displays image settings listing page.
     *
     * @param Request $request
     * @param ImageTypeFilters $filters
     *
     * @return Response
     */
    #[AdminSecurity("is_granted('read', request.get('_legacy_controller'))")]
    public function indexAction(Request $request, ImageTypeFilters $filters): Response
    {
        // Get image type grid
        $imageTypeGridFactory = $this->get('prestashop.core.grid.factory.image_type');
        $imageTypeGrid = $imageTypeGridFactory->getGrid($filters);

        // Create form for deleting image type if needed (in modal)
        $deleteForm = $this->createForm(DeleteImageTypeType::class);

        // Create form to set some image settings
        $configFormBuilder = $this->get('prestashop.admin.image_settings.form_handler');
        $configForm = $configFormBuilder->getForm();

        // Create form to regenerate thumbnails
        $regenThumbnailsForm = $this->createForm(RegenerateThumbnailsType::class);

        return $this->render('@PrestaShop/Admin/Improve/Design/ImageSettings/index.html.twig', [
            'help_link' => $this->generateSidebarLink($request->attributes->get('_legacy_controller')),
            'imageTypeGrid' => $this->presentGrid($imageTypeGrid),
            'enableSidebar' => true,
            'deleteImageTypeForm' => $deleteForm->createView(),
            'layoutHeaderToolbarBtn' => [
                'add' => [
                    'href' => $this->generateUrl('admin_image_settings_create'),
                    'desc' => $this->trans('Add new image type', 'Admin.Design.Feature'),
                    'icon' => 'add_circle_outline',
                ],
            ],
            'configForm' => $configForm->createView(),
            'regenThumbnailsForm' => $regenThumbnailsForm->createView(),
        ]);
    }

    #[AdminSecurity("is_granted('update', request.get('_legacy_controller'))")]
    public function saveSettingsAction(Request $request): Response
    {
        try {
            // Create form to set some image settings
            $configFormHandler = $this->get('prestashop.admin.image_settings.form_handler');
            $configForm = $configFormHandler->getForm();
            $configForm->handleRequest($request);

            if ($configForm->isSubmitted()) {
                if ($configForm->isValid()) {
                    $configFormHandler->save($configForm->getData());
                    $this->addFlash('success', $this->trans('The settings have been successfully updated.', 'Admin.Notifications.Success'));
                } else {
                    $this->addFlashFormErrors($configForm);
                }
            }
        } catch (Exception $e) {
            $this->addFlash('error', $e->getMessage());
        }

        return $this->redirectToRoute('admin_image_settings_index');
    }

    /**
     * Show "Add new" image type form and handles its submit.
     *
     * @param Request $request
     *
     * @return Response
     */
    #[AdminSecurity("is_granted('create', request.get('_legacy_controller'))", redirectRoute: 'admin_image_settings_index')]
    public function createAction(Request $request): Response
    {
        $formBuilder = $this->get('prestashop.core.form.identifiable_object.builder.image_type_form_builder');
        $formHandler = $this->get('prestashop.core.form.identifiable_object.handler.image_type_form_handler');

        $form = $formBuilder->getForm();
        $form->handleRequest($request);

        try {
            $handleResult = $formHandler->handle($form);

            if ($handleResult->isSubmitted() && $handleResult->isValid()) {
                $this->addFlash('success', $this->trans('Successful creation', 'Admin.Notifications.Success'));

                return $this->redirectToRoute('admin_image_settings_index');
            }
        } catch (Exception $e) {
            $this->addFlash('error', $e->getMessage());
        }

        return $this->render('@PrestaShop/Admin/Improve/Design/ImageSettings/ImageType/create.html.twig', [
            'form' => $form->createView(),
            'help_link' => $this->generateSidebarLink($request->attributes->get('_legacy_controller')),
            'enableSidebar' => true,
            'layoutTitle' => $this->trans('Add new', 'Admin.Actions'),
        ]);
    }

    /**
     * Displays image type for edit and handles its submit.
     *
     * @param int $imageTypeId
     * @param Request $request
     *
     * @return Response
     */
    #[AdminSecurity("is_granted('update', request.get('_legacy_controller'))", redirectRoute: 'admin_image_settings_index')]
    public function editAction(int $imageTypeId, Request $request): Response
    {
        try {
            /** @var EditableImageType $editableImageType */
            $editableImageType = $this->getQueryBus()->handle(new GetImageTypeForEditing($imageTypeId));

            $formBuilder = $this->get('prestashop.core.form.identifiable_object.builder.image_type_form_builder');
            $formHandler = $this->get('prestashop.core.form.identifiable_object.handler.image_type_form_handler');

            $form = $formBuilder->getFormFor($imageTypeId);
            $form->handleRequest($request);

            $result = $formHandler->handleFor($imageTypeId, $form);

            if ($result->isSubmitted() && $result->isValid()) {
                $this->addFlash('success', $this->trans('Update successful', 'Admin.Notifications.Success'));

                return $this->redirectToRoute('admin_image_settings_index');
            }
        } catch (Exception $e) {
            $this->addFlash('error', $e->getMessage());

            if ($e instanceof ImageTypeNotFoundException) {
                return $this->redirectToRoute('admin_image_settings_index');
            }
        }

        return $this->render('@PrestaShop/Admin/Improve/Design/ImageSettings/ImageType/edit.html.twig', [
            'form' => $form->createView(),
            'help_link' => $this->generateSidebarLink($request->attributes->get('_legacy_controller')),
            'enableSidebar' => true,
            'layoutTitle' => $this->trans('Edit: %value%', 'Admin.Actions', ['%value%' => $editableImageType->getName()]),
        ]);
    }

    /**
     * Delete image type.
     *
     * @param int $imageTypeId
     *
     * @return RedirectResponse
     */
    #[AdminSecurity("is_granted('delete', request.get('_legacy_controller'))", redirectRoute: 'admin_image_settings_index')]
    public function deleteAction(Request $request, int $imageTypeId): RedirectResponse
    {
        try {
            $deleteForm = $this->createForm(DeleteImageTypeType::class);
            $deleteForm->handleRequest($request);

            // If we need to delete images files too
            if ($deleteForm->get('delete_images_files_too')->getNormData()) {
                $this->getCommandBus()->handle(new DeleteImagesFromTypeCommand($imageTypeId));
            }

            // Delete image type
            $this->getCommandBus()->handle(new DeleteImageTypeCommand($imageTypeId));
            $this->addFlash('success', $this->trans('Successful deletion', 'Admin.Notifications.Success'));
        } catch (Exception $e) {
            $this->addFlash('error', $e->getMessage());
        }

        return $this->redirectToRoute('admin_image_settings_index');
    }

    /**
     * Deletes image type in bulk action
     *
     * @param Request $request
     *
     * @return RedirectResponse
     */
    #[AdminSecurity("is_granted('delete', request.get('_legacy_controller'))", redirectRoute: 'admin_image_settings_index')]
    public function bulkDeleteAction(Request $request): RedirectResponse
    {
        $ids = $this->getBulkIdsFromRequest($request);

        try {
            $this->getCommandBus()->handle(new BulkDeleteImageTypeCommand($ids));

            $this->addFlash(
                'success',
                $this->trans('The selection has been successfully deleted.', 'Admin.Notifications.Success')
            );
        } catch (Exception $e) {
            $this->addFlash('error', $e->getMessage());
        }

        return $this->redirectToRoute('admin_image_settings_index');
    }

    /**
     * Collects IDs from request.
     *
     * @param Request $request
     *
     * @return array
     */
    private function getBulkIdsFromRequest(Request $request): array
    {
        $ids = $request->request->all('image_type_bulk');

        return array_map('intval', $ids);
    }

    /**
     * Regenerate thumbnails.
     *
     * @param Request $request
     *
     * @return RedirectResponse
     */
    #[AdminSecurity("is_granted('update', request.get('_legacy_controller'))", redirectRoute: 'admin_image_settings_index')]
    public function regenerateThumbnailsAction(Request $request): RedirectResponse
    {
        try {
            $regenThumbnailsForm = $this->createForm(RegenerateThumbnailsType::class);
            $regenThumbnailsForm->handleRequest($request);

            $this->getCommandBus()->handle(new RegenerateThumbnailsCommand(
                $regenThumbnailsForm->get('image')->getData(),
                $regenThumbnailsForm->get('image-type')->getData(),
                $regenThumbnailsForm->get('erase-previous-images')->getData()
             ));
            $this->addFlash('success', $this->trans('The thumbnails were successfully regenerated.', 'Admin.Notifications.Success'));
        } catch (Exception $e) {
            $this->addFlash('error', $e->getMessage());
        }

        return $this->redirectToRoute('admin_image_settings_index');
    }
}
