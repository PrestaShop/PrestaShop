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

use PrestaShop\PrestaShop\Core\Domain\Attachment\Command\DeleteAttachmentCommand;
use PrestaShop\PrestaShop\Core\Domain\Attachment\Exception\AttachmentException;
use PrestaShop\PrestaShop\Core\Domain\Attachment\Exception\DeleteAttachmentException;
use PrestaShop\PrestaShop\Core\Search\Filters\AttachmentFilters;
use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use PrestaShopBundle\Security\Annotation\AdminSecurity;
use PrestaShopBundle\Security\Annotation\DemoRestricted;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class AttachmentController
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
        ]);
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

//    /**
//     * Delete attachments in bulk action.
//     *
//     * @AdminSecurity(
//     *     "is_granted('delete', request.get('_legacy_controller'))",
//     *     redirectRoute="admin_attachments_index",
//     *     message="You do not have permission to delete this."
//     * )
//     *
//     * @param Request $request
//     *
//     * @return RedirectResponse
//     */
//    public function deleteBulkAction(Request $request)
//    {
//        $manufacturerIds = $this->getBulkAttachmentsFromRequest($request);
//
//        try {
//            $this->getCommandBus()->handle(new BulkDeleteAttachmentCommand($manufacturerIds));
//            $this->addFlash(
//                'success',
//                $this->trans('Successful deletion.', 'Admin.Notifications.Success')
//            );
//        } catch (AttachmentException $e) {
//            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages()));
//        }
//
//        return $this->redirectToRoute('admin_attachments_index');
//    }

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
        ];
    }

    /**
     * @param Request $request
     *
     * @return array
     */
    private function getBulkAttachmentsFromRequest(Request $request)
    {
        $attachmentIds = $request->request->get('attachment_bulk');

        if (!is_array($attachmentIds)) {
            return [];
        }

        foreach ($attachmentIds as $i => $attachmentId) {
            $attachmentIds[$i] = (int) $attachmentId;
        }

        return $attachmentId;
    }
}
