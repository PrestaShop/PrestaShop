<?php
/**
 * 2007-2016 PrestaShop
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
 * @copyright 2007-2016 PrestaShop SA
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShopBundle\Controller\Admin;

use Doctrine\Common\Util\Inflector;
use PrestashopBundle\Entity\Translation;
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
            return $this->redirect('/admin-dev/index.php?controller=AdminTranslations');
        }

        $catalogue = $this->getTranslationsCatalogue($request);
        $translationsTree = $this->makeTranslationsTree($catalogue);

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
        $translationsTree = $this->makeTranslationsTree($catalogue);

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
        $zipFile = $themeExporter->createZipArchive($themeName, $locale);

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

        /**
         * @var \PrestaShopBundle\Entity\Translation $translation
         */
        $translation = $entityManager->getRepository('PrestaShopBundle:Translation')
            ->findOneBy(array(
                'lang' => $lang,
                'domain' => $requestParams['domain'],
                'key' => $requestParams['translation_key'],
                'theme' => $requestParams['theme']
            ));

        $theme = $requestParams['theme'];
        if (empty($requestParams['theme'])) {
            $theme = null;
        }

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
     */
    protected function makeTranslationsTree(array $catalogue)
    {
        $translationsTree = array();
        $flippedUnbreakableWords = array_flip($this->getUnbreakableWords());

        foreach ($catalogue as $domain => $messages) {
            $unbreakableDomain = $this->makeDomainUnbreakable($domain);

            $tableisedDomain = Inflector::tableize($unbreakableDomain);
            list($basename) = explode('.', $tableisedDomain);
            $parts = array_reverse(explode('_', $basename));

            $totalParts = count($parts);
            $subtree = &$translationsTree;

            if ($totalParts - 2 < 0) {
                $totalParts = 2;
                $parts = array($parts[0], 'Admin');
            }

            $firstDomainPart = $parts[count($parts) - 1];

            $condition = count($parts) > $totalParts - 2;
            $depth = 0;

            while ($condition) {
                if ($depth === 1) {
                    list($subdomain) = explode('.', str_replace(ucfirst($firstDomainPart), '', $domain));
                    array_pop($parts);
                } else {
                    $subdomain = ucfirst(array_pop($parts));
                    if (array_key_exists($subdomain, $flippedUnbreakableWords)) {
                        $subdomain = $flippedUnbreakableWords[$subdomain];
                    }
                }

                if (!array_key_exists($subdomain, $subtree)) {
                    $subtree[$subdomain] = array();
                }
                $subtree = &$subtree[$subdomain];

                $condition = count($parts) > $totalParts - 2;
                $depth++;

                if ($depth === 2) {
                    $subtree['__fixed_length_id'] = '_' . sha1($domain);
                    list($subtree['__domain']) = explode('.', $domain);

                    $subtree['__metadata'] = $messages['__metadata'];
                    $subtree['__metadata']['domain'] = $subtree['__domain'];
                    unset($messages['__metadata']);
                }
            }

            $subtree['__messages'] = array($domain => $messages);
            unset($catalogue[$domain]);
        }

        return $translationsTree;
    }

    /**
     * There are domains containing multiple words,
     * hence these domains should not be split from those words in camelcase.
     * The latter are replaced from a list of unbreakable words.
     *
     * @param $domain
     *
     * @return string
     */
    protected function makeDomainUnbreakable($domain)
    {
        $adjustedDomain = $domain;
        $unbreakableWords = $this->getUnbreakableWords();

        foreach ($unbreakableWords as $search => $replacement) {
            if (false !== strpos($domain, $search)) {
                $adjustedDomain = str_replace($search, $replacement, $domain);

                break;
            }
        }

        return $adjustedDomain;
    }

    /**
     * @return array
     */
    protected function getUnbreakableWords()
    {
        return array(
            'BankWire' => 'Bankwire',
            'BlockBestSellers' => 'Blockbestsellers',
            'BlockCart' => 'Blockcart',
            'CheckPayment' => 'Checkpayment',
            'ContactInfo' => 'Contactinfo',
            'EmailSubscription' => 'Emailsubscription',
            'FacetedSearch' => 'Facetedsearch',
            'FeaturedProducts' => 'Featuredproducts',
            'LegalCompliance' => 'Legalcompliance',
            'ShareButtons' => 'Sharebuttons',
            'ShoppingCart' => 'Shoppingcart',
            'SocialFollow' => 'Socialfollow',
            'WirePayment' => 'Wirepayment',
            'BlockAdvertising' => 'Blockadvertising',
            'CategoryTree' => 'Categorytree',
            'CustomerSignIn' => 'Customersignin',
            'CustomText' => 'Customtext',
            'ImageSlider' => 'Imageslider',
            'LinkList' => 'Linklist',
            'ShopPDF' => 'ShopPdf',
        );
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
