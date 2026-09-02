<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;
use PrestaShop\PrestaShop\Core\Util\InternationalizedDomainNameConverter;
use PrestaShopBundle\Entity\Employee\Employee;

class EmployeeRepository extends EntityRepository
{
    private InternationalizedDomainNameConverter $idnConverter;

    /**
     * This query is used by the authorization process when the full employee is needed,
     * we optimized it to avoid lazy loading on too many relations. We don't join the
     * profile.authorizationRoles relation ON PURPOSE, it turns out hydrating this many
     * elements in a single query dropped the performance hugely. So it's better to let
     * Doctrine fetch this part lazily itself (it's a few ms versus 500ms with the full
     * join and heady hydration).
     *
     * @param string $userIdentifier
     * @param bool $refresh Force return a fresh entity
     *
     * @return Employee|null
     *
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function loadEmployeeByIdentifier(string $userIdentifier, bool $refresh = false): ?Employee
    {
        $email = $this->idnConverter->emailToUtf8($userIdentifier);
        $qb = $this->createQueryBuilder('e');
        $qb
            ->leftJoin('e.profile', 'p')
            ->leftJoin('e.defaultLanguage', 'l')
            ->leftJoin('e.sessions', 's')
            ->addSelect('e')
            ->addSelect('p')
            ->addSelect('l')
            ->addSelect('s')
            ->where('e.email = :email')
            ->setParameter('email', $email)
        ;

        // This method is involved in security worflow so we always need to be sure the returned data is up to date,
        // since Doctrine caches the entities by default and the DB could be modified by legacy code we force doctrine
        // to return a fresh entity.
        $employee = $qb->getQuery()->getOneOrNullResult();
        if ($employee && $refresh) {
            $this->getEntityManager()->refresh($employee);
        }

        return $employee ?: null;
    }

    public function getIdnConverter(): InternationalizedDomainNameConverter
    {
        return $this->idnConverter;
    }

    public function setIdnConverter(InternationalizedDomainNameConverter $idnConverter): EmployeeRepository
    {
        $this->idnConverter = $idnConverter;

        return $this;
    }
}
