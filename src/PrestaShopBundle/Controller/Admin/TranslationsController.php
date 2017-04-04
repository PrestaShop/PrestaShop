<?php
/**
 * 2007-2017 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
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
 * @copyright 2007-2017 PrestaShop SA
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShopBundle\Controller\Admin;

use Doctrine\Common\Util\Inflector;
use PrestashopBundle\Entity\Translation;
use PrestaShopBundle\Translation\Provider\ModuleProvider;
use PrestaShopBundle\Translation\View\TreeBuilder;
use PrestaShopBundle\Security\Voter\PageVoter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

/**
 * Admin controller for the International pages.
 */
class TranslationsController extends FrameworkBundleAdminController
{
    const controller_name = 'ADMINTRANSLATIONS';
    /**
     * List translations keys and corresponding editable values.
     *
     * @Template
     *
     * @param Request $request
     *
     * @return array Template vars
     */
    public function listAction(Request $request)
    {
        if (!$request->isMethod('POST')) {
            return $this->redirect('./admin-dev/index.php?controller=AdminTranslations');
        }

        if (
            !in_array(
                $this->authorizationLevel($this::controller_name),
                array(
                    PageVoter::LEVEL_READ,
                    PageVoter::LEVEL_UPDATE,
                    PageVoter::LEVEL_CREATE,
                    PageVoter::LEVEL_DELETE,
                )
            )
        ) {
            return $this->redirect('admin_dashboard');
        }

        $lang = $request->get('lang');
        $theme = $request->get('selected-theme');

        $catalogue = $this->getTranslationsCatalogue($request);
        $treeBuilder = new TreeBuilder($this->langToLocale($lang), $theme);
        $translationsTree = $treeBuilder->makeTranslationsTree($catalogue);
        $editable = $this->isGranted(PageVoter::UPDATE, $this::controller_name.'_');

        return array(
            'translationsTree' => $translationsTree,
            'theme' => $this->getSelectedTheme($request),
            'requestParams' => array(
                'lang' => $request->get('lang'),
                'type' => $request->get('type'),
                'theme' => $request->get('selected-theme'),
            ),
            'total_remaining_translations' => $this->get('translator')->trans(
                '%nb_translations% missing',
                array('%nb_translations%' => '%d'),
                'Admin.International.Feature'
            ),
            'total_translations' => $this->get('translator')->trans(
                '%d expressions',
                array(),
                'Admin.International.Feature'
            ),
            'editable' => $editable,
        );
    }

    /**
     * List translations keys and corresponding editable values for one module.
     *
     * @Template("@PrestaShop/Admin/Translations/list.html.twig")
     *
     * @param Request $request
     *
     * @return array Template vars
     */
    public function moduleAction(Request $request)
    {
        if (!$request->isMethod('POST')) {
            return $this->redirect('./admin-dev/index.php?controller=AdminTranslations');
        }

        $lang = $request->get('lang');
        $theme = $request->get('selected-theme');
        $module = $request->get('selected-modules');

        $moduleProvider = new ModuleProvider(
            $this->container->get('prestashop.translation.database_loader'),
            $this->container->getParameter('translations_dir')
        );
        $moduleProvider->setModuleName($module);

        $treeBuilder = new TreeBuilder($this->langToLocale($lang), $theme);
        $catalogue = $treeBuilder->makeTranslationArray($moduleProvider);

        return array(
            'translationsTree' => $treeBuilder->makeTranslationsTree($catalogue),
            'theme' => $this->getSelectedTheme($request),
            'requestParams' => array(
                'lang' => $lang,
                'type' => $request->get('type'),
                'theme' => $theme,
            ),
            'total_remaining_translations' => $this->get('translator')->trans(
                '%nb_translations% missing',
                array('%nb_translations%' => '%d'),
                'Admin.International.Feature'
            ),
            'total_translations' => $this->get('translator')->trans(
                '%d expressions',
                array(),
                'Admin.International.Feature'
            )
        );
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function messagesFragmentsAction(Request $request)
    {
        $theme = $this->getSelectedTheme($request);
        $catalogue = $this->getTranslationsCatalogue($request);
        $treeBuilder = new TreeBuilder($request->get('lang'), $theme);
        $translationsTree = $treeBuilder->makeTranslationsTree($catalogue);

        $translationsFormsView = $this->renderView(
            'PrestaShopBundle:Admin/Translations/include:translations-forms.html.twig',
            array(
                'translationsTree' => $translationsTree,
                'theme' => $theme,
            )
        );
        $translationsTreeView = $this->renderView(
            'PrestaShopBundle:Admin/Translations/include:translations-tree.html.twig',
            array(
                'translationsTree' => $translationsTree,
                'theme' => $theme,
            )
        );

        return new JsonResponse(array(
            'translations_forms' => $translationsFormsView,
            'translations_tree' => $translationsTreeView,
        ));
    }

    private function getSelectedTheme(Request $request)
    {
        if ($request->get('type') === 'themes') {
            return $request->get('selected-theme');
        } else {
            return null;
        }
    }

    /**
     * Edit a translation value.
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function editAction(Request $request)
    {
        $updatedTranslationSuccessfully = $this->saveTranslationMessage($request);
        $this->clearCache();

        return new JsonResponse(array(
            'successful_update' => $updatedTranslationSuccessfully,
            'translation_value' => $request->request->get('translation_value'),
        ));
    }

    /**
     * extract theme using locale and theme name.
     *
     * @param Request $request
     *
     * @return file to be downloaded
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
     * @param Request $request
     *
     * @return bool
     */
    protected function saveTranslationMessage(Request $request)
    {
        $requestParams = $request->request->all();
        $entityManager = $this->getDoctrine()->getManager();

        $lang = $this->findLanguageByLocale($requestParams['locale']);

        $theme = $requestParams['theme'];
        if (empty($requestParams['theme'])) {
            $theme = null;
        }

        /**
         * @var \PrestaShopBundle\Entity\Translation $translation
         */
        $translation = $entityManager->getRepository('PrestaShopBundle:Translation')
            ->findOneBy(array(
                'lang' => $lang,
                'domain' => $requestParams['domain'],
                'key' => $requestParams['translation_key'],
                'theme' => $theme
            ));

        if (is_null($translation)) {
            $translation = new Translation();
            $translation->setDomain($requestParams['domain']);
            $translation->setLang($lang);
            $translation->setKey(htmlspecialchars_decode($requestParams['translation_key'], ENT_QUOTES));
            $translation->setTranslation($requestParams['translation_value']);
            $translation->setTheme($theme);
        } else {
            $translation->setTheme($theme);
            $translation->setTranslation($requestParams['translation_value']);
        }

        $updatedTranslationSuccessfully = false;

        try {
            $entityManager->persist($translation);
            $entityManager->flush();

            $updatedTranslationSuccessfully = true;
        } catch (\Exception $exception) {
            $this->container->get('logger')->error($exception->getMessage());
        }

        return $updatedTranslationSuccessfully;
    }

    /**
     * @see \Symfony\Bundle\FrameworkBundle\Command\CacheClearCommand
     */
    protected function clearCache()
    {
        $cacheRefresh = $this->container->get('prestashop.cache.refresh');

        try {
            $cacheRefresh->execute();
        } catch (\Exception $exception) {
            $this->container->get('logger')->error($exception->getMessage());
        }
    }

    /**
     * @param $locale
     *
     * @return mixed
     */
    protected function findLanguageByLocale($locale)
    {
        return $this->getDoctrine()->getManager()
            ->getRepository('PrestaShopBundle:Lang')->findOneByLocale($locale);
    }

    /**
     * @param Request $request
     *
     * @return \Symfony\Component\Translation\MessageCatalogue
     *
     * @throws \Exception
     */
    protected function getTranslationsCatalogue(Request $request)
    {
        $lang = $request->get('lang');
        $type = $request->get('type');
        $theme = $request->get('selected-theme');

        $factory = $this->get('ps.translations_factory');
        if ($theme !== 'classic' && $this->requiresThemeTranslationsFactory($theme, $type)) {
            $factory = $this->get('ps.theme_translations_factory');
        }

        $locale = $this->langToLocale($lang);

        if ($this->requiresThemeTranslationsFactory($theme, $type)) {
            if ('classic' === $theme) {
                $type = 'front';
            } else {
                $type = $theme;
            }
        }

        return $factory->createTranslationsArray($type, $locale);
    }

    /**
     * @param $theme
     * @param $type
     * @return bool
     */
    private function requiresThemeTranslationsFactory($theme, $type)
    {
        return $type === 'themes' && !is_null($theme);
    }

    /**
     * @param $catalogue
     *
     * @return array
     *
     * @deprecated since 1.7.1.0
     */
    protected function makeTranslationsTree(array $catalogue)
    {
        trigger_error('makeTranslationsTree() is deprecated since version 1.7.1. Use PrestaShopBundle\Translation\View\TreeBuilder instead.', E_USER_DEPRECATED);

        $treeBuilder = new TreeBuilder('en-US', null);
        return $treeBuilder->makeTranslationsTree($catalogue);
    }

    /**
     * There are domains containing multiple words,
     * hence these domains should not be split from those words in camelcase.
     * The latter are replaced from a list of unbreakable words.
     *
     * @param $domain
     *
     * @return string
     *
     * @deprecated since 1.7.1.0
     */
    protected function makeDomainUnbreakable($domain)
    {
        trigger_error('makeDomainUnbreakable() is deprecated since version 1.7.1. Use PrestaShopBundle\Translation\View\TreeBuilder instead.', E_USER_DEPRECATED);

        $treeBuilder = new TreeBuilder('en-US', null);
        return $treeBuilder->makeDomainUnbreakable($domain);
    }

    /**
     * @return array
     *
     * @deprecated since 1.7.1.0
     */
    protected function getUnbreakableWords()
    {
        trigger_error('getUnbreakableWords() is deprecated since version 1.7.1. Use PrestaShopBundle\Translation\View\TreeBuilder instead.', E_USER_DEPRECATED);

        $treeBuilder = new TreeBuilder('en-US', null);
        return $treeBuilder->getUnbreakableWords();
    }

    /**
     * @param $locale
     * @param $theme
     * @return array
     */
    protected function getTranslationsInDatabase($locale, $theme = null)
    {
        $translationRepository = $this->get('prestashop.core.admin.translation.repository');
        $translations = $translationRepository->findByLanguageAndTheme(
            $this->findLanguageByLocale($locale),
            $theme
        );

        $translationsMap = array();
        array_map(function ($translation) use (&$translationsMap, $locale) {
            $domainLocale = $translation->getDomain().'.'.$locale;
            if (!array_key_exists($domainLocale, $translationsMap)) {
                $translationsMap[$domainLocale] = array();
            }

            $translationsMap[$domainLocale][$translation->getKey()] = $translation->getTranslation();
        }, $translations);

        return $translationsMap;
    }
}
