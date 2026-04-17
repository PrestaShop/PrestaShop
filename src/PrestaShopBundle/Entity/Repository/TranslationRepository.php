<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;
use PrestaShop\PrestaShop\Core\Translation\TranslationRepositoryInterface;

class TranslationRepository extends EntityRepository implements TranslationRepositoryInterface
{
    /**
     * @param string $language
     * @param string $theme
     *
     * @return array
     */
    public function findByLanguageAndTheme($language, $theme = null)
    {
        $queryBuilder = $this->createQueryBuilder('t');
        $queryBuilder->where('lang = :language');
        $queryBuilder->setParameter('language', $language);

        if (null !== $theme) {
            $queryBuilder->andWhere('theme = :theme');
            $queryBuilder->setParameter('theme', $theme);
        } else {
            $queryBuilder->andWhere('theme IS NULL');
        }

        $query = $queryBuilder->getQuery();

        return $query->getResult();
    }
}
