<?php
/**
 * 2007-2018 PrestaShop.
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

use PrestaShop\PrestaShop\Adapter\Backup\Backup;
use PrestaShop\PrestaShop\Core\Backup\Exception\BackupException;
use PrestaShop\PrestaShop\Core\Backup\Exception\DirectoryIsNotWritableException;
use PrestaShop\PrestaShop\Core\Form\FormHandlerInterface;
use PrestaShop\PrestaShop\Core\Search\Filters\BackupFilters;
use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use PrestaShopBundle\Security\Annotation\AdminSecurity;
use PrestaShopBundle\Security\Annotation\DemoRestricted;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class BackupController is responsible of "Configure > Advanced Parameters > Database > Backup" page.
 */
class BackupController extends FrameworkBundleAdminController
{
    /**
     * Show backup page.
     *
     * @AdminSecurity("is_granted('read', request.get('_legacy_controller')~'_')")
     *
     * @param Request $request
     * @param BackupFilters $filters
     *
     * @return Response
     */
    public function indexAction(Request $request, BackupFilters $filters)
    {
        $backupForm = $this->getBackupFormHandler()->getForm();
        $configuration = $this->get('prestashop.adapter.legacy.configuration');

        $hasDownloadFile = false;
        $downloadFile = null;

        if ($request->query->has('download_filename')) {
            $hasDownloadFile = true;
            $backup = new Backup($request->query->get('download_filename'));
            $downloadFile = [
                'url' => $backup->getUrl(),
                'size' => number_format($backup->getSize() * 0.000001, 2, '.', ''),
            ];
        }

        $backupsGridFactory = $this->get('prestashop.core.grid.factory.backup');
        $backupGrid = $backupsGridFactory->createUsingSearchCriteria($filters);

        $gridPresenter = $this->get('prestashop.core.grid.presenter.grid_presenter');

        return $this->render('@PrestaShop/Admin/Configure/AdvancedParameters/Backup/backup.html.twig', [
            'backupGrid' => $gridPresenter->present($backupGrid),
            'backupForm' => $backupForm->createView(),
            'isHostMode' => $configuration->get('_PS_HOST_MODE_'),
            'dbPrefix' => $configuration->get('_DB_PREFIX_'),
            'hasDownloadFile' => $hasDownloadFile,
            'downloadFile' => $downloadFile,
            'enableSidebar' => true,
            'help_link' => $this->generateSidebarLink($request->attributes->get('_legacy_controller')),
        ]);
    }

    /**
     * Show file download view.
     *
     * @AdminSecurity("is_granted(['read'], request.get('_legacy_controller')~'_')")
     * @DemoRestricted(redirectRoute="admin_backup")
     *
     * @param Request $request
     * @param string $downloadFileName
     *
     * @return Response
     */
    public function downloadViewAction(Request $request, $downloadFileName)
    {
        $backup = new Backup($downloadFileName);

        return $this->render('@PrestaShop/Admin/Configure/AdvancedParameters/Backup/download_view.html.twig', [
            'downloadFile' => [
                'url' => $backup->getUrl(),
                'size' => $backup->getSize(),
            ],
            'layoutTitle' => $this->trans('View', 'Admin.Actions'),
            'enableSidebar' => true,
            'help_link' => $this->generateSidebarLink($request->attributes->get('_legacy_controller')),
        ]);
    }

    /**
     * Return a backup content as a download
     *
     * @AdminSecurity("is_granted(['read'], request.get('_legacy_controller')~'_')")
     * @DemoRestricted(redirectRoute="admin_backup")
     *
     * @param string $downloadFileName
     *
     * @return BinaryFileResponse
     */
    public function downloadContentAction($downloadFileName)
    {
        $backup = new Backup($downloadFileName);

        return new BinaryFileResponse($backup->getFilePath());
    }

    /**
     * Process backup options saving.
     *
     * @AdminSecurity("is_granted(['update', 'create', 'delete'], request.get('_legacy_controller')~'_')")
     * @DemoRestricted(redirectRoute="admin_backup")
     *
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function processFormAction(Request $request)
    {
        $backupFormHandler = $this->getBackupFormHandler();

        $backupForm = $backupFormHandler->getForm();
        $backupForm->handleRequest($request);

        if ($backupForm->isSubmitted()) {
            $errors = $backupFormHandler->save($backupForm->getData());

            if (!empty($errors)) {
                $this->flashErrors($errors);
            } else {
                $this->addFlash('success', $this->trans('Update successful', 'Admin.Notifications.Success'));
            }
        }

        return $this->redirectToRoute('admin_backup');
    }

    /**
     * Create new backup.
     *
     * @AdminSecurity("is_granted(['create'], request.get('_legacy_controller')~'_')")
     * @DemoRestricted(redirectRoute="admin_backup")
     *
     * @return RedirectResponse
     */
    public function processBackupCreateAction()
    {
        try {
            $backupCreator = $this->get('prestashop.adapter.backup.database_creator');
            $backup = $backupCreator->createBackup();

            $this->addFlash(
                'success',
                $this->trans(
                    'It appears the backup was successful, however you must download and carefully verify the backup file before proceeding.',
                    'Admin.Advparameters.Notification'
                )
            );

            return $this->redirectToRoute('admin_backup', ['download_filename' => $backup->getFileName()]);
        } catch (DirectoryIsNotWritableException $e) {
            $this->addFlash(
                'error',
                $this->trans(
                    'The "Backups" directory located in the admin directory must be writable (CHMOD 755 / 777).',
                    'Admin.Advparameters.Notification'
                )
            );
        } catch (BackupException $e) {
            $this->addFlash('error', $this->trans('The backup file does not exist', 'Admin.Advparameters.Notification'));
        }

        return $this->redirectToRoute('admin_backup');
    }

    /**
     * Process backup file deletion.
     *
     * @AdminSecurity("is_granted(['delete'], request.get('_legacy_controller')~'_')")
     * @DemoRestricted(redirectRoute="admin_backup")
     *
     * @param string $deleteFileName
     *
     * @return RedirectResponse
     */
    public function processDeleteAction($deleteFileName)
    {
        $backup = new Backup($deleteFileName);
        $backupRemover = $this->get('prestashop.adapter.backup.backup_remover');

        if (!$backupRemover->remove($backup)) {
            $this->addFlash(
                'error',
                sprintf(
                    '%s "%s"',
                    $this->trans('Error deleting', 'Admin.Advparameters.Notification'),
                    $backup->getFileName()
                )
            );

            return $this->redirectToRoute('admin_backup');
        }

        $this->addFlash('success', $this->trans('Successful deletion.', 'Admin.Notifications.Success'));

        return $this->redirectToRoute('admin_backup');
    }

    /**
     * Process bulk backup deletion.
     *
     * @AdminSecurity("is_granted(['delete'], request.get('_legacy_controller')~'_')")
     * @DemoRestricted(redirectRoute="admin_backup")
     *
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function processBulkDeleteAction(Request $request)
    {
        $backupsToDelete = $request->request->get('backup_backup_bulk_file_names', []);

        if (empty($backupsToDelete)) {
            $this->addFlash(
                'error',
                $this->trans('You must select at least one element to delete.', 'Admin.Notifications.Error')
            );

            return $this->redirectToRoute('admin_backup');
        }

        $backupRemover = $this->get('prestashop.adapter.backup.backup_remover');
        $failedBackups = [];

        foreach ($backupsToDelete as $backupFileName) {
            $backup = new Backup($backupFileName);

            if (!$backupRemover->remove($backup)) {
                $failedBackups[] = $backup->getFileName();
            }
        }

        if (!empty($failedBackups)) {
            $this->addFlash(
                'error',
                $this->trans('An error occurred while deleting this selection.', 'Admin.Notifications.Error')
            );

            foreach ($failedBackups as $backupFileName) {
                $this->addFlash(
                    'error',
                    $this->trans('Can\'t delete #%id%', 'Admin.Notifications.Error', ['%id%' => $backupFileName])
                );
            }

            return $this->redirectToRoute('admin_backup');
        }

        $this->addFlash(
            'success',
            $this->trans('The selection has been successfully deleted.', 'Admin.Notifications.Success')
        );

        return $this->redirectToRoute('admin_backup');
    }

    /**
     * Get backup form handler.
     *
     * @return FormHandlerInterface
     */
    protected function getBackupFormHandler()
    {
        return $this->get('prestashop.admin.backup.form_handler');
    }
}
