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

namespace PrestaShopBundle\Controller\Admin;

use PrestaShop\PrestaShop\Core\Language\Copier\LanguageCopierConfig;
use PrestaShopBundle\Security\Annotation\AdminSecurity;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

/**
 * Admin controller for the International pages.
 */
class TranslationsController extends FrameworkBundleAdminController
{
    protected $layoutTitle = 'Translations';

    const CONTROLLER_NAME = 'ADMINTRANSLATIONS';

    /**
     * @deprecated
     */
    const controller_name = self::CONTROLLER_NAME;

    /**
     * @Template("@PrestaShop/Admin/Translations/overview.html.twig")
     */
    public function overviewAction()
    {
        return parent::overviewAction();
    }

    /**
     * Extract theme using locale and theme name.
     *
     * @AdminSecurity("is_granted('read', request.get('_legacy_controller'))")
     *
     * @param Request $request
     *
     * @return BinaryFileResponse
     */
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
     * @Template("@PrestaShop/Admin/Improve/International/Translations/translations_settings.html.twig")
     * @AdminSecurity("is_granted('read', request.get('_legacy_controller'))")
     *
     * @param Request $request
     *
     * @return array
     */
    public function showSettingsAction(Request $request)
    {
        $legacyController = $request->attributes->get('_legacy_controller');
        $legacyContext = $this->get('prestashop.adapter.legacy.context');
        $kpiRowFactory = $this->get('prestashop.core.kpi_row.factory.translations_page');
        $formHandler = $this->get('prestashop.admin.translations_settings.form_handler');
        $form = $formHandler->getForm();

        return [
            'layoutTitle' => $this->trans('Translations', 'Admin.Navigation.Menu'),
            'enableSidebar' => true,
            'help_link' => $this->generateSidebarLink($legacyController),
            'kpiRow' => $kpiRowFactory->build(),
            'translationSettingsForm' => $form->createView(),
            'addLanguageUrl' => $legacyContext->getAdminLink('AdminLanguages', true, ['addlang' => '']),
        ];
    }

    /**
     * Modify translations action.
     *
     * @AdminSecurity("is_granted('read', request.get('_legacy_controller')~'_')")
     *
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function modifyTranslationsAction(Request $request)
    {
        $routeFinder = $this->get('prestashop.adapter.translation_route_finder');
        $route = $routeFinder->findRoute($request->query);
        $routeParameters = $routeFinder->findRouteParameters($request->query);

        // If route parameters are empty we are redirecting to a legacy route
        return empty($routeParameters) ? $this->redirect($route) : $this->redirectToRoute($route, $routeParameters);
    }

    /**
     * Add language pack for new languages and updates for the existing ones action.
     *
     * @AdminSecurity("is_granted('create', request.get('_legacy_controller')~'_')"))
     *
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function addUpdateLanguageAction(Request $request)
    {
        $formHandler = $this->get('prestashop.admin.translations_settings.form_handler');
        $addUpdateLanguageForm = $formHandler->getForm();
        $addUpdateLanguageForm->handleRequest($request);

        if ($addUpdateLanguageForm->isSubmitted()) {
            $data = $addUpdateLanguageForm->getData();
            $isoCode = $data['add_update_language']['iso_localization_pack'];

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
     * Extract theme using locale and theme name.
     *
     * @AdminSecurity("is_granted('create', request.get('_legacy_controller')~'_')")
     *
     * @param Request $request
     *
     * @return BinaryFileResponse|RedirectResponse
     */
    public function exportThemeLanguageAction(Request $request)
    {
        $formHandler = $this->get('prestashop.admin.translations_settings.form_handler');
        $exportThemeLanguageForm = $formHandler->getForm();
        $exportThemeLanguageForm->handleRequest($request);

        if ($exportThemeLanguageForm->isSubmitted()) {
            $data = $exportThemeLanguageForm->getData();

            $themeName = $data['export_language']['theme_name'];
            $isoCode = $data['export_language']['iso_code'];

            $langRepository = $this->get('prestashop.core.admin.lang.repository');
            $locale = $langRepository->getLocaleByIsoCode($isoCode);

            $themeExporter = $this->get('prestashop.translation.theme.exporter');
            $zipFile = $themeExporter->createZipArchive($themeName, $locale, _PS_ROOT_DIR_ . DIRECTORY_SEPARATOR);

            $response = new BinaryFileResponse($zipFile);
            $response->deleteFileAfterSend(true);

            $themeExporter->cleanArtifacts($themeName);

            return $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT);
        }

        return $this->redirectToRoute('admin_international_translations_show_settings');
    }

    /**
     * Copy language action.
     *
     * @AdminSecurity("is_granted('create', request.get('_legacy_controller')~'_')")
     *
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function copyLanguageAction(Request $request)
    {
        $formHandler = $this->get('prestashop.admin.translations_settings.form_handler');
        $form = $formHandler->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $languageCopier = $this->get('prestashop.adapter.language.copier');
            $data = $form->getData();
            $languageCopierConfig = new LanguageCopierConfig(
                $data['copy_language']['from_theme'],
                $data['copy_language']['from_language'],
                $data['copy_language']['to_theme'],
                $data['copy_language']['to_language']
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
}
