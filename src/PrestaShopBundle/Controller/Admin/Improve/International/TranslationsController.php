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

use PrestaShop\PrestaShop\Adapter\Shop\Context;
use PrestaShop\PrestaShop\Core\Form\FormHandlerInterface;
use PrestaShop\PrestaShop\Core\Language\Copier\LanguageCopierConfig;
use PrestaShop\PrestaShop\Core\Translation\Storage\Provider\Definition\ProviderDefinitionInterface;
use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use PrestaShopBundle\Exception\InvalidModuleException;
use PrestaShopBundle\Security\Annotation\AdminSecurity;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

/**
 * Admin controller for the International pages.
 */
class TranslationsController extends FrameworkBundleAdminController
{
    public const CONTROLLER_NAME = 'ADMINTRANSLATIONS';

    /**
     * @deprecated
     */
    public const controller_name = self::CONTROLLER_NAME;

    /**
     * Renders the translation page
     */
    #[AdminSecurity("is_granted('read', request.get('_legacy_controller'))")]
    public function overviewAction()
    {
        return $this->render('@PrestaShop/Admin/Improve/International/Translations/overview.html.twig', [
            'is_shop_context' => (new Context())->isShopContext(),
            'layoutTitle' => $this->trans('Translations', 'Admin.Navigation.Menu'),
        ]);
    }

    /**
     * Extract theme using locale and theme name.
     *
     * @param Request $request
     *
     * @return BinaryFileResponse
     */
    #[AdminSecurity("is_granted('read', request.get('_legacy_controller'))")]
    public function exportThemeAction(Request $request)
    {
        $themeName = $request->request->get('theme-name');
        $isoCode = $request->request->get('iso_code');

        $langRepository = $this->get('prestashop.core.admin.lang.repository');
        $locale = $langRepository->getLocaleByIsoCode($isoCode);

        $themeExporter = $this->get('prestashop.translation.theme.exporter');
        $zipFile = $themeExporter->createZipArchive($themeName, $locale, _PS_ROOT_DIR_ . DIRECTORY_SEPARATOR);

        $response = new BinaryFileResponse($zipFile);
        $response->deleteFileAfterSend(true);

        $themeExporter->cleanArtifacts($themeName);

        return $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT);
    }

    /**
     * Show translations settings page.
     *
     * @param Request $request
     *
     * @return Response
     */
    #[AdminSecurity("is_granted('read', request.get('_legacy_controller'))")]
    public function showSettingsAction(Request $request)
    {
        $legacyController = $request->attributes->get('_legacy_controller');
        $legacyContext = $this->get('prestashop.adapter.legacy.context');
        $kpiRowFactory = $this->get('prestashop.core.kpi_row.factory.translations_page');
        $modifyTranslationsForm = $this->getModifyTranslationsFormHandler()->getForm();
        $addUpdateLanguageForm = $this->getAddUpdateLanguageTranslationsFormHandler()->getForm();
        $exportCataloguesForm = $this->getExportTranslationCataloguesFormHandler()->getForm();
        $copyLanguageForm = $this->getCopyLanguageTranslationsFormHandler()->getForm();

        return $this->render('@PrestaShop/Admin/Improve/International/Translations/translations_settings.html.twig', [
            'layoutTitle' => $this->trans('Translations', 'Admin.Navigation.Menu'),
            'enableSidebar' => true,
            'help_link' => $this->generateSidebarLink($legacyController),
            'kpiRow' => $kpiRowFactory->build(),
            'copyLanguageForm' => $copyLanguageForm->createView(),
            'exportCataloguesForm' => $exportCataloguesForm->createView(),
            'addUpdateLanguageForm' => $addUpdateLanguageForm->createView(),
            'modifyTranslationsForm' => $modifyTranslationsForm->createView(),
            'addLanguageUrl' => $legacyContext->getAdminLink('AdminLanguages', true, ['addlang' => '']),
        ]);
    }

    /**
     * Modify translations action.
     *
     * @param Request $request
     *
     * @return RedirectResponse
     */
    #[AdminSecurity("is_granted('read', request.get('_legacy_controller'))")]
    public function modifyTranslationsAction(Request $request)
    {
        try {
            $routeFinder = $this->get('prestashop.adapter.translation_route_finder');
            $route = $routeFinder->findRoute($request->query);
            $routeParameters = $routeFinder->findRouteParameters($request->query);
        } catch (InvalidModuleException $e) {
            $this->addFlash('error', $this->trans('An error has occurred, this module does not exist: %s', 'Admin.International.Notification', [$e->getMessage()]));

            return $this->redirectToRoute('admin_international_translations_show_settings');
        }

        // If route parameters are empty we are redirecting to a legacy route
        return empty($routeParameters) ? $this->redirect($route) : $this->redirectToRoute($route, $routeParameters);
    }

    /**
     * Add language pack for new languages and updates for the existing ones action.
     *
     * @param Request $request
     *
     * @return RedirectResponse
     */
    #[AdminSecurity("is_granted('create', request.get('_legacy_controller'))")]
    public function addUpdateLanguageAction(Request $request)
    {
        $formHandler = $this->getAddUpdateLanguageTranslationsFormHandler();
        $addUpdateLanguageForm = $formHandler->getForm();
        $addUpdateLanguageForm->handleRequest($request);

        if ($addUpdateLanguageForm->isSubmitted()) {
            $data = $addUpdateLanguageForm->getData();
            $isoCode = $data['iso_localization_pack'];

            $languagePackImporter = $this->get('prestashop.adapter.language.pack.importer');
            $errors = $languagePackImporter->import($isoCode);

            if (empty($errors)) {
                $this->addFlash(
                    'success',
                    $this->trans('The translations have been successfully added.', 'Admin.International.Notification')
                );

                return $this->redirectToRoute('admin_international_translations_show_settings');
            }

            foreach ($errors as $error) {
                $this->addFlash('error', $error);
            }
        }

        return $this->redirectToRoute('admin_international_translations_show_settings');
    }

    /**
     * Extract catalogues using locale.
     *
     * @param Request $request
     *
     * @return BinaryFileResponse|RedirectResponse
     */
    #[AdminSecurity("is_granted('create', request.get('_legacy_controller'))")]
    public function exportCataloguesAction(Request $request)
    {
        $formHandler = $this->getExportTranslationCataloguesFormHandler();
        $exportTranslationCataloguesForm = $formHandler->getForm();
        $exportTranslationCataloguesForm->handleRequest($request);

        if ($exportTranslationCataloguesForm->isSubmitted()) {
            $data = $exportTranslationCataloguesForm->getData();

            // Get the language
            $isoCode = $data['iso_code'];

            $coreTypeSelector = $data['core_selectors'];
            $themesTypeSelector = $data['themes_selectors'];
            $modulesTypeSelector = $data['modules_selectors'];
            $selections = [];

            // Core translation types
            if (
                isset($coreTypeSelector['core_type'])
                && $coreTypeSelector['core_type']
                && isset($coreTypeSelector['selected_value'])
            ) {
                foreach ($coreTypeSelector['selected_value'] as $type) {
                    $selections[] = [
                        'type' => $type,
                        'selected' => null,
                    ];

                    /*
                     * Exporting mails will also export Mails_Body
                     */
                    if (ProviderDefinitionInterface::TYPE_MAILS === $type) {
                        $selections[] = [
                            'type' => ProviderDefinitionInterface::TYPE_MAILS_BODY,
                            'selected' => null,
                        ];
                    }
                }
            }

            // Theme translation type
            if (
                isset($themesTypeSelector['themes_type'])
                && $themesTypeSelector['themes_type']
                && isset($themesTypeSelector['selected_value'])
            ) {
                $selections[] = [
                    'type' => ProviderDefinitionInterface::TYPE_THEMES,
                    'selected' => $themesTypeSelector['selected_value'],
                ];
            }

            // Module translation type
            if (
                isset($modulesTypeSelector['modules_type'])
                && $modulesTypeSelector['modules_type']
                && isset($modulesTypeSelector['selected_value'])
            ) {
                $selections[] = [
                    'type' => ProviderDefinitionInterface::TYPE_MODULES,
                    'selected' => $modulesTypeSelector['selected_value'],
                ];
            }

            if (empty($selections)) {
                $this->addFlash(
                    'error',
                    $this->trans('You must select at least one translation type to export translations.', 'Admin.International.Notification')
                );

                return $this->redirectToRoute('admin_international_translations_show_settings');
            }

            $langRepository = $this->get('prestashop.core.admin.lang.repository');
            $locale = $langRepository->getLocaleByIsoCode($isoCode);

            $zipFilename = $this->get('prestashop.translation.export.translation_catalogue')->export($selections, $locale);

            $response = new BinaryFileResponse($zipFilename);
            $response->deleteFileAfterSend(true);

            return $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT);
        }

        return $this->redirectToRoute('admin_international_translations_show_settings');
    }

    /**
     * Copy language action.
     *
     * @param Request $request
     *
     * @return RedirectResponse
     */
    #[AdminSecurity("is_granted('create', request.get('_legacy_controller'))")]
    public function copyLanguageAction(Request $request)
    {
        $formHandler = $this->getCopyLanguageTranslationsFormHandler();
        $form = $formHandler->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $languageCopier = $this->get('prestashop.adapter.language.copier');
            $data = $form->getData();
            $languageCopierConfig = new LanguageCopierConfig(
                $data['from_theme'],
                $data['from_language'],
                $data['to_theme'],
                $data['to_language']
            );

            if ($errors = $languageCopier->copy($languageCopierConfig)) {
                $this->flashErrors($errors);
            } else {
                $this->addFlash(
                    'success',
                    $this->trans('The translation was successfully copied.', 'Admin.International.Notification')
                );
            }
        }

        return $this->redirectToRoute('admin_international_translations_show_settings');
    }

    /**
     * @return FormHandlerInterface
     */
    private function getModifyTranslationsFormHandler(): FormHandlerInterface
    {
        return $this->get('prestashop.admin.translations_settings.modify_translations.form_handler');
    }

    /**
     * @return FormHandlerInterface
     */
    private function getAddUpdateLanguageTranslationsFormHandler(): FormHandlerInterface
    {
        return $this->get('prestashop.admin.translations_settings.add_update_language.form_handler');
    }

    /**
     * @return FormHandlerInterface
     */
    private function getCopyLanguageTranslationsFormHandler(): FormHandlerInterface
    {
        return $this->get('prestashop.admin.translations_settings.copy_language.form_handler');
    }

    /**
     * @return FormHandlerInterface
     */
    private function getExportTranslationCataloguesFormHandler(): FormHandlerInterface
    {
        return $this->get('prestashop.admin.translations_settings.export_catalogues.form_handler');
    }
}
