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
use PrestaShop\PrestaShop\Core\Domain\ImageType\Command\BulkDeleteImageTypeCommand;
use PrestaShop\PrestaShop\Core\Domain\ImageType\Command\DeleteImageTypeCommand;
use PrestaShop\PrestaShop\Core\Domain\ImageType\Exception\DeleteImageTypeException;
use PrestaShop\PrestaShop\Core\Domain\ImageType\Exception\ImageTypeException;
use PrestaShop\PrestaShop\Core\Domain\ImageType\Exception\ImageTypeNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\ImageType\Query\GetImageTypeForEditing;
use PrestaShop\PrestaShop\Core\Domain\ImageType\QueryResult\EditableImageType;
use PrestaShop\PrestaShop\Core\Grid\Definition\Factory\ImageTypeGridDefinitionFactory;
use PrestaShop\PrestaShop\Core\Search\Filters\ImageTypeFilters;
use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use PrestaShopBundle\Security\Annotation\AdminSecurity;
use PrestaShopBundle\Service\Grid\ResponseBuilder;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class ImageSettingsController manages "Improve > Design > Image Settings" pages.
 */
class ImageSettingsController extends FrameworkBundleAdminController
{
    /**
     * Show image types index page.
     *
     * @AdminSecurity("is_granted('read', request.get('_legacy_controller'))")
     *
     * @param ImageTypeFilters $filters
     * @param Request $request
     *
     * @return Response
     */
    public function indexAction(ImageTypeFilters $filters, Request $request): Response
    {
        $gridFactory = $this->get('prestashop.core.grid.factory.image_type');
        $grid = $gridFactory->getGrid($filters);

        return $this->render('@PrestaShop/Admin/Improve/Design/ImageSettings/index.html.twig', [
            'imageTypeGrid' => $this->presentGrid($grid),
            'enableSidebar' => true,
            'help_link' => $this->generateSidebarLink($request->attributes->get('_legacy_controller')),
        ]);
    }

    /**
     * Provides filters functionality.
     *
     * @AdminSecurity("is_granted(['read'], request.get('_legacy_controller'))")
     *
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function searchAction(Request $request): RedirectResponse
    {
        /** @var ResponseBuilder $responseBuilder */
        $responseBuilder = $this->get('prestashop.bundle.grid.response_builder');

        return $responseBuilder->buildSearchResponse(
            $this->get('prestashop.core.grid.definition.factory.image_type'),
            $request,
            ImageTypeGridDefinitionFactory::GRID_ID,
            'admin_image_settings_index'
        );
    }

    /**
     * Show image type creation form page and handle its submit.
     *
     * @AdminSecurity("is_granted('create', request.get('_legacy_controller'))")
     *
     * @param Request $request
     *
     * @return Response
     */
    public function createAction(Request $request): Response
    {
        $imageTypeFormHandler = $this->get('prestashop.core.form.identifiable_object.handler.image_type_form_handler');
        $imageTypeFormBuilder = $this->get('prestashop.core.form.identifiable_object.builder.image_type_form_builder');

        $imageTypeForm = $imageTypeFormBuilder->getForm();
        $imageTypeForm->handleRequest($request);

        try {
            $result = $imageTypeFormHandler->handle($imageTypeForm);

            if (null !== $result->getIdentifiableObjectId()) {
                $this->addFlash('success', $this->trans('Successful creation.', 'Admin.Notifications.Success'));

                return $this->redirectToRoute('admin_image_settings_index');
            }
        } catch (Exception $exception) {
            $this->addFlash('error', $this->getErrorMessageForException($exception, $this->getErrorMessages()));
        }

        return $this->render('PrestaShopBundle:Admin/Improve/Design/ImageSettings:create.html.twig', [
            'imageTypeForm' => $imageTypeForm->createView(),
            'help_link' => $this->generateSidebarLink($request->attributes->get('_legacy_controller')),
            'enableSidebar' => true,
        ]);
    }

    /**
     * Handles image type edit
     *
     * @AdminSecurity(
     *     "is_granted('update', request.get('_legacy_controller'))",
     *     redirectRoute="admin_image_settings_index",
     * )
     *
     * @param Request $request
     * @param int $imageTypeId
     *
     * @return Response
     */
    public function editAction(Request $request, int $imageTypeId): Response
    {
        $imageTypeFormBuilder = $this->get('prestashop.core.form.identifiable_object.builder.image_type_form_builder');
        $imageTypeFormHandler = $this->get('prestashop.core.form.identifiable_object.handler.image_type_form_handler');

        try {
            $imageTypeForm = $imageTypeFormBuilder->getFormFor($imageTypeId);
        } catch (Exception $exception) {
            $this->addFlash('error', $this->getErrorMessageForException($exception, $this->getErrorMessages()));
            return $this->redirectToRoute('admin_image_settings_index');
        }

        try {
            $imageTypeForm->handleRequest($request);
            $result = $imageTypeFormHandler->handleFor($imageTypeId, $imageTypeForm);

            if ($result->isSubmitted() && $result->isValid()) {
                $this->addFlash('success', $this->trans('Successful update.', 'Admin.Notifications.Success'));
                return $this->redirectToRoute('admin_image_settings_index');
            }
        } catch (Exception $exception) {
            $this->addFlash('error', $this->getErrorMessageForException($exception, $this->getErrorMessages()));

            if ($exception instanceof ImageTypeNotFoundException) {
                return $this->redirectToRoute('admin_image_settings_index');
            }
        }

        /** @var EditableImageType $editableImageType */
        $editableImageType = $this->getQueryBus()->handle(new GetImageTypeForEditing($imageTypeId));

        return $this->render('@PrestaShop/Admin/Improve/Design/ImageSettings/edit.html.twig', [
            'imageTypeForm' => $imageTypeForm->createView(),
            'imageTypeName' => $editableImageType->getName(),
            'help_link' => $this->generateSidebarLink($request->attributes->get('_legacy_controller')),
            'enableSidebar' => true,
        ]);
    }

    /**
     * Deletes image type
     *
     * @AdminSecurity(
     *     "is_granted('delete', request.get('_legacy_controller'))",
     *     redirectRoute="admin_image_settings_index",
     * )
     *
     * @param int $imageTypeId
     *
     * @return RedirectResponse
     */
    public function deleteAction(int $imageTypeId): RedirectResponse
    {
        try {
            $this->getCommandBus()->handle(new DeleteImageTypeCommand($imageTypeId));
            $this->addFlash(
                'success',
                $this->trans('Successful deletion.', 'Admin.Notifications.Success')
            );
        } catch (ImageTypeException $exception) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages()));
        }

        return $this->redirectToRoute('admin_image_settings_index');
    }

    /**
     * Deletes image types in bulk action
     *
     * @AdminSecurity(
     *     "is_granted('delete', request.get('_legacy_controller'))",
     *     redirectRoute="admin_image_settings_index"
     * )
     *
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function bulkDeleteAction(Request $request): RedirectResponse
    {
        $imageTypeIds = $this->getBulkImageTypeIdsFromRequest($request);

        try {
            $this->getCommandBus()->handle(new BulkDeleteImageTypeCommand($imageTypeIds));
            $this->addFlash(
                'success',
                $this->trans('The selection has been successfully deleted.', 'Admin.Notifications.Success')
            );
        } catch (ImageTypeException $exception) {
            $this->addFlash('error', $this->getErrorMessageForException($exception, $this->getErrorMessages()));
        }

        return $this->redirectToRoute('admin_image_settings_index');
    }

    /**
     * Get image type IDs from request for bulk action.
     *
     * @param Request $request
     *
     * @return int[]
     */
    private function getBulkImageTypeIdsFromRequest(Request $request): array
    {
        $imageTypeIds = $request->request->get('image_type_bulk');

        if (!is_array($imageTypeIds)) {
            return [];
        }

        return array_map('intval', $imageTypeIds);
    }

    private function getErrorMessages(): array
    {
        return [
            ImageTypeNotFoundException::class => $this->trans(
                'The object cannot be loaded (or found)',
                'Admin.Notifications.Error'
            ),
            DeleteImageTypeException::class => [
                DeleteImageTypeException::FAILED_DELETE => $this->trans(
                    'An error occurred while deleting the object.',
                    'Admin.Notifications.Error'
                ),
                DeleteImageTypeException::FAILED_BULK_DELETE => $this->trans(
                    'An error occurred while deleting this selection.',
                    'Admin.Notifications.Error'
                ),
            ],
        ];
    }
}
