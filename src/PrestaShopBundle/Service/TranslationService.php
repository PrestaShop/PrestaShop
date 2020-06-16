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

namespace PrestaShopBundle\Service;

use Exception;
use PrestaShopBundle\Entity\Translation;
use PrestaShopBundle\Exception\InvalidLanguageException;
use PrestaShopBundle\Translation\Constraints\PassVsprintf;
use PrestaShopBundle\Translation\Provider\TranslationsCatalogueProvider;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\Validator\Validation;

class TranslationService
{
    /**
     * @deprecated Since 1.7.7.0
     */
    const DEFAULT_THEME = 'classic';

    /**
     * @var Container
     */
    public $container;

    /**
     * @var string
     */
    private $defaultTheme;

    public function __construct($defaultTheme)
    {
        $this->defaultTheme = $defaultTheme;
    }

    /**
     * @param string $lang
     *
     * @return mixed
     */
    public function langToLocale($lang)
    {
        $legacyToStandardLocales = $this->getLangToLocalesMapping();

        return $legacyToStandardLocales[$lang];
    }

    /**
     * @param string $locale
     *
     * @return mixed
     *
     * @throws InvalidLanguageException
     */
    public function findLanguageByLocale($locale)
    {
        $doctrine = $this->container->get('doctrine');

        $lang = $doctrine->getManager()->getRepository('PrestaShopBundle:Lang')->findOneByLocale($locale);

        if (!$lang) {
            throw InvalidLanguageException::localeNotFound($locale);
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
     * Returns list translations by domain
     *
     * @param string $lang
     * @param string $type
     * @param string $theme
     * @param string|null $search
     *
     * @return array
     */
    public function getTranslationsCatalogue($lang, $type, $theme, $search = null)
    {
        /**
         * @var TranslationsCatalogueProvider
         */
        $provider = $this->container->get('prestashop.translation.translations_provider');

        $provider
            ->setLocale($this->langToLocale($lang))
            ->setType($type)
            ->setTheme($theme)
        ;

        return $provider->getCatalogue($search);

//        $factory = $this->container->get('prestashop.translation.translations_factory');
//
//        if ($this->requiresThemeTranslationsFactory($theme, $type)) {
//            if ($this->isDefaultTheme($theme)) {
//                $type = 'front';
//            } else {
//                $type = $theme;
//                $factory = $this->container->get('prestashop.translation.theme_translations_factory');
//            }
//        }
//
//        return $factory->createTranslationsArray($type, $this->langToLocale($lang), $theme, $search);
    }

    /**
     * @param string|null $theme
     * @param string $type
     *
     * @return bool
     */
    private function requiresThemeTranslationsFactory($theme, $type)
    {
        return $type === 'themes' && null !== $theme;
    }

    /**
     * List translations for a specific domain.
     *
     * @todo: we need module information here
     * @todo: we need to improve the Vuejs application to send the information
     *
     * @param string $locale
     * @param string $domain
     * @param string|null $theme
     * @param string|array|null $search
     * @param string|null $module
     *
     * @return array
     */
    public function listDomainTranslation(
        string $locale,
        string $domain,
        ?string $theme = null,
        $search = null,
        ?string $module = null
    ): array {
        /**
         * @var TranslationsCatalogueProvider
         */
        $provider = $this->container->get('prestashop.translation.translations_provider');

        $provider
            ->setLocale($locale)
            ->setTheme($theme)
        ;

        $router = $this->container->get('router');

        return [
            'info' => [
                'edit_url' => $router->generate('api_translation_value_edit'),
                'reset_url' => $router->generate('api_translation_value_reset'),
            ],
            'data' => $provider->getDomainCatalogue($domain, $search, $module),
        ];
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
            ->findOneBy([
                'lang' => $lang,
                'domain' => $domain,
                'key' => $key,
                'theme' => $theme,
            ]);

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
        $log_context = ['object_type' => 'Translation'];
        if (0 !== count($violations)) {
            foreach ($violations as $violation) {
                $logger->error($violation->getMessage(), $log_context);
            }

            return false;
        }

        $updatedTranslationSuccessfully = false;

        try {
            $entityManager->persist($translation);
            $entityManager->flush();

            $updatedTranslationSuccessfully = true;
        } catch (Exception $exception) {
            $logger->error($exception->getMessage(), $log_context);
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

        $searchTranslation = [
            'lang' => $lang,
            'domain' => $domain,
            'key' => $key,
        ];
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
