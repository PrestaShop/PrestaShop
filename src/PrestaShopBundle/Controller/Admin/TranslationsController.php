<?php
/**
 * 2007-2017 PrestaShop
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
 * @copyright 2007-2017 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShopBundle\Controller\Admin;

use PrestashopBundle\Entity\Translation;
<<<<<<< d510188f37a12d27c1f08601b5c9a1c95ad35b90
use PrestaShopBundle\Translation\Constraints\PassVsprintf;
use PrestaShopBundle\Translation\Provider\ModuleProvider;
||||||| merged common ancestors
use PrestaShopBundle\Translation\Provider\ModuleProvider;
=======
>>>>>>> CO: some refacto on controllers..
use PrestaShopBundle\Translation\View\TreeBuilder;
use PrestaShopBundle\Security\Voter\PageVoter;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Validator\Validation;

/**
 * Admin controller for the International pages.
 */
class TranslationsController extends FrameworkBundleAdminController
{
    const controller_name = 'ADMINTRANSLATIONS';

    // overview method on FrameworkBundleAdminController for all vue-js app
    // redirect to the new translation application
    // before, clean request params
     private function redirectToTranslationApp(Request $request)
    {
        $params = array();
        foreach ($request->request->all() as $k => $p) {
            if (strstr($k, 'selected')) {
                $k = 'selected';
            } else if ('locale' === $k) {
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
     * @param Request $request
     * @return JsonResponse
     */
    public function messagesFragmentsAction(Request $request)
    {
        $theme = $this->getSelectedTheme($request);

        $translationService = $this->get('prestashop.service.translation');
        $catalogue = $translationService->getTranslationsCatalogue(
            $request->get('lang'),
            $request->get('type'),
            $request->get('selected-theme')
        );

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
<<<<<<< 22d7add5c759c1e5fd1c63ad96a15076cc538437
     * @param Request $request
     *
     * @return bool
     */
    protected function saveTranslationMessage(Request $request)
    {
        $requestParams = $request->request->all();
        $entityManager = $this->getDoctrine()->getManager();
        $logger = $this->container->get('logger');

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

        $validator = Validation::createValidator();
        $violations = $validator->validate($translation, new PassVsprintf);
        if (0 !== count($violations)) {
            foreach ($violations as $violation) {
                $logger->error($violation->getMessage());
            }
            return false;
        }

        $updatedTranslationSuccessfully = false;

        try {
            $entityManager->persist($translation);
            $entityManager->flush();

            $updatedTranslationSuccessfully = true;
        } catch (\Exception $exception) {
            $logger->error($exception->getMessage());
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
        return $this->getDoctrine()->getManager()->getRepository('PrestaShopBundle:Lang')->findOneByLocale($locale);
    }

    /**
||||||| merged common ancestors
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
        return $this->getDoctrine()->getManager()->getRepository('PrestaShopBundle:Lang')->findOneByLocale($locale);
    }

    /**
=======
>>>>>>> BO: clean old translation controller
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
}
