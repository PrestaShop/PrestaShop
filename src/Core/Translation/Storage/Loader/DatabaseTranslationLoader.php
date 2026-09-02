<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
