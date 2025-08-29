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

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Translation\Storage\Loader;

use Doctrine\ORM\QueryBuilder;
use PrestaShop\PrestaShop\Core\Language\LanguageInterface;
use PrestaShop\PrestaShop\Core\Language\LanguageRepositoryInterface;
use PrestaShop\PrestaShop\Core\Translation\TranslationInterface;
use PrestaShop\PrestaShop\Core\Translation\TranslationRepositoryInterface;
use Symfony\Component\Translation\MessageCatalogue;

/**
 * The user translated catalogue is stored in database.
 * This class is a helper to build the query for retrieving the translations.
 * They depend on parameters like locale, theme or domain.
 */
class DatabaseTranslationLoader
{
    /**
     * @var LanguageRepositoryInterface
     */
    private $languageRepository;

    /**
     * @var TranslationRepositoryInterface
     */
    private $translationRepository;

    public function __construct(
        LanguageRepositoryInterface $languageRepository,
        TranslationRepositoryInterface $translationRepository
    ) {
        $this->languageRepository = $languageRepository;
        $this->translationRepository = $translationRepository;
    }

    /**
     * Loads all user translations according to search parameters
     *
     * @param string $locale Translation language
     * @param string $domain Regex for domain pattern search
     * @param string|null $theme A theme name
     *
     * @return MessageCatalogue A MessageCatalogue instance
     */
    public function load(string $locale, string $domain = 'messages', ?string $theme = null): MessageCatalogue
    {
        static $languages = [];
        $catalogue = new MessageCatalogue($locale);

        // do not try and load translations for a locale that cannot be saved to DB anyway
        if ($locale === 'default') {
            return $catalogue;
        }

        if (!array_key_exists($locale, $languages)) {
            $languages[$locale] = $this->languageRepository->findOneBy(['locale' => $locale]);
        }

        $queryBuilder = $this->translationRepository->createQueryBuilder('t');

        $this->addLangConstraint($queryBuilder, $languages[$locale]);

        $this->addThemeConstraint($queryBuilder, $theme);

        $this->addDomainConstraint($queryBuilder, $domain);

        $translations = $queryBuilder
            ->getQuery()
            ->getResult();

        /** @var TranslationInterface $translation */
        foreach ($translations as $translation) {
            $catalogue->set($translation->getKey(), $translation->getTranslation(), $translation->getDomain());
        }

        return $catalogue;
    }

    /**
     * @param QueryBuilder $queryBuilder
     * @param LanguageInterface $currentLang
     */
    private function addLangConstraint(QueryBuilder $queryBuilder, LanguageInterface $currentLang): void
    {
        $queryBuilder->andWhere('t.lang =:lang')
            ->setParameter('lang', $currentLang);
    }

    /**
     * @param QueryBuilder $queryBuilder
     * @param string|null $theme
     */
    private function addThemeConstraint(QueryBuilder $queryBuilder, ?string $theme = null): void
    {
        if (null === $theme) {
            $queryBuilder->andWhere('t.theme IS NULL');
        } else {
            $queryBuilder
                ->andWhere('t.theme = :theme')
                ->setParameter('theme', $theme);
        }
    }

    /**
     * @param QueryBuilder $queryBuilder
     * @param string $domain
     */
    private function addDomainConstraint(QueryBuilder $queryBuilder, string $domain): void
    {
        if ($domain !== '*') {
            $queryBuilder->andWhere('REGEXP(t.domain, :domain) = true')
                ->setParameter('domain', $domain);
        }
    }
}
