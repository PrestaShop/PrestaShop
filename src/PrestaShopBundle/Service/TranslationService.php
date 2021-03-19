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
use PrestaShop\PrestaShop\Core\Translation\Storage\Provider\Definition\CoreDomainProviderDefinition;
use PrestaShop\PrestaShop\Core\Translation\Storage\Provider\Definition\ModuleProviderDefinition;
use PrestaShop\PrestaShop\Core\Translation\Storage\Provider\Definition\ProviderDefinitionInterface;
use PrestaShop\PrestaShop\Core\Translation\Storage\Provider\Definition\ThemeProviderDefinition;
use PrestaShopBundle\Entity\Lang;
use PrestaShopBundle\Entity\Translation;
use PrestaShopBundle\Exception\InvalidLanguageException;
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
     * @param string $lang
     * @param string|null $type
     * @param string $theme
     * @param null $search
     *
     * @return array|mixed
     */
    public function getTranslationsCatalogue($lang, $type, $theme, $search = null)
    {
        $translationCatalogueBuilder = $this->container->get('prestashop.translation.builder.translation_catalogue');

        $factory = $this->container->get('ps.translations_factory');

        if ($this->requiresThemeTranslationsFactory($theme, $type)) {
            if ('classic' === $theme) {
                $type = 'front';
            } else {
                $type = $theme;
                $factory = $this->container->get('ps.theme_translations_factory');
            }
        }

        $locale = $this->langToLocale($lang);

        return $factory->createTranslationsArray($type, $locale, $theme, $search);
    }

    /**
     * @param ProviderDefinitionInterface $providerDefinition
     * @param string $locale
     * @param array $search
     *
     * @return array
     *
     * @throws Exception
     */
    public function getTranslationsTree(
        ProviderDefinitionInterface $providerDefinition,
        string $locale,
        array $search
    ): array {
        $translationTreeBuilder = $this->container->get('prestashop.translation.builder.translation_tree');

        return $translationTreeBuilder->getTree($providerDefinition, $locale, $search);
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
     * @param array|null $search
     * @param string|null $module
     *
     * @return array
     */
    public function listDomainTranslation(
        string $locale,
        string $domain,
        ?string $theme = null,
        ?array $search = null,
        ?string $module = null
    ): array {
        if ('Messages' === $domain) {
            $domain = 'messages';
        }

        $translationCatalogueBuilder = $this->container->get('prestashop.translation.builder.translation_catalogue');

        if (!empty($module)) {
            $providerDefinition = new ModuleProviderDefinition($module);
        } elseif (
            !empty($theme)
            // Default theme is not considered like other themes because his translations are within the Core
            && ThemeProviderDefinition::DEFAULT_THEME_NAME !== $theme
        ) {
            $providerDefinition = new ThemeProviderDefinition($theme);
        } else {
            $providerDefinition = new CoreDomainProviderDefinition($domain);
        }

        $domainCatalogue = $translationCatalogueBuilder->getDomainCatalogue(
            $providerDefinition,
            $locale,
            $domain,
            $search
        );

        $router = $this->container->get('router');
        $domainCatalogue['info'] = array_merge($domainCatalogue['info'], [
            'edit_url' => $router->generate('api_translation_value_edit'),
            'reset_url' => $router->generate('api_translation_value_reset'),
        ]);

        return $domainCatalogue;
    }

    /**
     * Check if data contains search word.
     *
     * @param string|array|null $search
     * @param array $data
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
     * @param Lang $lang
     * @param string $domain
     * @param string $key
     * @param string $translationValue
     * @param null $theme
     *
     * @return bool
     */
    public function saveTranslationMessage($lang, $domain, $key, $translationValue, $theme = null)
    {
        $doctrine = $this->container->get('doctrine');
        $entityManager = $doctrine->getManager();
        $logger = $this->container->get('logger');
        $log_context = ['object_type' => 'Translation'];

        if (empty($theme)) {
            $theme = null;
        }

        $translation = null;

        try {
            $translation = $entityManager->getRepository('PrestaShopBundle:Translation')
                ->createQueryBuilder('t')
                ->where('t.lang = :lang')->setParameter('lang', $lang)
                ->andWhere('t.domain = :domain')->setParameter('domain', $domain)
                ->andWhere('t.key LIKE :key')->setParameter('key', $key)
                ->andWhere('t.theme = :theme OR t.theme is NULL')->setParameter('theme', $theme)
                ->getQuery()
                ->getSingleResult();
        } catch (Exception $exception) {
            $logger->error($exception->getMessage(), $log_context);
        }

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
     * @param Lang $lang
     * @param string $domain
     * @param string $key
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
