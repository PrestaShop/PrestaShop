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

namespace PrestaShopBundle\Controller\Admin\Improve\International;

use PrestaShop\PrestaShop\Core\Form\FormHandlerInterface;
use PrestaShop\PrestaShop\Core\Localization\Pack\Import\LocalizationPackImportConfig;
use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use PrestaShopBundle\Form\Admin\Improve\International\Localization\ImportLocalizationPackType;
use PrestaShopBundle\Security\Annotation\AdminSecurity;
use PrestaShopBundle\Security\Annotation\DemoRestricted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class LocalizationController is responsible for handling "Improve > International > Localization" page
 */
class LocalizationController extends FrameworkBundleAdminController
{
    /**
     * Show localization settings page
     *
     * @Template("@PrestaShop/Admin/Improve/International/Localization/localization.html.twig")
     *
     * @AdminSecurity("is_granted('read', request.get('_legacy_controller')~'_')", message="Access denied.")
     *
     * @param Request $request
     *
     * @return array|RedirectResponse
     */
    public function indexAction(Request $request)
    {
        $legacyController = $request->attributes->get('_legacy_controller');

        if (!extension_loaded('openssl')) {
            $this->addFlash('warning', $this->trans('Importing a new language may fail without the OpenSSL module. Please enable "openssl.so" on your server configuration.', 'Admin.International.Notification'));
        }

        $localizationPackImportForm = $this->createForm(ImportLocalizationPackType::class);
        $localizationForm = $this->getLocalizationFormHandler()->getForm();

        return [
            'layoutHeaderToolbarBtn' => [],
            'layoutTitle' => $this->trans('Localization', 'Admin.Navigation.Menu'),
            'requireAddonsSearch' => true,
            'enableSidebar' => true,
            'help_link' => $this->generateSidebarLink($legacyController),
            'localizationForm' => $localizationForm->createView(),
            'localizationPackImportForm' => $localizationPackImportForm->createView(),
        ];
    }

    /**
     * Save localization settings
     *
     * @AdminSecurity("is_granted(['read','update', 'create','delete'], request.get('_legacy_controller')~'_')", message="You do not have permission to edit this.")
     * @DemoRestricted(redirectRoute="admin_localization_show_settings")
     *
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function processFormAction(Request $request)
    {
        $localizationFormHandler = $this->getLocalizationFormHandler();

        $localizationForm = $localizationFormHandler->getForm();
        $localizationForm->handleRequest($request);

        if ($localizationForm->isSubmitted()) {
            $data = $localizationForm->getData();

            $errors = $localizationFormHandler->save($data);
            if (empty($errors)) {
                $this->addFlash('success', $this->trans('Update successful', 'Admin.Notifications.Success'));

                return $this->redirectToRoute('admin_localization_show_settings');
            }

            $this->flashErrors($errors);
        }

        return $this->redirectToRoute('admin_localization_show_settings');
    }

    /**
     * Handles localization pack import
     *
     * @AdminSecurity("is_granted(['read','update', 'create','delete'], request.get('_legacy_controller')~'_')", message="You do not have permission to edit this.")
     * @DemoRestricted(redirectRoute="admin_localization_show_settings")
     *
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function importLocalizationPackAction(Request $request)
    {
        $localizationPackImportForm = $this->createForm(ImportLocalizationPackType::class);
        $localizationPackImportForm->handleRequest($request);

        if ($localizationPackImportForm->isSubmitted()) {
            $data = $localizationPackImportForm->getData();

            $localizationImportConfig = new LocalizationPackImportConfig(
                $data['iso_localization_pack'],
                $data['content_to_import'],
                $data['download_pack_data']
            );

            $localizationPackImporter = $this->get('prestashop.core.localization.pack.import.importer');
            $errors = $localizationPackImporter->import($localizationImportConfig);

            if (empty($errors)) {
                $this->addFlash(
                    'success',
                    $this->trans('Localization pack imported successfully.', 'Admin.International.Notification')
                );

                return $this->redirectToRoute('admin_localization_show_settings');
            }

            foreach ($errors as $error) {
                $this->addFlash('error', $error);
            }
        }

        return $this->redirectToRoute('admin_localization_show_settings');
    }

    /**
     * Returns localization settings form handler
     *
     * @return FormHandlerInterface
     */
    private function getLocalizationFormHandler()
    {
        return $this->get('prestashop.admin.localization.form_handler');
    }
}
