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

use PrestaShop\PrestaShop\Core\Backup\Exception\BackupException;
use PrestaShop\PrestaShop\Core\Backup\Exception\DirectoryIsNotWritableException;
use PrestaShop\PrestaShop\Core\Form\FormHandlerInterface;
use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class BackupController is responsible of "Configure > Advanced Parameters > Database > Backup" page
 */
class BackupController extends FrameworkBundleAdminController
{
    /**
     * Show backup page
     *
     * @param Request $request
     *
     * @return Response
     */
    public function indexAction(Request $request)
    {
        $backupForm = $this->getBackupFormHandler()->getForm();
        $configuration = $this->get('prestashop.adapter.legacy.configuration');

        return $this->render('@PrestaShop/Admin/Configure/AdvancedParameters/Backup/backup.html.twig', [
            'backupForm' => $backupForm->createView(),
            'isHostMode' => $configuration->get('_PS_HOST_MODE_'),
            'enableSidebar' => true,
            'help_link' => $this->generateSidebarLink($request->attributes->get('_legacy_controller')),
        ]);
    }

    /**
     * Process backup options saving
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
     * Create new backup
     *
     * @return RedirectResponse
     */
    public function processBackupCreateAction()
    {
        try {
            $backupCreator = $this->get('prestashop.adapter.backup.database_creator');
            $backup = $backupCreator->createBackup();

            $routeParams['download_filename'] = $backup->getFileName();

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
     * Get backup form handler
     *
     * @return FormHandlerInterface
     */
    protected function getBackupFormHandler()
    {
        return $this->get('prestashop.admin.backup.form_handler');
    }
}
