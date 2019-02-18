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

namespace PrestaShopBundle\Service;

use Exception;
use PrestaShopBundle\Entity\Translation;
use PrestaShopBundle\Translation\Constraints\PassVsprintf;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\Validator\Validation;

class TranslationService
{
    /**
     * @var Container
     */
    public $container;

    /**
     * @param $lang
     *
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
     *
     * @throws Exception
     */
    public function findLanguageByLocale($locale)
    {
        $doctrine = $this->container->get('doctrine');

        $lang = $doctrine->getManager()->getRepository('PrestaShopBundle:Lang')->findOneByLocale($locale);

        if (!$lang) {
            throw new Exception('The language for this locale is not available');
        }

        return $lang;
    }

    /**
     * @return mixed
     *
     * @throws Exception
     */
    private function getLangToLocalesMapping()
    {
        $translationsDirectory = $this->getResourcesDirectory();

        $legacyToStandardLocalesJson = file_get_contents($translationsDirectory . '/legacy-to-standard-locales.json');
        $legacyToStandardLocales = json_decode($legacyToStandardLocalesJson, true);

        $jsonLastErrorCode = json_last_error();
        if (JSON_ERROR_NONE !== $jsonLastErrorCode) {
            throw new Exception('The legacy to standard locales JSON could not be decoded', $jsonLastErrorCode);
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
     * @param null $search
     *
     * @return array|mixed
     */
    public function getTranslationsCatalogue($lang, $type, $selected, $search = null)
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

        return $factory->createTranslationsArray($type, $locale, $selected, $search);
    }

    /**
     * @param $theme
     * @param $type
     *
     * @return bool
     */
    private function requiresThemeTranslationsFactory($theme, $type)
    {
        return $type === 'themes' && null !== $theme;
    }

    /**
     * List translation for domain.
     *
     * @todo: we need module information here
     * @todo: we need to improve the Vuejs application to send the information
     *
     * @param $locale
     * @param $domain
     * @param null $theme
     * @param null $search
     *
     * @return array
     */
    public function listDomainTranslation($locale, $domain, $theme = null, $search = null)
    {
        /*
         * @todo: needs refacto to call the right provider according to the parameters
         */
        if (!empty($theme) && 'classic' !== $theme) {
            $translationProvider = $this->container->get('prestashop.translation.theme_provider');
            $translationProvider->setThemeName($theme);
        } else {
            $translationProvider = $this->container->get('prestashop.translation.external_module_provider');
            $translationProvider->setModuleName('ps_themecusto');
        }

        if ('Messages' === $domain) {
            $domain = 'messages';
        }

        $translationProvider->setDomain($domain);
        $translationProvider->setLocale($locale);

        $router = $this->container->get('router');
        $domains = [
            'info' => [
                'edit_url' => $router->generate('api_translation_value_edit'),
                'reset_url' => $router->generate('api_translation_value_reset'),
            ],
            'data' => [],
        ];
        $treeDomain = preg_split('/(?=[A-Z])/', $domain, -1, PREG_SPLIT_NO_EMPTY);

        if (!empty($theme) && 'classic' !== $theme) {
            $defaultCatalog = current($translationProvider->getThemeCatalogue()->all());
        } else {
            $defaultCatalog = current($translationProvider->getDefaultCatalogue()->all());
        }

        $xliffCatalog = method_exists($translationProvider, 'getLegacyCatalogue') ? $translationProvider->getLegacyCatalogue()->all($domain) : $translationProvider->getXliffCatalogue()->all();

        if ('EmailsSubject' === $domain) {
            $theme = 'subject';
        }
        $dbCatalog = current($translationProvider->getDatabaseCatalogue($theme)->all());

        foreach ($defaultCatalog as $key => $message) {
            $data = array(
                'default' => $key,
                'xliff' => (array_key_exists($key, (array) $xliffCatalog) ? $xliffCatalog[$key] : null),
                'database' => (array_key_exists($key, (array) $dbCatalog) ? $dbCatalog[$key] : null),
                'tree_domain' => $treeDomain,
            );

            // if search is empty or is in catalog default|xlf|database
            if (empty($search) || $this->dataContainsSearchWord($search, $data)) {
                if (empty($data['xliff']) && empty($data['database'])) {
                    array_unshift($domains['data'], $data);
                } else {
                    $domains['data'][] = $data;
                }
            }
        }

        dump($domains);

        return $domains;
    }

    /**
     * Check if data contains search word.
     *
     * @param $search
     * @param $data
     *
     * @return bool
     */
    private function dataContainsSearchWord($search, $data)
    {
        if (is_string($search)) {
            $search = strtolower($search);

            return false !== strpos(strtolower($data['default']), $search) ||
                false !== strpos(strtolower($data['xliff']), $search) ||
                false !== strpos(strtolower($data['database']), $search);
        }

        if (is_array($search)) {
            $contains = true;
            foreach ($search as $s) {
                $s = strtolower($s);
                $contains &= false !== strpos(strtolower($data['default']), $s) ||
                    false !== strpos(strtolower($data['xliff']), $s) ||
                    false !== strpos(strtolower($data['database']), $s);
            }

            return $contains;
        }

        return false;
    }

    /**
     * Save a translation in database.
     *
     * @param $lang
     * @param $domain
     * @param $key
     * @param $translationValue
     * @param null $theme
     *
     * @return bool
     */
    public function saveTranslationMessage($lang, $domain, $key, $translationValue, $theme = null)
    {
        $doctrine = $this->container->get('doctrine');
        $entityManager = $doctrine->getManager();
        $logger = $this->container->get('logger');

        if (empty($theme)) {
            $theme = null;
        }

        $translation = $entityManager->getRepository('PrestaShopBundle:Translation')
            ->findOneBy(array(
                'lang' => $lang,
                'domain' => $domain,
                'key' => $key,
                'theme' => $theme,
            ));

        if (null === $translation) {
            $translation = new Translation();
            $translation->setDomain($domain);
            $translation->setLang($lang);
            $translation->setKey(htmlspecialchars_decode($key, ENT_QUOTES));
            $translation->setTranslation($translationValue);
            if (!empty($theme)) {
                $translation->setTheme($theme);
            }
        } else {
            if (!empty($theme)) {
                $translation->setTheme($theme);
            }
            $translation->setTranslation($translationValue);
        }

        $validator = Validation::createValidator();
        $violations = $validator->validate($translation, new PassVsprintf());
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
        } catch (Exception $exception) {
            $logger->error($exception->getMessage());
        }

        return $updatedTranslationSuccessfully;
    }

    /**
     * Reset translation from database.
     *
     * @param $lang
     * @param $domain
     * @param $key
     * @param null $theme
     *
     * @return bool
     */
    public function resetTranslationMessage($lang, $domain, $key, $theme = null)
    {
        $doctrine = $this->container->get('doctrine');
        $entityManager = $doctrine->getManager();

        $searchTranslation = array(
            'lang' => $lang,
            'domain' => $domain,
            'key' => $key,
        );
        if (!empty($theme)) {
            $searchTranslation['theme'] = $theme;
        }

        $translation = $entityManager->getRepository('PrestaShopBundle:Translation')->findOneBy($searchTranslation);

        $resetTranslationSuccessfully = false;
        if (null === $translation) {
            $resetTranslationSuccessfully = true;
        }

        try {
            $entityManager->remove($translation);
            $entityManager->flush();

            $resetTranslationSuccessfully = true;
        } catch (Exception $exception) {
            $this->container->get('logger')->error($exception->getMessage());
        }

        return $resetTranslationSuccessfully;
    }
}
