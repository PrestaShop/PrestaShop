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
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace PrestaShopBundle\Controller\Admin\Sell\Catalog;

use Exception;
use PrestaShop\PrestaShop\Core\Domain\Attachment\Command\BulkDeleteAttachmentsCommand;
use PrestaShop\PrestaShop\Core\Domain\Attachment\Command\DeleteAttachmentCommand;
use PrestaShop\PrestaShop\Core\Domain\Attachment\Exception\AttachmentConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Attachment\Exception\AttachmentNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Attachment\Exception\AttachmentUploadFailedException;
use PrestaShop\PrestaShop\Core\Domain\Attachment\Exception\BulkDeleteAttachmentsException;
use PrestaShop\PrestaShop\Core\Domain\Attachment\Exception\CannotAddAttachmentException;
use PrestaShop\PrestaShop\Core\Domain\Attachment\Exception\CannotUnlinkAttachmentException;
use PrestaShop\PrestaShop\Core\Domain\Attachment\Exception\CannotUpdateAttachmentException;
use PrestaShop\PrestaShop\Core\Domain\Attachment\Exception\DeleteAttachmentException;
use PrestaShop\PrestaShop\Core\Domain\Attachment\Exception\EmptyFileException;
use PrestaShop\PrestaShop\Core\Domain\Attachment\Exception\EmptySearchException;
use PrestaShop\PrestaShop\Core\Domain\Attachment\Query\GetAttachment;
use PrestaShop\PrestaShop\Core\Domain\Attachment\Query\GetAttachmentForEditing;
use PrestaShop\PrestaShop\Core\Domain\Attachment\Query\GetAttachmentInformation;
use PrestaShop\PrestaShop\Core\Domain\Attachment\Query\SearchAttachment;
use PrestaShop\PrestaShop\Core\Domain\Attachment\QueryResult\Attachment;
use PrestaShop\PrestaShop\Core\Domain\Attachment\QueryResult\AttachmentInformation;
use PrestaShop\PrestaShop\Core\Domain\Attachment\QueryResult\EditableAttachment;
use PrestaShop\PrestaShop\Core\Search\Filters\AttachmentFilters;
use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use PrestaShopBundle\Security\Annotation\AdminSecurity;
use PrestaShopBundle\Security\Annotation\DemoRestricted;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class is responsible for "Sell > Catalog > Files" page.
 */
class AttachmentController extends FrameworkBundleAdminController
{
    /**
     * @AdminSecurity("is_granted('read', request.get('_legacy_controller'))")
     */
    public function indexAction(Request $request, AttachmentFilters $filters): Response
    {
        $attachmentGridFactory = $this->get('prestashop.core.grid.factory.attachment');
        $attachmentGrid = $attachmentGridFactory->getGrid($filters);

        return $this->render('@PrestaShop/Admin/Sell/Catalog/Attachment/index.html.twig', [
            'help_link' => $this->generateSidebarLink($request->attributes->get('_legacy_controller')),
            'attachmentGrid' => $this->presentGrid($attachmentGrid),
            'enableSidebar' => true,
            'layoutHeaderToolbarBtn' => $this->getAttachmentToolbarButtons(),
        ]);
    }

    /**
     * Show "Add new" form and handle form submit.
     *
     * @AdminSecurity(
     *     "is_granted('create', request.get('_legacy_controller'))",
     *     redirectRoute="admin_attachments_index",
     *     message="You do not have permission to create this."
     * )
     *
     * @return Response
     */
    public function createAction(Request $request)
    {
        $attachmentFormBuilder = $this->get(
            'prestashop.core.form.identifiable_object.builder.attachment_form_builder'
        );
        $attachmentFormHandler = $this->get(
            'prestashop.core.form.identifiable_object.handler.attachment_form_handler'
        );

        $attachmentForm = $attachmentFormBuilder->getForm();
        $attachmentForm->handleRequest($request);

        try {
            $handlerResult = $attachmentFormHandler->handle($attachmentForm);

            if ($handlerResult->isSubmitted() && $handlerResult->isValid()) {
                $this->addFlash('success', $this->trans('Successful creation', 'Admin.Notifications.Success'));

                if ($request->get('saveAndStay') !== null) {
                    // Keep the initial query parameters (to keep liteDisplay or saveAndStay for example)
                    $parameters = array_merge([
                        'attachmentId' => $handlerResult->getIdentifiableObjectId(),
                    ], $request->query->all());

                    return $this->redirectToRoute('admin_attachments_edit', $parameters);
                }

                return $this->redirectToRoute('admin_attachments_index');
            }
        } catch (Exception $e) {
            $message = $this->getErrorMessageForException($e, $this->getErrorMessages($e));
            $this->addFlash('error', $message);
        }

        return $this->render('@PrestaShop/Admin/Sell/Catalog/Attachment/add.html.twig', [
            'lightDisplay' => $request->query->has('liteDisplaying'),
            'enableSidebar' => true,
            'layoutTitle' => $this->trans('Add new file', 'Admin.Catalog.Feature'),
            'attachmentForm' => $attachmentForm->createView(),
            'help_link' => $this->generateSidebarLink($request->attributes->get('_legacy_controller')),
        ]);
    }

    /**
     * Show & process attachment editing.
     *
     * @AdminSecurity(
     *     "is_granted('update', request.get('_legacy_controller'))",
     *     redirectRoute="admin_attachments_index",
     *     message="You do not have permission to edit this."
     * )
     *
     * @param int $attachmentId
     *
     * @return Response
     */
    public function editAction($attachmentId, Request $request)
    {
        try {
            /** @var EditableAttachment $attachmentInformation */
            $attachmentInformation = $this->getQueryBus()->handle(new GetAttachmentForEditing((int) $attachmentId));

            $attachmentFormBuilder = $this->get(
                'prestashop.core.form.identifiable_object.builder.attachment_form_builder'
            );
            $attachmentFormHandler = $this->get(
                'prestashop.core.form.identifiable_object.handler.attachment_form_handler'
            );

            $attachmentForm = $attachmentFormBuilder->getFormFor((int) $attachmentId);

            $attachmentForm->handleRequest($request);
            $result = $attachmentFormHandler->handleFor((int) $attachmentId, $attachmentForm);

            if ($result->isSubmitted() && $result->isValid()) {
                $this->addFlash('success', $this->trans('Successful update', 'Admin.Notifications.Success'));

                return $this->redirectToRoute('admin_attachments_index');
            }
        } catch (CannotUnlinkAttachmentException $e) {
            $this->addFlash('warning', $this->getErrorMessageForException($e, $this->getErrorMessages($e)));

            return $this->redirectToRoute('admin_attachments_index');
        } catch (Exception $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages($e)));

            if ($e instanceof AttachmentNotFoundException) {
                return $this->redirectToRoute('admin_attachments_index');
            }
        }

        if (!isset($attachmentInformation) || !isset($attachmentForm)) {
            return $this->redirectToRoute('admin_attachments_index');
        }

        $names = $attachmentInformation->getName();

        return $this->render('@PrestaShop/Admin/Sell/Catalog/Attachment/edit.html.twig', [
            'enableSidebar' => true,
            'layoutTitle' => $this->trans(
                'Edit: %value%',
                'Admin.Catalog.Feature',
                ['%value%' => reset($names)]
            ),
            'attachmentForm' => $attachmentForm->createView(),
            'attachmentId' => $attachmentId,
            'help_link' => $this->generateSidebarLink($request->attributes->get('_legacy_controller')),
        ]);
    }

    /**
     * View attachment.
     *
     * @AdminSecurity(
     *     "is_granted('read', request.get('_legacy_controller'))",
     *     redirectRoute="admin_attachments_index",
     *     message="You do not have permission to edit this."
     * )
     */
    public function viewAction(int $attachmentId): Response
    {
        try {
            /** @var Attachment $attachment */
            $attachment = $this->getCommandBus()->handle(new GetAttachment((int) $attachmentId));

            return $this->file($attachment->getPath(), $attachment->getName());
        } catch (Exception $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages($e)));
        }

        return $this->redirectToRoute('admin_attachments_index');
    }

    /**
     * Deletes attachment
     *
     * @AdminSecurity("is_granted('delete', request.get('_legacy_controller'))", redirectRoute="admin_attachments_index")
     * @DemoRestricted(redirectRoute="admin_attachments_index")
     */
    public function deleteAction(int $attachmentId): RedirectResponse
    {
        try {
            $this->getCommandBus()->handle(new DeleteAttachmentCommand((int) $attachmentId));
            $this->addFlash(
                'success',
                $this->trans('Successful deletion', 'Admin.Notifications.Success')
            );
        } catch (Exception $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages($e)));
        }

        return $this->redirectToRoute('admin_attachments_index');
    }

    /**
     * Delete attachments in bulk action.
     *
     * @AdminSecurity(
     *     "is_granted('delete', request.get('_legacy_controller'))",
     *     redirectRoute="admin_attachments_index",
     *     message="You do not have permission to delete this."
     * )
     */
    public function deleteBulkAction(Request $request): RedirectResponse
    {
        $attachmentIds = $this->getBulkAttachmentsFromRequest($request);

        try {
            $this->getCommandBus()->handle(new BulkDeleteAttachmentsCommand($attachmentIds));
            $this->addFlash(
                'success',
                $this->trans('The selection has been successfully deleted.', 'Admin.Notifications.Success')
            );
        } catch (Exception $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages($e)));
        }

        return $this->redirectToRoute('admin_attachments_index');
    }

    /**
     * @AdminSecurity("is_granted('read', 'AdminProducts') || is_granted('read', 'AdminAttachments')")
     *
     * @param int $attachmentId
     *
     * @return JsonResponse
     */
    public function getAttachmentInfoAction(int $attachmentId): JsonResponse
    {
        $attachmentInfo = $this->getQueryBus()->handle(new GetAttachmentInformation($attachmentId));

        return $this->json(['attachmentInfo' => $this->presentAttachmentInfo($attachmentInfo)]);
    }

    /**
     * @AdminSecurity("is_granted('read', 'AdminProducts') || is_granted('read', 'AdminAttachments')")
     *
     * @param string $searchPhrase
     *
     * @return JsonResponse
     */
    public function searchAction(string $searchPhrase): JsonResponse
    {
        try {
            /** @var AttachmentInformation[] $attachments */
            $attachments = $this->getCommandBus()->handle(new SearchAttachment($searchPhrase));
        } catch (EmptySearchException $e) {
            return $this->json(
                [$e, 'message' => $this->getErrorMessageForException($e, $this->getErrorMessages($e))],
                Response::HTTP_NOT_FOUND
            );
        }

        $result = [];
        foreach ($attachments as $attachment) {
            $result[] = $this->presentAttachmentInfo($attachment);
        }

        return $this->json($result);
    }

    /**
     * @param AttachmentInformation $productAttachmentInfo
     *
     * @return array<string, mixed>
     */
    private function presentAttachmentInfo(AttachmentInformation $productAttachmentInfo): array
    {
        $localizedNames = $productAttachmentInfo->getLocalizedNames();

        return [
            'attachment_id' => $productAttachmentInfo->getAttachmentId(),
            'name' => $localizedNames[$this->getContextLangId()] ?? reset($localizedNames),
            'file_name' => $productAttachmentInfo->getFilename(),
            'mime_type' => $productAttachmentInfo->getMimeType(),
        ];
    }

    /**
     * @param Exception $e
     */
    private function getErrorMessages(Exception $e = null): array
    {
        return [
            DeleteAttachmentException::class => $this->trans(
                'An error occurred while deleting the object.',
                'Admin.Notifications.Error'
            ),
            AttachmentNotFoundException::class => $this->trans(
                'The object cannot be loaded (or found).',
                'Admin.Notifications.Error'
            ),
            AttachmentConstraintException::class => [
                AttachmentConstraintException::INVALID_ID => $this->trans(
                    'The object cannot be loaded (the identifier is missing or invalid)',
                    'Admin.Notifications.Error'
                    ),
                AttachmentConstraintException::INVALID_FILE_SIZE => $this->trans(
                    'Upload error. Please check your server configurations for the maximum upload size allowed.',
                    'Admin.Catalog.Notification'
                ),
                AttachmentConstraintException::EMPTY_NAME => $this->trans(
                    'An attachment name is required.',
                    'Admin.Catalog.Notification'
                ),
                AttachmentConstraintException::EMPTY_DESCRIPTION => $this->trans(
                    'Invalid description for %s language.',
                    'Admin.Catalog.Notification'
                ),
                AttachmentConstraintException::INVALID_FIELDS => $this->trans(
                    'An error occurred when attempting to update the required fields.',
                    'Admin.Notifications.Error'
                    ),
                AttachmentConstraintException::INVALID_DESCRIPTION => $this->trans(
                    'Invalid description for %s language.',
                    'Admin.Catalog.Notification'
                ),
                AttachmentConstraintException::MISSING_NAME_IN_DEFAULT_LANGUAGE => $this->trans(
                    'The %s field is not valid',
                    'Admin.Notifications.Error',
                    [
                        sprintf('"%s"', $this->trans('Name', 'Admin.Global')),
                    ]
                ),
            ],
            AttachmentUploadFailedException::class => $this->trans(
                'Failed to copy the file.',
                'Admin.Catalog.Notification'
            ),
            CannotAddAttachmentException::class => $this->trans(
                'This attachment was unable to be loaded into the database.',
                'Admin.Catalog.Notification'
                ),
            CannotUpdateAttachmentException::class => $this->trans(
                'This attachment was unable to be loaded into the database.',
                'Admin.Catalog.Notification'
            ),
            BulkDeleteAttachmentsException::class => sprintf(
                '%s: %s',
                $this->trans(
                    'An error occurred while deleting this selection.',
                    'Admin.Notifications.Error'
                ),
                $e instanceof BulkDeleteAttachmentsException ? implode(', ', $e->getAttachmentIds()) : ''
            ),
            EmptyFileException::class => $this->trans('No file has been selected', 'Admin.Notifications.Error'),
        ];
    }

    /**
     * @param Request $request
     *
     * @return array
     */
    private function getBulkAttachmentsFromRequest(Request $request): array
    {
        $attachmentIds = $request->request->get('attachment_files_bulk');

        if (!is_array($attachmentIds)) {
            return [];
        }

        foreach ($attachmentIds as $i => $attachmentId) {
            $attachmentIds[$i] = (int) $attachmentId;
        }

        return $attachmentIds;
    }

    /**
     * @return array
     */
    private function getAttachmentToolbarButtons(): array
    {
        $toolbarButtons = [];

        $toolbarButtons['add'] = [
            'href' => $this->generateUrl('admin_attachments_create'),
            'desc' => $this->trans('Add new file', 'Admin.Catalog.Feature'),
            'icon' => 'add_circle_outline',
        ];

        return $toolbarButtons;
    }
}
