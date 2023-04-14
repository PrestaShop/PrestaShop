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
use PrestaShop\PrestaShop\Core\Translation\Storage\Provider\Definition\ProviderDefinitionInterface;
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
     * @return Lang
     *
     * @throws InvalidLanguageException
     */
    public function findLanguageByLocale($locale)
    {
        $doctrine = $this->container->get('doctrine');

        /** @var Lang|null $lang */
        $lang = $doctrine->getManager()->getRepository(Lang::class)->findOneByLocale($locale);

        if (!$lang instanceof Lang) {
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
        return $this->container->getParameter('kernel.project_dir') . '/Resources';
    }

    /**
     * @param string $lang
     * @param string|null $type
     * @param string|null $theme
     * @param string|null $search
     *
     * @return array|mixed
     */
    public function getTranslationsCatalogue($lang, $type, $theme, $search = null)
    {
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
     * Returns the translation domains tree and counters with total of wording and total of missing translations
     * The tree should look like
     *  tree => [
     *      total_translations => int
     *      total_missing_translations => int
     *      children => [
     *          [
     *              name => string
     *              full_name => string
     *              domain_catalog_link => string
     *              total_translations => int
     *              total_missing_translations => int
     *              children => [
     *                  ...
     *              ]
     *          ]
     *   ]
     *
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
     * @param ProviderDefinitionInterface $providerDefinition
     * @param string $locale
     * @param string $domain
     * @param array|null $search
     *
     * @return array
     *
     * @throws Exception
     * @todo: we need module information here
     * @todo: we need to improve the Vuejs application to send the information
     */
    public function listDomainTranslation(
        ProviderDefinitionInterface $providerDefinition,
        string $locale,
        string $domain,
        ?array $search = null
    ): array {
        $domainCatalogue = $this->container->get('prestashop.translation.builder.translation_catalogue')->getDomainCatalogue(
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
     * Save a translation in database.
     *
     * @param Lang $lang
     * @param string $domain
     * @param string $key
     * @param string $translationValue
     * @param string|null $theme
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
            $queryBuilder = $entityManager->getRepository(Translation::class)
                ->createQueryBuilder('t')
                ->where('t.lang = :lang')->setParameter('lang', $lang)
                ->andWhere('t.domain = :domain')->setParameter('domain', $domain)
                ->andWhere('t.key LIKE :key')->setParameter('key', $key)
            ;
            if ($theme !== null) {
                $queryBuilder->andWhere('t.theme = :theme')->setParameter('theme', $theme);
            } else {
                $queryBuilder->andWhere('t.theme IS NULL');
            }
            $translation = $queryBuilder->getQuery()->getSingleResult();
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
     * @param string|null $theme
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

        $translation = $entityManager->getRepository(Translation::class)->findOneBy($searchTranslation);

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
