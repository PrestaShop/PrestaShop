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

namespace PrestaShopBundle\Controller\Admin\Configure\AdvancedParameters;

use PrestaShop\PrestaShop\Core\Import\Exception\NotSupportedImportEntityException;
use PrestaShop\PrestaShop\Core\Import\Exception\UnavailableImportFileException;
use PrestaShop\PrestaShop\Core\Import\ImportDirectory;
use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use PrestaShopBundle\Exception\FileUploadException;
use PrestaShopBundle\Security\Annotation\AdminSecurity;
use PrestaShopBundle\Security\Annotation\DemoRestricted;
use PrestaShopBundle\Security\Voter\PageVoter;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

/**
 * Responsible of "Configure > Advanced Parameters > Import" page display.
 */
class ImportController extends FrameworkBundleAdminController
{
    /**
     * Show import form & handle forwarding to legacy controller.
     *
     * @param Request $request
     *
     * @AdminSecurity("is_granted('read', request.get('_legacy_controller'))")
     *
     * @return array|RedirectResponse|Response
     */
    public function importAction(Request $request)
    {
        $legacyController = $request->attributes->get('_legacy_controller');

        $importDir = $this->get('prestashop.core.import.dir');

        if (!$this->checkImportDirectory($importDir)) {
            return $this->render(
                '@PrestaShop/Admin/Configure/AdvancedParameters/ImportPage/import.html.twig',
                $this->getTemplateParams($request)
            );
        }

        $formHandler = $this->get('prestashop.admin.import.form_handler');
        $finder = $this->get('prestashop.core.import.file_finder');
        $iniConfiguration = $this->get('prestashop.core.configuration.ini_configuration');
        $importConfigFactory = $this->get('prestashop.core.import.config_factory');

        $importConfig = $importConfigFactory->buildFromRequest($request);
        $form = $formHandler->getForm($importConfig);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if (!$this->checkImportFormSubmitPermissions($legacyController)) {
                return $this->redirectToRoute('admin_import');
            }

            $data = $form->getData();

            if (!$errors = $formHandler->save($data)) {
                // WIP import page 2 redirect
                /*return $this->redirectToRoute(
                    'admin_import_data_configuration_index',
                    [],
                    Response::HTTP_TEMPORARY_REDIRECT
                );*/
                return $this->forwardRequestToLegacyResponse($request);
            }

            $this->flashErrors($errors);
        }

        $params = [
            'importForm' => $form->createView(),
            'importFileUploadUrl' => $this->generateUrl('admin_import_file_upload'),
            'importFileNames' => $finder->getImportFileNames(),
            'importDirectory' => $importDir->getDir(),
            'maxFileUploadSize' => $iniConfiguration->getPostMaxSizeInBytes(),
        ];

        return $this->render(
            '@PrestaShop/Admin/Configure/AdvancedParameters/ImportPage/import.html.twig',
            $this->getTemplateParams($request) + $params
        );
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
                'error' => $this->trans('You do not have permission to update this.', 'Admin.Notifications.Error'),
            ]);
        }

        $uploadedFile = $request->files->get('file');
        if (!$uploadedFile instanceof UploadedFile) {
            return $this->json([
                'error' => $this->trans('No file was uploaded.', 'Admin.Advparameters.Notification'),
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
     * Delete import file.
     *
     * @AdminSecurity("is_granted('delete', request.get('_legacy_controller'))", message="You do not have permission to update this.", redirectRoute="admin_import")
     * @DemoRestricted(redirectRoute="admin_import")
     *
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function deleteAction(Request $request)
    {
        $filename = $request->query->get('filename', $request->query->get('csvfilename'));
        if ($filename) {
            $fileRemoval = $this->get('prestashop.core.import.file_removal');
            $fileRemoval->remove($filename);
        }

        return $this->redirectToRoute('admin_import');
    }

    /**
     * Download import file from history.
     *
     * @AdminSecurity("is_granted(['read','update', 'create','delete'], request.get('_legacy_controller'))", message="You do not have permission to update this.", redirectRoute="admin_import")
     * @DemoRestricted(redirectRoute="admin_import")
     *
     * @param Request $request
     *
     * @return Response
     */
    public function downloadAction(Request $request)
    {
        if ($filename = $request->query->get('filename')) {
            $importDirectory = $this->get('prestashop.core.import.dir');

            $response = new BinaryFileResponse($importDirectory . $filename);
            $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, $filename);

            return $response;
        }

        return $this->redirectToRoute('admin_import');
    }

    /**
     * Download import sample file.
     *
     * @AdminSecurity("is_granted(['read'], request.get('_legacy_controller'))", redirectRoute="admin_import")
     *
     * @param string $sampleName
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
     * Get available entity fields.
     *
     * @AdminSecurity(
     *     "is_granted('read', request.get('_legacy_controller'))",
     *     redirectRoute="admin_import"
     * )
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function getAvailableEntityFieldsAction(Request $request)
    {
        $fieldsProviderFinder = $this->get('prestashop.core.import.fields_provider_finder');
        try {
            $fieldsProvider = $fieldsProviderFinder->find($request->get('entity'));
            $fieldsCollection = $fieldsProvider->getCollection();
            $entityFields = $fieldsCollection->toArray();
        } catch (NotSupportedImportEntityException $e) {
            $entityFields = [];
        }

        return $this->json($entityFields);
    }

    /**
     * Process the import.
     *
     * @AdminSecurity("is_granted(['update', 'create', 'delete'], request.get('_legacy_controller'))", redirectRoute="admin_import")
     * @DemoRestricted(redirectRoute="admin_import")
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function processImportAction(Request $request)
    {
        $errors = [];
        $requestValidator = $this->get('prestashop.core.import.request_validator');

        try {
            $requestValidator->validate($request);
        } catch (UnavailableImportFileException $e) {
            $errors[] = $this->trans('To proceed, please upload a file first.', 'Admin.Advparameters.Notification');
        }

        if (!empty($errors)) {
            return $this->json([
                'errors' => $errors,
                'isFinished' => true,
            ]);
        }

        $importer = $this->get('prestashop.core.import.importer');
        $importConfigFactory = $this->get('prestashop.core.import.config_factory');
        $runtimeConfigFactory = $this->get('prestashop.core.import.runtime_config_factory');
        $importHandlerFinder = $this->get('prestashop.adapter.import.handler_finder');

        $importConfig = $importConfigFactory->buildFromRequest($request);
        $runtimeConfig = $runtimeConfigFactory->buildFromRequest($request);

        $importer->import(
            $importConfig,
            $runtimeConfig,
            $importHandlerFinder->find($importConfig->getEntityType())
        );

        return $this->json($runtimeConfig->toArray());
    }

    /**
     * Get generic template parameters.
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
     * Checks permissions of import form in step 1.
     *
     * @param string $legacyController
     *
     * @return bool
     */
    private function checkImportFormSubmitPermissions($legacyController)
    {
        if ($this->isDemoModeEnabled()) {
            $this->addFlash(
                'error',
                $this->trans(
                    'This functionality has been disabled.',
                    'Admin.Notifications.Error'
                )
            );

            return false;
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

            return false;
        }

        return true;
    }

    /**
     * Check if the import directory exists and is accessible.
     *
     * @param ImportDirectory $importDir
     *
     * @return bool
     */
    private function checkImportDirectory(ImportDirectory $importDir)
    {
        if (!$importDir->exists()) {
            $this->addFlash(
                'error',
                $this->trans(
                    'The import directory doesn\'t exist. Please check your file path.',
                    'Admin.Advparameters.Notification'
                )
            );

            return false;
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

        return true;
    }

    /**
     * Forwards submitted form data to legacy import page.
     * To be removed in 1.7.7 version.
     *
     * @param Request $request
     *
     * @return RedirectResponse
     */
    private function forwardRequestToLegacyResponse(Request $request)
    {
        $legacyController = $request->attributes->get('_legacy_controller');
        $legacyContext = $this->get('prestashop.adapter.legacy.context');

        $legacyImportUrl = $legacyContext->getLegacyAdminLink($legacyController);

        return $this->redirect($legacyImportUrl, Response::HTTP_TEMPORARY_REDIRECT);
    }
}
