<?php
/**
 * 2007-2016 PrestaShop.
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
 * @copyright 2007-2015 PrestaShop SA
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShopBundle\Controller\Admin;

use Doctrine\Common\Util\Inflector;
use PrestaShop\TranslationToolsBundle\Translation\Extractor\Util\Flattenizer;
use PrestashopBundle\Entity\Translation;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
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

        return array('translationsTree' => $translationsTree);
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
    public function extractThemeAction(Request $request)
    {
        $themeName = $request->request->get('theme-name');
        $locale = $this
            ->getDoctrine()
            ->getRepository('PrestaShopBundle:Lang')
            ->findOneByIsoCode($request->request->get('iso_code'))
            ->getLocale()
        ;

        $theme = $this->get('prestashop.core.admin.theme.repository')
            ->getInstanceByName($themeName)
        ;

        $tmpFolderPath = $this->get('kernel')->getCacheDir().'/'.$themeName.'-tmp';
        $folderPath = $this->get('kernel')->getCacheDir().'/'.$themeName;

        $zipFile = $folderPath.'.'.$locale.'.zip';

        // create the directories
        $fs = new Filesystem();
        $fs->mkdir($folderPath);
        $fs->mkdir($tmpFolderPath);

        $themeExtractor = $this->get('prestashop.translations.theme_extractor');
        $themeExtractor
            ->setOutputPath($tmpFolderPath)
            ->enableOverridingFromDatabase()
            ->extract($theme, $locale)
        ;

        Flattenizer::flatten($tmpFolderPath.'/'.$locale, $folderPath.'/'.$locale, $locale);

        $this->get('prestashop.utils.zip_manager')->createArchive($zipFile, $folderPath);

        $response = new BinaryFileResponse($zipFile);
        $response->deleteFileAfterSend(true);

        $fs->remove($tmpFolderPath);
        $fs->remove($folderPath);

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

        $translation = $entityManager->getRepository('PrestaShopBundle:Translation')
            ->findOneBy(array(
                'lang' => $lang,
                'domain' => $requestParams['domain'],
                'key' => $requestParams['translation_key'],
            ));

        if (is_null($translation)) {
            $translation = new Translation();
            $translation->setDomain($requestParams['domain']);
            $translation->setLang($lang);
            $translation->setKey(htmlspecialchars_decode($requestParams['translation_key'], ENT_QUOTES));
            $translation->setTranslation($requestParams['translation_value']);
        } else {
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

        $translator = $this->container->get('translator');

        $factory = ($theme !== 'classic' && $theme !== null) ?
            $this->get('ps.theme_translations_factory') :
            $this->get('ps.translations_factory')
        ;

        $locale = $this->langToLocale($lang);

        if (!is_null($theme) && 'themes' === $type) {
            if ('classic' === $theme) {
                $type = 'front';
            } else {
                $type = $theme;
                $this->synchronizeTheme($theme, $locale);
            }
        }

        $translations = $factory->createTranslationsArray($type, $locale);

        if (!is_null($translations)) {
            return $translations;
        }

        // if type is not found, return all keys
        $finder = new Finder();
        $translationFiles = $finder->files()->in($this->getResourcesDirectory().'/translations/'.$locale);

        $translationLocale = str_replace('-', '_', $locale);

        if (count($translationFiles) === 0) {
            throw new \Exception('There is no translation file available');
        }

        foreach ($translationFiles as $file) {
            $translator->addResource('xlf', $file->getPathname(), $translationLocale, $file->getBasename('.xlf'));
        }

        $catalogue = $translator->getCatalogue($translationLocale)->all();
        $databaseCatalogue = $this->getTranslationsInDatabase($locale);

        foreach ($databaseCatalogue as $domain => $messages) {
            foreach ($messages as $translationKey => $translationValue) {
                $catalogue[$domain][$translationKey] = array(
                    // Xliff-based translation stored for reset action
                    'xlf' => $catalogue[$domain][$translationKey],
                    'db' => $translationValue,
                );
            }
        }

        ksort($catalogue);

        return $catalogue;
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

            $subtree = &$translationsTree;

            while (count($parts) > 0) {
                $subdomain = ucfirst(array_pop($parts));
                if (array_key_exists($subdomain, $flippedUnbreakableWords)) {
                    $subdomain = $flippedUnbreakableWords[$subdomain];
                }

                if (!array_key_exists($subdomain, $subtree)) {
                    $subtree[$subdomain] = array();
                }
                $subtree = &$subtree[$subdomain];
            }

            $subtree['__messages'] = array($domain => $messages);
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
            'ContactInfo' => 'Contactinfo',
            'EmailSubscription' => 'Emailsubscription',
            'FeaturedProducts' => 'Featuredproducts',
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
     *
     * @return array
     */
    protected function getTranslationsInDatabase($locale)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $translations = $entityManager->getRepository('PrestaShopBundle:Translation')
            ->findBy(array(
                'lang' => $this->findLanguageByLocale($locale),
            ));

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

    private function synchronizeTheme($themeName, $locale)
    {
        $theme = $this
            ->get('prestashop.core.admin.theme.repository')
            ->getInstanceByName($themeName)
        ;

        $path = $this->getParameter('themes_dir').'/'.$themeName.'/translations';
        $this->get('filesystem')->remove($path);
        $this->get('filesystem')->mkdir($path);

        $this->get('prestashop.translations.theme_extractor')
            ->setOutputPath($path)
            ->extract($theme, $locale)
        ;

        $translationFilesPath = $path.'/'.$locale;
        Flattenizer::flatten($translationFilesPath, $translationFilesPath, $locale, false);

        foreach ($this->get('finder')->directories()->depth('== 0')->in($translationFilesPath) as $folder) {
            $this->get('filesystem')->remove($folder);
        }
    }
}
