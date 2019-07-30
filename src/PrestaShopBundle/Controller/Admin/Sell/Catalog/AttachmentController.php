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

use Exception;
use PrestaShop\PrestaShop\Core\Domain\Attachment\Exception\AttachmentNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Attachment\Query\GetAttachmentForEditing;
use PrestaShop\PrestaShop\Core\Domain\Attachment\QueryResult\EditableAttachment;
use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use PrestaShopBundle\Security\Annotation\AdminSecurity;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class AttachmentController
 */
class AttachmentController extends FrameworkBundleAdminController
{
    public function indexAction()
    {
        return new Response();
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
        $attachmentFormBuilder = $this->get(
            'prestashop.core.form.identifiable_object.builder.attachment_form_builder'
        );
        $attachmentFormHandler = $this->get(
            'prestashop.core.form.identifiable_object.handler.attachment_form_handler'
        );

        $attachmentForm = $attachmentFormBuilder->getForm(
            [],
            [
                'is_edit_form' => false,
            ]
        );

        $attachmentForm->handleRequest($request);

        try {
            $handlerResult = $attachmentFormHandler->handle($attachmentForm);

            if ($handlerResult->isSubmitted() && $handlerResult->isValid()) {
                $this->addFlash('success', $this->trans('Successful creation.', 'Admin.Notifications.Success'));

                return $this->redirectToRoute('admin_attachments_index');
            }
        } catch (Exception $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages()));
        }

        return $this->render('@PrestaShop/Admin/Sell/Attachment/add.html.twig', [
            'attachmentForm' => $attachmentForm->createView(),
            'help_link' => $this->generateSidebarLink($request->attributes->get('_legacy_controller')),
        ]);
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
        try {
            /** @var EditableAttachment $attachmentInformation */
            $attachmentInformation = $this->getQueryBus()->handle(new GetAttachmentForEditing((int) $attachmentId));

            $attachmentFormBuilder = $this->get(
                'prestashop.core.form.identifiable_object.builder.attachment_form_builder'
            );
            $attachmentFormHandler = $this->get(
                'prestashop.core.form.identifiable_object.handler.attachment_form_handler'
            );

            $attachmentForm = $attachmentFormBuilder->getFormFor(
                (int) $attachmentId,
                [],
                [
                    'is_edit_form' => true,
                    'has_old_file' => $attachmentInformation->getFile() !== null,
                ]
            );

            $attachmentForm->handleRequest($request);
            $result = $attachmentFormHandler->handleFor((int) $attachmentId, $attachmentForm);

            if ($result->isSubmitted() && $result->isValid()) {
                $this->addFlash('success', $this->trans('Successful update.', 'Admin.Notifications.Success'));

                return $this->redirectToRoute('admin_attachments_index');
            }
        } catch (Exception $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages($e)));
            if ($e instanceof AttachmentNotFoundException) {
                return $this->redirectToRoute('admin_attachments_index');
            }
        }

        return $this->render('@PrestaShop/Admin/Sell/Attachment/edit.html.twig', [
            'attachmentForm' => $attachmentForm->createView(),
            'attachmentInformation' => $attachmentInformation,
            'help_link' => $this->generateSidebarLink($request->attributes->get('_legacy_controller')),
        ]);
    }

    /**
     * Provides error messages for exceptions
     *
     * @return array
     */
    private function getErrorMessages()
    {
        return [
            AttachmentNotFoundException::class => $this->trans(
                'The object cannot be loaded (or found)',
                'Admin.Notifications.Error'
            ),
        ];
    }
}
