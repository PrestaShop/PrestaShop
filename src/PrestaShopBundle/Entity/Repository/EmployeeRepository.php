<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\Entity\Repository;

use Doctrine\DBAL\Query\QueryBuilder;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\EntityRepository;
use PrestaShop\PrestaShop\Core\Domain\Employee\Exception\EmployeeNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Employee\Exception\EmployeeTwoFactorException;
use PrestaShop\PrestaShop\Core\Domain\Employee\ValueObject\EmployeeId;
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

    /**
     * Reset all 2FA data for all employees.
     * Used when 2FA is globally disabled.
     *
     * @return int Number of employees affected
     */
    public function resetAllTwoFactorData(): int
    {
        $qb = $this->getEntityManager()->createQueryBuilder();

        return $qb->update($this->getEntityName(), 'e')
            ->set('e.twoFactorEnabled', ':false')
            ->set('e.twoFactorTotpEnabled', ':false')
            ->set('e.twoFactorEmailEnabled', ':false')
            ->set('e.twoFactorTotpSecretEncrypted', ':null')
            ->set('e.twoFactorEmailAuthCode', ':null')
            ->set('e.twoFactorBackupCodes', ':null')
            ->setParameter('false', false)
            ->setParameter('null', null)
            ->getQuery()
            ->execute();
    }

    public function clearBackupCodes(int $employeeId): void
    {
        $this->getEntityManager()->createQueryBuilder()
            ->update($this->getEntityName(), 'e')
            ->set('e.twoFactorBackupCodes', ':null')
            ->where('e.id = :employeeId')
            ->andWhere('e.twoFactorBackupCodes IS NOT NULL')
            ->andWhere('(e.twoFactorEnabled = false OR (e.twoFactorTotpEnabled = false AND e.twoFactorEmailEnabled = false))')
            ->setParameter('null', null)
            ->setParameter('employeeId', $employeeId)
            ->getQuery()
            ->execute();
    }

    public function getForTwoFactor(int $employeeId): Employee
    {
        $employee = $this->find($employeeId);
        if (!$employee instanceof Employee) {
            throw new EmployeeNotFoundException(new EmployeeId($employeeId), sprintf('Employee %d was not found.', $employeeId));
        }

        // Legacy updates and targeted DQL writes bypass Doctrine's identity map.
        $this->getEntityManager()->refresh($employee);

        return $employee;
    }

    /**
     * The raw stored value is the comparison token, so JSON whitespace and escaping cannot
     * turn an unchanged set into a conflict. It contains hashes, never plaintext codes.
     */
    public function getBackupCodesSnapshot(int $employeeId): ?string
    {
        return $this->getTwoFactorStateSnapshot($employeeId)['two_factor_backup_codes'];
    }

    /**
     * Read flags and the comparison token together, independently of ObjectModel/ORM caches.
     *
     * @return array{two_factor_enabled: bool, two_factor_totp_enabled: bool, two_factor_email_enabled: bool, two_factor_backup_codes: ?string}
     */
    public function getTwoFactorStateSnapshot(int $employeeId): array
    {
        $state = $this->getEntityManager()->getConnection()->createQueryBuilder()
            ->select('two_factor_enabled', 'two_factor_totp_enabled', 'two_factor_email_enabled', 'two_factor_backup_codes')
            ->from($this->getClassMetadata()->getTableName())
            ->where('id_employee = :employeeId')
            ->setParameter('employeeId', $employeeId)
            ->setMaxResults(1)
            ->executeQuery()->fetchAssociative();
        if (false === $state) {
            throw new EmployeeNotFoundException(new EmployeeId($employeeId));
        }

        return [
            'two_factor_enabled' => (bool) $state['two_factor_enabled'],
            'two_factor_totp_enabled' => (bool) $state['two_factor_totp_enabled'],
            'two_factor_email_enabled' => (bool) $state['two_factor_email_enabled'],
            'two_factor_backup_codes' => $state['two_factor_backup_codes'],
        ];
    }

    /**
     * @param string[] $backupCodes
     */
    public function saveBackupCodes(
        int $employeeId,
        array $backupCodes,
        ?string $expectedBackupCodes,
        bool $clearEmailCode = false,
        string $provider = '',
        ?string $expectedVerificationValue = null,
    ): void {
        $qb = $this->getEntityManager()->getConnection()->createQueryBuilder()
            ->update($this->getClassMetadata()->getTableName())
            ->set('two_factor_backup_codes', ':codes')
            ->where('id_employee = :employeeId')
            ->andWhere('two_factor_enabled = 1')
            ->andWhere('(two_factor_totp_enabled = 1 OR two_factor_email_enabled = 1)')
            ->setParameter('codes', $backupCodes, Types::JSON)
            ->setParameter('employeeId', $employeeId);
        $this->addExpectedValue($qb, 'two_factor_backup_codes', 'expectedCodes', $expectedBackupCodes);

        if ('email' === $provider) {
            $qb->andWhere('two_factor_email_enabled = 1');
            $this->addExpectedValue($qb, 'two_factor_email_auth_code', 'expectedVerification', $expectedVerificationValue);
        } elseif ('totp' === $provider) {
            $qb->andWhere('two_factor_totp_enabled = 1');
            $this->addExpectedValue($qb, 'two_factor_totp_secret', 'expectedVerification', $expectedVerificationValue);
        }
        if ($clearEmailCode) {
            $qb->set('two_factor_email_auth_code', 'NULL');
        }
        if (1 !== $qb->executeStatement()) {
            throw new EmployeeTwoFactorException('Two-factor settings changed during this operation.', EmployeeTwoFactorException::CONCURRENT_UPDATE);
        }
    }

    private function addExpectedValue(QueryBuilder $qb, string $column, string $parameter, ?string $expected): void
    {
        if (null === $expected) {
            $qb->andWhere($column . ' IS NULL');

            return;
        }

        // HEX is available on MySQL/MariaDB and SQLite and avoids case-insensitive text collations.
        $qb->andWhere('HEX(' . $column . ') = :' . $parameter)
            ->setParameter($parameter, strtoupper(bin2hex($expected)));
    }

    public function initializeTotpSecret(int $employeeId, string $encryptedSecret): void
    {
        $this->getEntityManager()->createQueryBuilder()
            ->update($this->getEntityName(), 'e')
            ->set('e.twoFactorTotpSecretEncrypted', ':secret')
            ->where('e.id = :employeeId')
            ->andWhere('(e.twoFactorTotpSecretEncrypted IS NULL OR e.twoFactorTotpSecretEncrypted = :empty)')
            ->andWhere('e.twoFactorTotpEnabled = false')
            ->setParameter('empty', '')
            ->setParameter('secret', $encryptedSecret)
            ->setParameter('employeeId', $employeeId)
            ->getQuery()->execute();
    }

    public function saveEmailAuthCode(int $employeeId, string $code): void
    {
        $this->getEntityManager()->createQueryBuilder()
            ->update($this->getEntityName(), 'e')
            ->set('e.twoFactorEmailAuthCode', ':code')
            ->where('e.id = :employeeId')
            ->setParameter('code', $code)
            ->setParameter('employeeId', $employeeId)
            ->getQuery()->execute();
    }

    /**
     * Check if any employee has 2FA data.
     */
    public function hasAnyEmployeeWithTwoFactorData(): bool
    {
        $qb = $this->createQueryBuilder('e')
            ->select('COUNT(e.id)')
            ->where('e.twoFactorEnabled = :true')
            ->orWhere('e.twoFactorBackupCodes IS NOT NULL')
            ->setParameter('true', true);

        return (int) $qb->getQuery()->getSingleScalarResult() > 0;
    }
}
