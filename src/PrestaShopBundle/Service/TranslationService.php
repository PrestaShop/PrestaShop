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

namespace PrestaShopBundle\Service;

use PrestaShopBundle\Entity\Translation;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\HttpFoundation\Request;

class TranslationService {

    /**
     * @var Container
     */
    public $container;

    /**
     * @param $lang
     * @return mixed
     */
    public function langToLocale($lang)
    {
        $legacyToStandardLocales = $this->getLangToLocalesMapping();

        return $legacyToStandardLocales[$lang];
    }

    /**
     * @param $locale
     *
     * @return mixed
     */
    protected function findLanguageByLocale($locale)
    {
        $doctrine = $this->container->get('doctrine');

        $lang = $doctrine->getManager()->getRepository('PrestaShopBundle:Lang')->findOneByLocale($locale);

        if (!$lang) {
            throw new \Exception('The language for this locale is not available');
        }

        return $lang;
    }

    /**
     * @return mixed
     * @throws \Exception
     */
    private function getLangToLocalesMapping()
    {
        $translationsDirectory = $this->getResourcesDirectory();

        $legacyToStandardLocalesJson = file_get_contents($translationsDirectory . '/legacy-to-standard-locales.json');
        $legacyToStandardLocales = json_decode($legacyToStandardLocalesJson, true);

        $jsonLastErrorCode = json_last_error();
        if (JSON_ERROR_NONE !== $jsonLastErrorCode) {
            throw new \Exception('The legacy to standard locales JSON could not be decoded', $jsonLastErrorCode);
        }

        return $legacyToStandardLocales;
    }

    /**
     * @return string
     */
    private function getResourcesDirectory()
    {
        return $this->container->getParameter('kernel.root_dir') . '/Resources';
    }

    /**
     * @param $lang
     * @param $type
     * @param $selected
     *
     * @return mixed
     */
    public function getTranslationsCatalogue($lang, $type, $selected)
    {
        $factory = $this->container->get('ps.translations_factory');

        if ($selected !== 'classic' && $this->requiresThemeTranslationsFactory($selected, $type)) {
            $factory = $this->container->get('ps.theme_translations_factory');
        }

        $locale = $this->langToLocale($lang);

        if ($this->requiresThemeTranslationsFactory($selected, $type)) {
            if ('classic' === $selected) {
                $type = 'front';
            } else {
                $type = $selected;
            }
        }

        return $factory->createTranslationsArray($type, $locale);
    }

    /**
     * @param $theme
     * @param $type
     *
     * @return bool
     */
    private function requiresThemeTranslationsFactory($theme, $type)
    {
        return $type === 'themes' && !is_null($theme);
    }

    /**
     * List translation for domain
     *
     * @param $locale
     * @param $domain
     * @return array|mixed
     */
    public function listDomainTranslation($locale, $domain){
        $translationProvider = $this->container->get('prestashop.translation.search_provider');
        $router = $this->container->get('router');
        $paramsRouter = array(
            'locale' => $locale,
            'domain' => $domain,
        );

        $translationProvider->setLocale($locale);
        $translationProvider->setDomain($domain);

        $domains = array(
            'info' => array(
                'edit_url' => $router->generate('api_translation_value_edit', $paramsRouter),
                'reset_url' => $router->generate('api_translation_value_reset', $paramsRouter),
            )
        );
        $treeDomain = preg_split('/(?=[A-Z])/', $domain, -1, PREG_SPLIT_NO_EMPTY);

        $defaultCatalog = current($translationProvider->getDefaultCatalogue()->all());
        $xliffCatalog = current($translationProvider->getXliffCatalogue()->all());
        $dbCatalog = current($translationProvider->getDatabaseCatalogue()->all());

        foreach ($defaultCatalog as $message) {
            $domains['data'][] = array(
                'default' => $message,
                'xliff' => (array_key_exists($message, (array)$xliffCatalog) ? $xliffCatalog[$message] : null),
                'database' => (array_key_exists($message, (array)$dbCatalog) ? $dbCatalog[$message] : null),
                'tree_domain' => $treeDomain,
            );
        }

        return $domains;
    }

    /**
     * Save a translation in database
     *
     * @param $locale
     * @param $domain
     * @param $key
     * @param $translationValue
     * @param null $theme
     * @return bool
     */
    public function saveTranslationMessage($locale, $domain, $key, $translationValue, $theme = null)
    {
        $doctrine = $this->container->get('doctrine');
        $entityManager = $doctrine->getManager();

        $lang = $this->findLanguageByLocale($locale);

        if (empty($theme)) {
            $theme = null;
        }

        $translation = $entityManager->getRepository('PrestaShopBundle:Translation')
            ->findOneBy(array(
                'lang' => $lang,
                'domain' => $domain,
                'key' => $key,
                'theme' => $theme
            ));

        if (is_null($translation)) {
            $translation = new Translation();
            $translation->setDomain($domain);
            $translation->setLang($lang);
            $translation->setKey(htmlspecialchars_decode($key, ENT_QUOTES));
            $translation->setTranslation($translationValue);
            $translation->setTheme($theme);
        } else {
            $translation->setTheme($theme);
            $translation->setTranslation($translationValue);
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
     * Reset translation from database
     *
     * @param $locale
     * @param $domain
     * @param $key
     * @param null $theme
     * @return bool
     */
    public function resetTranslationMessage($locale, $domain, $key, $theme = null)
    {
        $doctrine = $this->container->get('doctrine');
        $entityManager = $doctrine->getManager();

        $lang = $this->findLanguageByLocale($locale);

        $translation = $entityManager->getRepository('PrestaShopBundle:Translation')
            ->findOneBy(array(
                'lang' => $lang,
                'domain' => $domain,
                'key' => $key,
                'theme' => $theme
            ));

        $resetTranslationSuccessfully = false;

        if (is_null($translation)) {
            $resetTranslationSuccessfully = true;
        }

        try {
            $entityManager->remove($translation);
            $entityManager->flush();

            $resetTranslationSuccessfully = true;
        } catch (\Exception $exception) {
            $this->container->get('logger')->error($exception->getMessage());
        }

        return $resetTranslationSuccessfully;
    }


}
