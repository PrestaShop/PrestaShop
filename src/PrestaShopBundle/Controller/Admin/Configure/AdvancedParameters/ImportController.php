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
use PrestaShopBundle\Security\Voter\PageVoter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use PrestaShopBundle\Security\Annotation\AdminSecurity;
use PrestaShopBundle\Security\Annotation\DemoRestricted;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
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
     * @Template("@PrestaShop/Admin/Configure/AdvancedParameters/ImportPage/import.html.twig")
     *
     * @return array|RedirectResponse|Response
     */
    public function importAction(Request $request)
    {
        $legacyController = $request->attributes->get('_legacy_controller');

        $importDir = $this->get('prestashop.core.import.dir');
        if (!$importDir->exists()) {
            $this->addFlash(
                'error',
                $this->trans(
                    'The import directory doesn\'t exist. Please check your file path.',
                    'Admin.Advparameters.Notification'
                )
            );

            return $this->getTemplateParams($request);
        }

        if (!$importDir->isWritable()) {
            $this->addFlash(
                'warning',
                $this->trans(
                    'The import directory must be writable (CHMOD 755 / 777).',
                    'Admin.Advparameters.Notification'
                )
            );
        }

        $formHandler = $this->get('prestashop.admin.import.form_handler');
        $finder = $this->get('prestashop.core.import.file_finder');
        $iniConfiguration = $this->get('prestashop.core.configuration.ini_configuration');

        $form = $formHandler->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($this->isDemoModeEnabled()) {
                $this->addFlash(
                    'error',
                    $this->trans(
                        'This functionality has been disabled.',
                        'Admin.Notifications.Error'
                    )
                );

                return $this->redirectToRoute('admin_import');
            }

            if (!in_array($this->authorizationLevel($legacyController), [
                PageVoter::LEVEL_CREATE,
                PageVoter::LEVEL_UPDATE,
                PageVoter::LEVEL_DELETE,
            ])) {
                $this->addFlash(
                    'error',
                    $this->trans(
                        'You do not have permission to update this.',
                        'Admin.Notifications.Error'
                    )
                );

                return $this->redirectToRoute('admin_import');
            }

            $data = $form->getData();
            if (!$errors = $formHandler->save($data)) {
                return $this->fowardRequestToLegacyResponse($request);
            }

            $this->flashErrors($errors);
        }

        $params = [
            'importForm' => $form->createView(),
            'importFileUploadUrl' => $this->generateUrl('admin_import_file_upload'),
            'importFileNames' => $finder->getImportFileNames(),
            'importDirectory' => $this->get('prestashop.core.import.dir')->getDir(),
            'maxFileUploadSize' => $iniConfiguration->getPostMaxSizeInBytes(),
        ];

        return $this->getTemplateParams($request) + $params;
    }

    /**
     * Handle import file upload via AJAX, sending authorization errors in JSON.
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function uploadAction(Request $request)
    {
        $legacyController = $request->attributes->get('_legacy_controller');

        if ($this->isDemoModeEnabled()) {
            return $this->json([
                'error' => $this->trans('This functionality has been disabled.', 'Admin.Notifications.Error'),
            ]);
        }

        if (!in_array($this->authorizationLevel($legacyController), [
            PageVoter::LEVEL_CREATE,
            PageVoter::LEVEL_UPDATE,
            PageVoter::LEVEL_DELETE,
        ])) {
            return $this->json([
                'error' =>  $this->trans('You do not have permission to update this.', 'Admin.Notifications.Error'),
            ]);
        }

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
            return $this->json(['error' => $e->getMessage()]);
        }

        $response['file'] = [
            'name' => $file->getFilename(),
            'size' => $file->getSize(),
        ];

        return $this->json($response);
    }

    /**
     * Delete import file
     * @AdminSecurity("is_granted('delete', request.get('_legacy_controller')~'_')", message="You do not have permission to update this.", redirectRoute="admin_import")
     * @DemoRestricted(redirectRoute="admin_import")
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
     * @AdminSecurity("is_granted(['read','update', 'create','delete'], request.get('_legacy_controller')~'_')", message="You do not have permission to update this.", redirectRoute="admin_import")
     * @DemoRestricted(redirectRoute="admin_import")
     *
     * @param Request $request
     * @return Response
     */
    public function downloadAction(Request $request)
    {
        if ($filename = $request->query->get('filename')) {
            $importDirectory = $this->get('prestashop.core.import.dir');

            $response = new BinaryFileResponse($importDirectory.$filename);
            $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, $filename);

            return $response;
        }

        return $this->redirectToRoute('admin_import');
    }

    /**
     * Download import sample file
     *
     * @param $sampleName
     *
     * @return Response
     */
    public function downloadSampleAction($sampleName)
    {
        $sampleFileProvider = $this->get('prestashop.core.import.sample.file_provider');
        $sampleFile = $sampleFileProvider->getFile($sampleName);

        if (null === $sampleFile) {
            return $this->redirectToRoute('admin_import');
        }

        $response = new BinaryFileResponse($sampleFile->getPathname());
        $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, $sampleFile->getFilename());

        return $response;
    }

    /**
     * Get generic template parameters
     *
     * @param Request $request
     *
     * @return array
     */
    protected function getTemplateParams(Request $request)
    {
        $legacyController = $request->attributes->get('_legacy_controller');

        return [
            'layoutHeaderToolbarBtn' => [],
            'layoutTitle' => $this->get('translator')->trans('Import', [], 'Admin.Navigation.Menu'),
            'requireAddonsSearch' => true,
            'requireBulkActions' => false,
            'showContentHeader' => true,
            'enableSidebar' => true,
            'help_link' => $this->generateSidebarLink($legacyController),
        ];
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
