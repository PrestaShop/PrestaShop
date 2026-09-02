<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;
use InvalidArgumentException;
use PrestaShopBundle\Entity\Tab;

class TabRepository extends EntityRepository
{
    /** @var array<string, int> */
    private $cachedTabIds = [];

    /**
     * @param string $moduleName
     *
     * @return Tab[]
     */
    public function findByModule($moduleName)
    {
        return $this->findBy(['module' => $moduleName]);
    }

    /**
     * @param int $idParent
     *
     * @return array
     */
    public function findByParentId($idParent)
    {
        return $this->findBy(['idParent' => $idParent]);
    }

    /**
     * @param string $className
     *
     * @return Tab|null
     */
    public function findOneByClassName($className)
    {
        return $this->findOneBy(['className' => $className]);
    }

    public function findOneByRouteName(string $routeName): ?Tab
    {
        return $this->findOneBy(['routeName' => $routeName]);
    }

    /**
     * @param string $className
     *
     * @return int|null
     */
    public function findOneIdByClassName($className)
    {
        $tab = $this->findOneByClassName($className);
        if ($tab) {
            return $tab->getId();
        }

        return null;
    }

    /**
     * Changes tab status.
     *
     * @param string $className tab's class name
     * @param bool $status wanted status for the tab
     *
     * @throws InvalidArgumentException
     */
    public function changeStatusByClassName($className, $status)
    {
        if (!is_bool($status)) {
            throw new InvalidArgumentException(sprintf('Invalid type: bool expected, got %s', gettype($status)));
        }

        /** @var Tab $tab */
        $tab = $this->findOneByClassName($className);

        if (null !== $tab) {
            $tab->setActive($status);
            $this->getEntityManager()->persist($tab);
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @param string $moduleName
     * @param bool $enabled
     */
    public function changeEnabledByModuleName($moduleName, $enabled)
    {
        $tabs = $this->findByModule($moduleName);
        /** @var Tab $tab */
        foreach ($tabs as $tab) {
            $tab->setEnabled($enabled);
        }
        $this->getEntityManager()->flush();
    }

    /**
     * @param string $className
     *
     * @return int
     *
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getIdByClassName(string $className): int
    {
        if (!isset($this->cachedTabIds[$className])) {
            $result = $this->createQueryBuilder('t')
                ->select('t.id, t.className')
                // Use binary to force a case-sensitive comparison (HOME and Home are different)
                ->where('t.className = BINARY(:className)')
                ->andWhere('t.id != 0')
                ->setParameter('className', $className)
                ->getQuery()
                ->getSingleResult(Query::HYDRATE_ARRAY)
            ;

            $this->cachedTabIds[$result['className']] = (int) $result['id'];
        }

        return $this->cachedTabIds[$className];
    }

    /**
     * @param int $tabId
     *
     * @return Tab[] breadcrumb to access the Tab, ordered from closest to oldest ancestor
     *
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getAncestors(int $tabId): array
    {
        return $this->getTabParents($tabId);
    }

    /**
     * Recursive method is kept as private.
     *
     * @param int $tabId
     *
     * @return Tab[]
     *
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    private function getTabParents(int $tabId): array
    {
        /** @var Tab $tab */
        $tab = $this->findOneBy(['id' => $tabId]);

        if (empty($tab->getIdParent())) {
            return [];
        }

        $parent = $this->findOneBy(['id' => $tab->getIdParent()]);
        if (null === $parent) {
            return [];
        }

        return array_merge([$parent], $this->getTabParents($parent->getId()));
    }
}
