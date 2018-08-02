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

namespace PrestaShopBundle\Controller\Admin;

use PrestaShop\PrestaShop\Core\Localization\Pack\Import\LocalizationPackImportConfig;
use PrestaShop\PrestaShop\Core\Localization\Pack\Import\LocalizationPackImportConfigInterface;
use PrestaShopBundle\Form\Admin\Improve\International\Translations\AddUpdateLanguageType;
use PrestaShopBundle\Form\Admin\Improve\International\Translations\ModifyTranslationsType;
use PrestaShopBundle\Security\Annotation\AdminSecurity;
use PrestaShopBundle\Security\Voter\PageVoter;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

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

    // redirect to the new translation application
    // before, clean request params
    private function redirectToTranslationApp(Request $request)
    {
        $params = array();
        foreach ($request->request->all() as $k => $p) {
            if (strstr($k, 'selected')) {
                $k = 'selected';
            } elseif ('locale' === $k) {
                $translationService = $this->get('prestashop.service.translation');
                $p = $translationService->langToLocale($p);
            }
            if (!empty($p) && !in_array($k, array('controller'))) {
                $params[$k] = $p;
            }
        }

        return $this->redirectToRoute('admin_international_translation_overview', $params);
    }

    /**
     * List translations keys and corresponding editable values.
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function listAction(Request $request)
    {
        if (!$request->isMethod('POST')) {
            return $this->redirect('./admin-dev/index.php?controller=AdminTranslations');
        }

        if (!in_array(
            $this->authorizationLevel(self::CONTROLLER_NAME),
            array(
                PageVoter::LEVEL_READ,
                PageVoter::LEVEL_UPDATE,
                PageVoter::LEVEL_CREATE,
                PageVoter::LEVEL_DELETE,
            )
        )) {
            return $this->redirect('admin_dashboard');
        }

        return $this->redirectToTranslationApp($request);
    }

    /**
     * List translations keys and corresponding editable values for one module.
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function moduleAction(Request $request)
    {
        if (!$request->isMethod('POST')) {
            return $this->redirect('./admin-dev/index.php?controller=AdminTranslations');
        }

        return $this->redirectToTranslationApp($request);
    }

    /**
     * Extract theme using locale and theme name.
     *
     * @param Request $request
     * @return BinaryFileResponse
     */
    public function exportThemeAction(Request $request)
    {
        $themeName = $request->request->get('theme-name');
        $isoCode = $request->request->get('iso_code');

        $langRepository = $this->get('prestashop.core.admin.lang.repository');
        $locale = $langRepository->getLocaleByIsoCode($isoCode);

        $themeExporter = $this->get('prestashop.translation.theme.exporter');
        $zipFile = $themeExporter->createZipArchive($themeName, $locale, _PS_ROOT_DIR_.DIRECTORY_SEPARATOR);

        $response = new BinaryFileResponse($zipFile);
        $response->deleteFileAfterSend(true);

        $themeExporter->cleanArtifacts($themeName);

        return $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT);
    }

    /**
     * Show translations settings page
     *
     * @Template("@PrestaShop/Admin/Improve/International/Translations/translations_settings.html.twig")
     * @AdminSecurity("is_granted('read', request.get('_legacy_controller'))", message="Access denied.")
     *
     * @param Request $request
     *
     * @return array
     */
    public function showSettingsAction(Request $request)
    {
        $legacyController = $request->attributes->get('_legacy_controller');
        $kpiRowFactory = $this->get('prestashop.core.kpi_row.factory.translations_page');
        $modifyTranslationsForm = $this->createForm(ModifyTranslationsType::class);
        $addUpdateLanguageForm = $this->createForm(AddUpdateLanguageType::class);

        return [
            'layoutTitle' => $this->trans('Translations', 'Admin.Navigation.Menu'),
            'enableSidebar' => true,
            'help_link' => $this->generateSidebarLink($legacyController),
            'kpiRow' => $kpiRowFactory->build(),
            'modifyTranslationsForm' => $modifyTranslationsForm->createView(),
            'addUpdateLanguageForm' => $addUpdateLanguageForm->createView()
        ];
    }

    /**
     * Modify translations action
     *
     * @AdminSecurity("is_granted('read', request.get('_legacy_controller')~'_')", message="Access denied.")
     *
     * @param Request $request
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
     * todo: check available security
     * todo: add iso code validation if it exists in the given choices
     * @param Request $request
     */
    public function addUpdateLanguageAction(Request $request)
    {
        $addUpdateLanguageForm = $this->createForm(AddUpdateLanguageType::class);
        $addUpdateLanguageForm->handleRequest($request);
        $isFormSubmitted = $addUpdateLanguageForm->isSubmitted();

        if ($isFormSubmitted) {
            $data = $addUpdateLanguageForm->getData();
            $isoCode = $data['iso_localization_pack'];

            $languageValidator = $this->get('prestashop.adapter.language.validator');

            $isNewLanguage = !$languageValidator->isInstalledByIsoCode($isoCode);
            $errors = [];

            if ($isNewLanguage) {
                $localizationImportConfig = new LocalizationPackImportConfig(
                    $isoCode,
                    $contentToImport = [
                        LocalizationPackImportConfigInterface::CONTENT_LANGUAGES
                    ],
                    $downloadPackData = true
                );

                $localizationPackImporter = $this->get('prestashop.core.localization.pack.import.importer');
                $errors = $localizationPackImporter->import($localizationImportConfig);
            }

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
}
