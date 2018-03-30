<?php
/**
 * 2007-2018 PrestaShop
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
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShopBundle\Controller\Admin\Configure\AdvancedParameters;

use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use PrestaShopBundle\Exception\FileUploadException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

/**
 * Responsible of "Configure > Advanced Parameters > Import" page display
 */
class ImportController extends FrameworkBundleAdminController
{
    /**
     * Show import form & handle forwarding to legacy controller
     *
     * @param Request $request
     *
     * @Template("@PrestaShop/Admin/Configure/AdvancedParameters/import.html.twig")
     *
     * @return array|RedirectResponse|Response
     */
    public function importAction(Request $request)
    {
        $legacyController = $request->attributes->get('_legacy_controller');

        $formHandler = $this->get('prestashop.admin.import.form_handler');
        $form = $formHandler->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($this->isDemoModeEnabled()) {
                $this->addFlash(
                    'error',
                    $this->trans('This functionality has been disabled.', 'Admin.Notifications.Error')
                );

                return $this->redirectToRoute('admin_import');
            }

            $data = $form->getData();
            if (!$errors = $formHandler->save($data)) {
                return $this->fowardRequestToLegacyResponse($request);
            }

            $this->flashErrors($errors);
        }

        $finder = $this->get('prestashop.core.import.file_finder');
        $names = $finder->getImportFileNames();

        return [
            'layoutHeaderToolbarBtn' => [],
            'layoutTitle' => $this->get('translator')->trans('Import', [], 'Admin.Navigation.Menu'),
            'requireAddonsSearch' => true,
            'requireBulkActions' => false,
            'showContentHeader' => true,
            'enableSidebar' => true,
            'help_link' => $this->generateSidebarLink($legacyController),
            'form' => $form->createView(),
            'file_upload_url' => $this->generateUrl('admin_import_file_upload'),
            'import_file_names' => $names,
            'import_dir' => $this->get('prestashop.core.import.dir')->getDir(),
            'max_file_upload_size' => $this->get('prestashop.utils.ini_config')->getPostMaxSizeInBytes(),
        ];
    }

    /**
     * Handle import file upload
     *
     * @param Request $request
     *
     * @return Response
     */
    public function uploadAction(Request $request)
    {
        $uploadedFile = $request->files->get('file');
        if (!$uploadedFile instanceof UploadedFile) {
            return $this->json([
                'error' => $this->trans('No file was uploaded.', 'Admin.Advparameters.Notification')
            ]);
        }

        try {
            $fileUploader = $this->get('prestashop.core.import.file_uploader');
            $file = $fileUploader->upload($uploadedFile);
        } catch (FileUploadException $e) {
            $response['error'] = $e->getMessage();

            return $this->json($response);
        }

        $response['file'] = [
            'name' => $file->getFilename(),
            'size' => $file->getSize(),
        ];

        return $this->json($response);
    }

    /**
     * Delete import file
     *
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function deleteAction(Request $request)
    {
        if ($filename = $request->query->get('filename')) {
            $fileRemoval = $this->get('prestashop.core.import.file_removal');
            $fileRemoval->remove($filename);
        }

        return $this->redirectToRoute('admin_import');
    }

    /**
     * Download import file from history
     *
     * @param Request $request
     *
     * @return Response
     */
    public function downloadAction(Request $request)
    {
        if ($filename = $request->query->get('filename')) {
            $importDirectory = $this->get('prestashop.core.import.dir');

            $response = new BinaryFileResponse($importDirectory.$filename);
            $response->setContentDisposition(
                ResponseHeaderBag::DISPOSITION_ATTACHMENT,
                $filename
            );

            return $response;
        }

        return $this->redirectToRoute('admin_import');
    }

    /**
     * Fowards submitted form data to legacy import page
     *
     * @param Request $request
     *
     * @return RedirectResponse
     */
    private function fowardRequestToLegacyResponse(Request $request)
    {
        $legacyController = $request->attributes->get('_legacy_controller');
        $legacyContext =  $this->get('prestashop.adapter.legacy.context');

        $legacyImportUrl = $legacyContext->getAdminLink($legacyController);

        return $this->redirect($legacyImportUrl, Response::HTTP_TEMPORARY_REDIRECT);
    }
}
