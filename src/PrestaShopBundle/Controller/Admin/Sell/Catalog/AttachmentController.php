<?php
/**
 * 2007-2019 PrestaShop and Contributors
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShopBundle\Controller\Admin\Sell\Catalog;

use PrestaShop\PrestaShop\Core\Domain\Attachment\Command\BulkDeleteAttachmentsCommand;
use PrestaShop\PrestaShop\Core\Domain\Attachment\Command\DeleteAttachmentCommand;
use PrestaShop\PrestaShop\Core\Domain\Attachment\Exception\AttachmentConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Attachment\Exception\AttachmentException;
use PrestaShop\PrestaShop\Core\Domain\Attachment\Exception\AttachmentNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Attachment\Exception\DeleteAttachmentException;
use PrestaShop\PrestaShop\Core\Domain\Attachment\Query\GetAttachment;
use PrestaShop\PrestaShop\Core\Domain\Attachment\QueryResult\Attachment;
use PrestaShop\PrestaShop\Core\Search\Filters\AttachmentFilters;
use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use PrestaShopBundle\Security\Annotation\AdminSecurity;
use PrestaShopBundle\Security\Annotation\DemoRestricted;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class is responsible for "Sell > Catalog > Files" page.
 */
class AttachmentController extends FrameworkBundleAdminController
{
    /**
     * @AdminSecurity("is_granted(['read'], request.get('_legacy_controller'))")
     *
     * @param Request $request
     * @param AttachmentFilters $filters
     *
     * @return Response
     */
    public function indexAction(Request $request, AttachmentFilters $filters)
    {
        $attachmentGridFactory = $this->get('prestashop.core.grid.factory.attachment');
        $attachmentGrid = $attachmentGridFactory->getGrid($filters);

        return $this->render('@PrestaShop/Admin/Sell/Catalog/Attachment/index.html.twig', [
            'help_link' => $this->generateSidebarLink($request->attributes->get('_legacy_controller')),
            'attachmentGrid' => $this->presentGrid($attachmentGrid),
            'enableSidebar' => true,
            'layoutHeaderToolbarBtn' => $this->getAttachmentToolbarButtons($request),
        ]);
    }

    /**
     * Show "Add new" form and handle form submit.
     *
     * @AdminSecurity(
     *     "is_granted(['create'], request.get('_legacy_controller'))",
     *     redirectRoute="admin_attachments_index",
     *     message="You do not have permission to create this."
     * )
     *
     * @param Request $request
     *
     * @return Response
     */
    public function createAction(Request $request)
    {
        return new Response();
    }

    /**
     * Show & process attachment editing.
     *
     * @AdminSecurity(
     *     "is_granted(['update'], request.get('_legacy_controller'))",
     *     redirectRoute="admin_attachments_index",
     *     message="You do not have permission to edit this."
     * )
     *
     * @param int $attachmentId
     * @param Request $request
     *
     * @return Response
     */
    public function editAction($attachmentId, Request $request)
    {
        return new Response($attachmentId);
    }

    /**
     * View attachment.
     *
     * @AdminSecurity(
     *     "is_granted(['read'], request.get('_legacy_controller'))",
     *     redirectRoute="admin_attachments_index",
     *     message="You do not have permission to edit this."
     * )
     *
     * @param int $attachmentId
     *
     * @return Response
     */
    public function viewAction($attachmentId)
    {
        try {
            /** @var Attachment $attachment */
            $attachment = $this->getCommandBus()->handle(new GetAttachment((int) $attachmentId));

            return $this->file($attachment->getPath(), $attachment->getName());
        } catch (AttachmentException $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages()));
        }

        return $this->redirectToRoute('admin_attachments_index');
    }

    /**
     * Deletes attachment
     *
     * @AdminSecurity("is_granted('delete', request.get('_legacy_controller'))", redirectRoute="admin_attachments_index")
     * @DemoRestricted(redirectRoute="admin_attachments_index")
     *
     * @param $attachmentId
     *
     * @return RedirectResponse
     */
    public function deleteAction($attachmentId)
    {
        try {
            $this->getCommandBus()->handle(new DeleteAttachmentCommand((int) $attachmentId));
            $this->addFlash(
                'success',
                $this->trans('Successful deletion.', 'Admin.Notifications.Success')
            );
        } catch (AttachmentException $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages()));
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
     *
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function deleteBulkAction(Request $request)
    {
        $attachmentIds = $this->getBulkAttachmentsFromRequest($request);

        try {
            $this->getCommandBus()->handle(new BulkDeleteAttachmentsCommand($attachmentIds));
            $this->addFlash(
                'success',
                $this->trans('Successful deletion.', 'Admin.Notifications.Success')
            );
        } catch (AttachmentException $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages()));
        }

        return $this->redirectToRoute('admin_attachments_index');
    }

    /**
     * Provides error messages for exceptions
     *
     * @return array
     */
    private function getErrorMessages()
    {
        return [
            DeleteAttachmentException::class => $this->trans(
                'An error occurred while deleting the object.',
                'Admin.Notifications.Error'
            ),
            AttachmentNotFoundException::class => $this->trans(
                'The object cannot be loaded (or found)',
                'Admin.Notifications.Error'
            ),
            AttachmentConstraintException::class => [
                AttachmentConstraintException::INVALID_ID => $this->trans(
                    'The object cannot be loaded (the identifier is missing or invalid)',
                    'Admin.Notifications.Error'
                ),
            ],
        ];
    }

    /**
     * @param Request $request
     *
     * @return array
     */
    private function getBulkAttachmentsFromRequest(Request $request)
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
    private function getAttachmentToolbarButtons()
    {
        $toolbarButtons = [];

        $toolbarButtons['add'] = [
            'href' => $this->generateUrl('admin_attachment_create'),
            'desc' => $this->trans('Add new file', 'Admin.Catalog.Feature'),
            'icon' => 'add_circle_outline',
        ];

        return $toolbarButtons;
    }
}
