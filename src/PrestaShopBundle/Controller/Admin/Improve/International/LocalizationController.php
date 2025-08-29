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

namespace PrestaShopBundle\Controller\Admin\Improve\International;

use PrestaShop\PrestaShop\Core\Form\FormHandlerInterface;
use PrestaShop\PrestaShop\Core\Localization\Pack\Import\LocalizationPackImportConfig;
use PrestaShop\PrestaShop\Core\Localization\Pack\Import\LocalizationPackImporter;
use PrestaShopBundle\Controller\Admin\PrestaShopAdminController;
use PrestaShopBundle\Form\Admin\Improve\International\Localization\ImportLocalizationPackType;
use PrestaShopBundle\Security\Attribute\AdminSecurity;
use PrestaShopBundle\Security\Attribute\DemoRestricted;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class LocalizationController is responsible for handling "Improve > International > Localization" page.
 */
class LocalizationController extends PrestaShopAdminController
{
    /**
     * Show localization settings page.
     *
     * @param Request $request
     *
     * @return Response
     */
    #[AdminSecurity("is_granted('read', request.get('_legacy_controller'))", message: 'Access denied.')]
    public function indexAction(
        Request $request,
        #[Autowire(service: 'prestashop.admin.localization.configuration.form_handler')]
        FormHandlerInterface $configurationFormHandler,
        #[Autowire(service: 'prestashop.admin.localization.local_units.form_handler')]
        FormHandlerInterface $localUnitsFormHandler,
        #[Autowire(service: 'prestashop.admin.localization.advanced.form_handler')]
        FormHandlerInterface $advancedFormHandler
    ): Response {
        $legacyController = $request->attributes->get('_legacy_controller');

        if (!extension_loaded('openssl')) {
            $this->addFlash('warning', $this->trans('Importing a new language may fail without the OpenSSL module. Please enable "openssl.so" on your server configuration.', [], 'Admin.International.Notification'));
        }

        $localizationPackImportForm = $this->createForm(ImportLocalizationPackType::class);
        $configurationForm = $configurationFormHandler->getForm();
        $localUnitsForm = $localUnitsFormHandler->getForm();
        $advancedForm = $advancedFormHandler->getForm();

        return $this->render('@PrestaShop/Admin/Improve/International/Localization/index.html.twig', [
            'layoutHeaderToolbarBtn' => [],
            'layoutTitle' => $this->trans('Localization', [], 'Admin.Navigation.Menu'),
            'enableSidebar' => true,
            'help_link' => $this->generateSidebarLink($legacyController),
            'configurationForm' => $configurationForm->createView(),
            'localUnitsForm' => $localUnitsForm->createView(),
            'advancedForm' => $advancedForm->createView(),
            'localizationPackImportForm' => $localizationPackImportForm->createView(),
        ]);
    }

    /**
     * Process the Localization Configuration form.
     *
     * @param Request $request
     *
     * @return RedirectResponse
     */
    #[DemoRestricted(redirectRoute: 'admin_localization_index')]
    #[AdminSecurity("is_granted('update', request.get('_legacy_controller')) && is_granted('create', request.get('_legacy_controller')) && is_granted('delete', request.get('_legacy_controller'))", message: 'You do not have permission to edit this.', redirectRoute: 'admin_localization_index')]
    public function processConfigurationFormAction(
        Request $request,
        #[Autowire(service: 'prestashop.admin.localization.configuration.form_handler')]
        FormHandlerInterface $configurationFormHandler,
    ): RedirectResponse {
        return $this->processForm(
            $request,
            $configurationFormHandler,
            'Configuration'
        );
    }

    /**
     * Process the Localization Local Units form.
     *
     * @param Request $request
     *
     * @return RedirectResponse
     */
    #[DemoRestricted(redirectRoute: 'admin_localization_index')]
    #[AdminSecurity("is_granted('read', request.get('_legacy_controller')) && is_granted('update', request.get('_legacy_controller')) && is_granted('create', request.get('_legacy_controller')) && is_granted('delete', request.get('_legacy_controller'))", message: 'You do not have permission to edit this.')]
    public function processLocalUnitsFormAction(
        Request $request,
        #[Autowire(service: 'prestashop.admin.localization.local_units.form_handler')]
        FormHandlerInterface $localUnitsFormHandler,
    ): RedirectResponse {
        return $this->processForm(
            $request,
            $localUnitsFormHandler,
            'LocalUnits'
        );
    }

    /**
     * Process the Localization Advanced form.
     *
     * @param Request $request
     *
     * @return RedirectResponse
     */
    #[DemoRestricted(redirectRoute: 'admin_localization_index')]
    #[AdminSecurity("is_granted('read', request.get('_legacy_controller')) && is_granted('update', request.get('_legacy_controller')) && is_granted('create', request.get('_legacy_controller')) && is_granted('delete', request.get('_legacy_controller'))", message: 'You do not have permission to edit this.')]
    public function processAdvancedFormAction(
        Request $request,
        #[Autowire(service: 'prestashop.admin.localization.advanced.form_handler')]
        FormHandlerInterface $advancedFormHandler
    ): RedirectResponse {
        return $this->processForm(
            $request,
            $advancedFormHandler,
            'Advanced'
        );
    }

    /**
     * Process the Localization configuration form.
     *
     * @param Request $request
     * @param FormHandlerInterface $formHandler
     * @param string $hookName
     *
     * @return RedirectResponse
     */
    protected function processForm(Request $request, FormHandlerInterface $formHandler, string $hookName): RedirectResponse
    {
        $this->dispatchHookWithParameters(
            'actionAdminInternationalLocalizationControllerPostProcess' . $hookName . 'Before',
            ['controller' => $this]
        );

        $this->dispatchHookWithParameters('actionAdminInternationalLocalizationControllerPostProcessBefore', ['controller' => $this]);

        $form = $formHandler->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $data = $form->getData();
            $saveErrors = $formHandler->save($data);

            if (0 === count($saveErrors)) {
                $this->addFlash('success', $this->trans('Update successful', [], 'Admin.Notifications.Success'));
            } else {
                $this->addFlashErrors($saveErrors);
            }
        }

        return $this->redirectToRoute('admin_localization_index');
    }

    /**
     * Handles localization pack import.
     *
     * @param Request $request
     *
     * @return RedirectResponse
     */
    #[DemoRestricted(redirectRoute: 'admin_localization_index')]
    #[AdminSecurity("is_granted('update', request.get('_legacy_controller')) && is_granted('create', request.get('_legacy_controller')) && is_granted('delete', request.get('_legacy_controller'))", message: 'You do not have permission to edit this.', redirectRoute: 'admin_localization_index')]
    public function importPackAction(
        Request $request,
        LocalizationPackImporter $localizationPackImporter
    ): RedirectResponse {
        $localizationPackImportForm = $this->createForm(ImportLocalizationPackType::class);
        $localizationPackImportForm->handleRequest($request);

        if ($localizationPackImportForm->isSubmitted()) {
            $data = $localizationPackImportForm->getData();

            $localizationImportConfig = new LocalizationPackImportConfig(
                $data['iso_localization_pack'],
                $data['content_to_import'],
                $data['download_pack_data']
            );

            $errors = $localizationPackImporter->import($localizationImportConfig);

            if (empty($errors)) {
                $this->addFlash(
                    'success',
                    $this->trans('Localization pack imported successfully.', [], 'Admin.International.Notification')
                );

                return $this->redirectToRoute('admin_localization_index');
            }

            foreach ($errors as $error) {
                $this->addFlash('error', $error);
            }
        }

        return $this->redirectToRoute('admin_localization_index');
    }
}
