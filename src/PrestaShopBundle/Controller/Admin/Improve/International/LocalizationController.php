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
use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use PrestaShopBundle\Form\Admin\Improve\International\Localization\ImportLocalizationPackType;
use PrestaShopBundle\Security\Annotation\AdminSecurity;
use PrestaShopBundle\Security\Annotation\DemoRestricted;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class LocalizationController is responsible for handling "Improve > International > Localization" page.
 */
class LocalizationController extends FrameworkBundleAdminController
{
    /**
     * Show localization settings page.
     *
     * @AdminSecurity("is_granted('read', request.get('_legacy_controller'))", message="Access denied.")
     *
     * @param Request $request
     *
     * @return Response
     */
    public function indexAction(Request $request)
    {
        $legacyController = $request->attributes->get('_legacy_controller');

        if (!extension_loaded('openssl')) {
            $this->addFlash('warning', $this->trans('Importing a new language may fail without the OpenSSL module. Please enable "openssl.so" on your server configuration.', 'Admin.International.Notification'));
        }

        $localizationPackImportForm = $this->createForm(ImportLocalizationPackType::class);
        $configurationForm = $this->getConfigurationFormHandler()->getForm();
        $localUnitsForm = $this->getLocalUnitsFormHandler()->getForm();
        $advancedForm = $this->getAdvancedFormHandler()->getForm();

        return $this->render('@PrestaShop/Admin/Improve/International/Localization/index.html.twig', [
            'layoutHeaderToolbarBtn' => [],
            'layoutTitle' => $this->trans('Localization', 'Admin.Navigation.Menu'),
            'requireAddonsSearch' => true,
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
     * @AdminSecurity("is_granted(['update', 'create', 'delete'], request.get('_legacy_controller'))", message="You do not have permission to edit this.")
     * @DemoRestricted(redirectRoute="admin_localization_index")
     *
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function processConfigurationFormAction(Request $request)
    {
        return $this->processForm(
            $request,
            $this->getConfigurationFormHandler(),
            'Configuration'
        );
    }

    /**
     * Process the Localization Local Units form.
     *
     * @AdminSecurity("is_granted(['read','update', 'create','delete'], request.get('_legacy_controller'))", message="You do not have permission to edit this.")
     * @DemoRestricted(redirectRoute="admin_localization_index")
     *
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function processLocalUnitsFormAction(Request $request)
    {
        return $this->processForm(
            $request,
            $this->getLocalUnitsFormHandler(),
            'LocalUnits'
        );
    }

    /**
     * Process the Localization Advanced form.
     *
     * @AdminSecurity("is_granted(['read','update', 'create','delete'], request.get('_legacy_controller'))", message="You do not have permission to edit this.")
     * @DemoRestricted(redirectRoute="admin_localization_index")
     *
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function processAdvancedFormAction(Request $request)
    {
        return $this->processForm(
            $request,
            $this->getAdvancedFormHandler(),
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
    protected function processForm(Request $request, FormHandlerInterface $formHandler, string $hookName)
    {
        $this->dispatchHook(
            'actionAdminInternationalLocalizationControllerPostProcess' . $hookName . 'Before',
            ['controller' => $this]
        );

        $this->dispatchHook('actionAdminInternationalLocalizationControllerPostProcessBefore', ['controller' => $this]);

        $form = $formHandler->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $data = $form->getData();
            $saveErrors = $formHandler->save($data);

            if (0 === count($saveErrors)) {
                $this->addFlash('success', $this->trans('Update successful', 'Admin.Notifications.Success'));
            } else {
                $this->flashErrors($saveErrors);
            }
        }

        return $this->redirectToRoute('admin_localization_index');
    }

    /**
     * Handles localization pack import.
     *
     * @AdminSecurity("is_granted(['update', 'create','delete'], request.get('_legacy_controller'))", message="You do not have permission to edit this.")
     * @DemoRestricted(redirectRoute="admin_localization_index")
     *
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function importPackAction(Request $request)
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

                return $this->redirectToRoute('admin_localization_index');
            }

            foreach ($errors as $error) {
                $this->addFlash('error', $error);
            }
        }

        return $this->redirectToRoute('admin_localization_index');
    }

    /**
     * Returns localization configuration form handler.
     *
     * @return FormHandlerInterface
     */
    private function getConfigurationFormHandler()
    {
        return $this->get('prestashop.admin.localization.configuration.form_handler');
    }

    /**
     * Returns localization local units form handler.
     *
     * @return FormHandlerInterface
     */
    private function getLocalUnitsFormHandler()
    {
        return $this->get('prestashop.admin.localization.local_units.form_handler');
    }

    /**
     * Returns localization advanced form handler.
     *
     * @return FormHandlerInterface
     */
    private function getAdvancedFormHandler()
    {
        return $this->get('prestashop.admin.localization.advanced.form_handler');
    }
}
