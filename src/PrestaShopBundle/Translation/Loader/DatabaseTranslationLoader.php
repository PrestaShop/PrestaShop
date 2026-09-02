<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Translation\Loader;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use PrestaShopBundle\Entity\Lang;
use PrestaShopBundle\Entity\Translation;
use Symfony\Component\Translation\Loader\LoaderInterface;
use Symfony\Component\Translation\MessageCatalogue;

/**
 * The user translated catalogue is stored in database.
 * This class is a helper to build the query for retrieving the translations.
 * They depend on parameters like locale, theme or domain.
 */
class DatabaseTranslationLoader implements LoaderInterface
{
    /** @var EntityManagerInterface */
    protected $entityManager;

    /**
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param mixed $resource
     * @param string $locale
     * @param string $domain
     * @param string|null $theme
     *
     * @return MessageCatalogue
     *
     * @todo: this method doesn't match the interface
     */
    public function load($resource, $locale, $domain = 'messages', $theme = null): MessageCatalogue
    {
        static $langs = [];
        $catalogue = new MessageCatalogue($locale);

        // do not try and load translations for a locale that cannot be saved to DB anyway
        if ($locale === 'default') {
            return $catalogue;
        }

        if (!array_key_exists($locale, $langs)) {
            $langs[$locale] = $this->entityManager->getRepository(Lang::class)->findOneBy(['locale' => $locale]);
        }

        if ($langs[$locale] === null) {
            return $catalogue;
        }

        /** @var EntityRepository $translationRepository */
        $translationRepository = $this->entityManager->getRepository(Translation::class);

        $queryBuilder = $translationRepository->createQueryBuilder('t');

        $this->addLangConstraint($queryBuilder, $langs[$locale]);

        $this->addThemeConstraint($queryBuilder, $theme);

        $this->addDomainConstraint($queryBuilder, $domain);

        $translations = $queryBuilder
            ->getQuery()
            ->getResult();

        /** @var Translation $translation */
        foreach ($translations as $translation) {
            $catalogue->set($translation->getKey(), $translation->getTranslation(), $translation->getDomain());
        }

        return $catalogue;
    }

    /**
     * @param QueryBuilder $queryBuilder
     * @param Lang $currentLang
     */
    private function addLangConstraint(QueryBuilder $queryBuilder, Lang $currentLang)
    {
        $queryBuilder->andWhere('t.lang =:lang')
            ->setParameter('lang', $currentLang);
    }

    /**
     * @param QueryBuilder $queryBuilder
     * @param string|null $theme
     */
    private function addThemeConstraint(QueryBuilder $queryBuilder, $theme)
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
    private function addDomainConstraint(QueryBuilder $queryBuilder, $domain)
    {
        if ($domain !== '*') {
            $queryBuilder->andWhere('REGEXP(t.domain, :domain) = true')
                ->setParameter('domain', $domain);
        }
    }
}
