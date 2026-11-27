<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\PrestaShopBundle\Entity\Repository;

use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\Attributes\DataProvider;
use PrestaShop\PrestaShop\Core\Domain\Employee\Exception\EmployeeException;
use PrestaShop\PrestaShop\Core\Domain\Employee\Exception\EmployeeNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Employee\Exception\EmployeeTwoFactorException;
use PrestaShopBundle\Entity\Employee\Employee;
use PrestaShopBundle\Entity\Repository\EmployeeRepository;
use PrestaShopBundle\SchebTwoFactor\EmployeeBackupCodeManager;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\PasswordHasher\PasswordHasherInterface;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;

/**
 * Exercises conditional writes on the test database, including stale request snapshots.
 */
final class EmployeeTwoFactorPersistenceTest extends KernelTestCase
{
    private EntityManagerInterface $entityManager;

    private Connection $connection;

    private EmployeeRepository $repository;

    private string $employeeTable;

    private int $employeeId;

    protected function setUp(): void
    {
        parent::setUp();
        self::bootKernel();

        $this->repository = self::getContainer()->get(EmployeeRepository::class);
        $this->entityManager = self::getContainer()->get('doctrine.orm.default_entity_manager');
        $this->connection = $this->entityManager->getConnection();
        $this->employeeTable = $this->entityManager->getClassMetadata(Employee::class)->getTableName();
        self::assertStringStartsWith('test_', (string) $this->connection->getDatabase());

        $fixture = [
            'id_profile' => 1,
            'id_lang' => 1,
            'firstname' => 'Ada',
            'lastname' => 'TwoFactorTest',
            'email' => 'two-factor-lifecycle-' . bin2hex(random_bytes(8)) . '@example.com',
            'passwd' => str_repeat('x', 60),
            'active' => 1,
            'two_factor_enabled' => 1,
            'two_factor_totp_enabled' => 0,
            'two_factor_email_enabled' => 1,
            'two_factor_email_auth_code' => '123456',
        ];
        $qb = $this->connection->createQueryBuilder()->insert($this->employeeTable);
        foreach ($fixture as $column => $value) {
            $qb->setValue($column, ':' . $column)->setParameter($column, $value);
        }
        $qb->executeStatement();
        $this->employeeId = (int) $this->connection->lastInsertId();
    }

    protected function tearDown(): void
    {
        try {
            if (isset($this->employeeId)) {
                $this->removeEmployeeFixture();
            }
            if (isset($this->entityManager)) {
                $this->entityManager->clear();
            }
        } finally {
            parent::tearDown();
        }
    }

    public function testUnknownEmployeeUsesTheDomainExceptionAndItsIdentifier(): void
    {
        $this->removeEmployeeFixture();
        try {
            $this->repository->getForTwoFactor($this->employeeId);
            self::fail('An unknown employee must be rejected.');
        } catch (EmployeeNotFoundException $exception) {
            self::assertSame($this->employeeId, $exception->getEmployeeId()->getValue());
        }
    }

    public function testBackupCodeWritePreservesFlagsAndOtherEmployeeFields(): void
    {
        $this->repository->saveBackupCodes($this->employeeId, ['hash-one', 'hash-two'], null);
        $row = $this->employeeRow();
        self::assertSame(['hash-one', 'hash-two'], json_decode($row['two_factor_backup_codes'], true));
        self::assertSame('Ada', $row['firstname']);
        self::assertSame(1, (int) $row['two_factor_enabled']);
        self::assertSame(0, (int) $row['two_factor_totp_enabled']);
        self::assertSame(1, (int) $row['two_factor_email_enabled']);
        self::assertSame('123456', $row['two_factor_email_auth_code']);
        $this->updateEmployee(['two_factor_enabled' => 0]);
        $this->repository->clearBackupCodes($this->employeeId);
        self::assertNull($this->employeeValue('two_factor_backup_codes'));
    }

    public function testLifecycleSnapshotReadsTheCurrentFlagsAndCodesTogether(): void
    {
        $this->updateEmployee(['two_factor_backup_codes' => '[ "existing" ]']);
        self::assertSame([
            'two_factor_enabled' => true,
            'two_factor_totp_enabled' => false,
            'two_factor_email_enabled' => true,
            'two_factor_backup_codes' => '[ "existing" ]',
        ], $this->repository->getTwoFactorStateSnapshot($this->employeeId));
    }

    public function testRegenerationReplacesHashesAndClearsEmailCode(): void
    {
        $this->repository->saveBackupCodes($this->employeeId, ['new-hash'], null, true, 'email', '123456');
        $row = $this->employeeRow();
        self::assertSame(['new-hash'], json_decode($row['two_factor_backup_codes'], true));
        self::assertNull($row['two_factor_email_auth_code']);
        self::assertSame('Ada', $row['firstname']);
    }

    public function testInitializingTotpNeverReplacesAnExistingSecret(): void
    {
        $this->repository->initializeTotpSecret($this->employeeId, 'encrypted-first');
        $this->repository->initializeTotpSecret($this->employeeId, 'encrypted-second');
        self::assertSame('encrypted-first', $this->employeeValue('two_factor_totp_secret'));
        self::assertSame(0, (int) $this->employeeValue('two_factor_totp_enabled'));
    }

    public function testInitializingTotpCannotReplaceTheSecretOfAnActiveProvider(): void
    {
        $this->updateEmployee(['two_factor_totp_enabled' => 1]);
        $this->repository->initializeTotpSecret($this->employeeId, 'new-secret');
        self::assertNull($this->employeeValue('two_factor_totp_secret'));
    }

    public function testMissingEmployeeCannotReceiveAPersistedCodeSet(): void
    {
        $this->removeEmployeeFixture();
        $this->expectException(EmployeeException::class);
        $this->repository->saveBackupCodes($this->employeeId, ['new-hash'], null);
    }

    public function testEmailPreparationDoesNotChangeBackupCodes(): void
    {
        $this->repository->saveBackupCodes($this->employeeId, ['existing-hash'], null);
        $this->repository->saveEmailAuthCode($this->employeeId, '654321');
        $row = $this->employeeRow();
        self::assertSame(['existing-hash'], json_decode($row['two_factor_backup_codes'], true));
        self::assertSame('654321', $row['two_factor_email_auth_code']);
    }

    #[DataProvider('emptySets')]
    public function testOnlyOneInitialGenerationWins(?string $initial): void
    {
        $this->updateEmployee(['two_factor_backup_codes' => $initial]);
        $snapshot = $this->repository->getBackupCodesSnapshot($this->employeeId);
        $this->repository->saveBackupCodes($this->employeeId, ['winner'], $snapshot);
        try {
            $this->repository->saveBackupCodes($this->employeeId, ['loser'], $snapshot);
            self::fail('The stale generation must fail.');
        } catch (EmployeeTwoFactorException $exception) {
            self::assertSame(EmployeeTwoFactorException::CONCURRENT_UPDATE, $exception->getCode());
            self::assertSame(['winner'], $this->storedCodes());
        }
    }

    public static function emptySets(): iterable
    {
        yield 'never generated' => [null];
        yield 'exhausted' => ['[]'];
    }

    public function testOnlyOneRegenerationCanConsumeTheEmailCode(): void
    {
        $this->repository->saveBackupCodes($this->employeeId, ['original'], null);
        $snapshot = $this->repository->getBackupCodesSnapshot($this->employeeId);
        $this->repository->saveBackupCodes($this->employeeId, ['winner'], $snapshot, true, 'email', '123456');
        try {
            $this->repository->saveBackupCodes($this->employeeId, ['loser'], $snapshot, true, 'email', '123456');
            self::fail('The stale regeneration must fail.');
        } catch (EmployeeTwoFactorException $exception) {
            self::assertSame(EmployeeTwoFactorException::CONCURRENT_UPDATE, $exception->getCode());
            self::assertSame(['winner'], $this->storedCodes());
            self::assertNull($this->employeeValue('two_factor_email_auth_code'));
        }
    }

    public function testAnAlreadyConsumedEmailCodeCannotAuthorizeAnotherWrite(): void
    {
        $this->repository->saveBackupCodes($this->employeeId, ['winner'], null, true, 'email', '123456');
        $snapshot = $this->repository->getBackupCodesSnapshot($this->employeeId);
        $this->expectException(EmployeeTwoFactorException::class);
        $this->repository->saveBackupCodes($this->employeeId, ['loser'], $snapshot, true, 'email', '123456');
    }

    public function testEmailResendInvalidatesAPreviouslyValidatedProof(): void
    {
        $this->repository->saveEmailAuthCode($this->employeeId, '654321');
        try {
            $this->repository->saveBackupCodes($this->employeeId, ['loser'], null, true, 'email', '123456');
            self::fail('A superseded email code must not authorize regeneration.');
        } catch (EmployeeTwoFactorException) {
            self::assertNull($this->storedCodes());
            self::assertSame('654321', $this->employeeValue('two_factor_email_auth_code'));
        }
    }

    #[DataProvider('disabledStates')]
    public function testConcurrentDisablingPreventsWritingCodes(array $changes): void
    {
        $this->updateEmployee($changes);
        $this->expectException(EmployeeTwoFactorException::class);
        $this->repository->saveBackupCodes($this->employeeId, ['new-codes'], null);
    }

    public static function disabledStates(): iterable
    {
        yield 'master disabled' => [['two_factor_enabled' => 0]];
        yield 'last provider disabled' => [['two_factor_email_enabled' => 0]];
    }

    public function testAChangedTotpSecretInvalidatesAPreviouslyValidatedProof(): void
    {
        $this->updateEmployee(['two_factor_totp_enabled' => 1, 'two_factor_totp_secret' => 'new-secret']);
        $this->expectException(EmployeeTwoFactorException::class);
        $this->repository->saveBackupCodes($this->employeeId, ['new-codes'], null, true, 'totp', 'old-secret');
    }

    public function testOnlyOneTotpRegenerationWinsForTheSameSnapshot(): void
    {
        $this->updateEmployee(['two_factor_totp_enabled' => 1, 'two_factor_totp_secret' => 'encrypted-secret']);
        $snapshot = $this->repository->getBackupCodesSnapshot($this->employeeId);
        $this->repository->saveBackupCodes($this->employeeId, ['winner'], $snapshot, true, 'totp', 'encrypted-secret');
        try {
            $this->repository->saveBackupCodes($this->employeeId, ['loser'], $snapshot, true, 'totp', 'encrypted-secret');
            self::fail('A concurrent TOTP regeneration must not replace the winning set.');
        } catch (EmployeeTwoFactorException) {
            self::assertSame(['winner'], $this->storedCodes());
        }
    }

    public function testDisablingTheSelectedProviderRejectsItsProofEvenIfAnotherProviderIsActive(): void
    {
        $this->updateEmployee(['two_factor_email_enabled' => 0, 'two_factor_totp_enabled' => 1]);
        $this->expectException(EmployeeTwoFactorException::class);
        $this->repository->saveBackupCodes($this->employeeId, ['new-codes'], null, true, 'email', '123456');
    }

    public function testSnapshotComparisonIsByteExactEvenOnCaseInsensitiveColumns(): void
    {
        $this->updateEmployee(['two_factor_backup_codes' => '["Hash"]']);
        $this->expectException(EmployeeTwoFactorException::class);
        $this->repository->saveBackupCodes($this->employeeId, ['new-codes'], '["hash"]');
    }

    public function testReactivationCanReplaceStaleCodesWithoutRequiringAnEmptySet(): void
    {
        $this->updateEmployee(['two_factor_backup_codes' => '[ "stale" ]']);
        $snapshot = $this->repository->getBackupCodesSnapshot($this->employeeId);
        $this->repository->saveBackupCodes($this->employeeId, ['new-codes'], $snapshot);
        self::assertSame(['new-codes'], $this->storedCodes());
    }

    public function testLateClearCannotEraseCodesOfAReactivatedAccount(): void
    {
        $this->repository->saveBackupCodes($this->employeeId, ['new-codes'], null);
        $this->repository->clearBackupCodes($this->employeeId);
        self::assertSame(['new-codes'], $this->storedCodes());
    }

    public function testConsumingAnOldCodeCannotResurrectARegeneratedSet(): void
    {
        $user = $this->backupCodeUser(['hash:a', 'hash:b']);
        $this->repository->saveBackupCodes($this->employeeId, ['hash:new'], null);
        $manager = $this->backupCodeManager();
        self::assertTrue($manager->isBackupCode($user, 'a'));
        try {
            $manager->invalidateBackupCode($user, 'a');
            self::fail('A code from a regenerated set must be rejected.');
        } catch (BadCredentialsException) {
            self::assertSame(['hash:new'], $this->storedCodes());
        }
    }

    public function testConsumingTheSameCodeTwiceFailsClosed(): void
    {
        $user = $this->backupCodeUser(['hash:a', 'hash:b']);
        $this->repository->saveBackupCodes($this->employeeId, ['hash:a', 'hash:b'], null);
        $manager = $this->backupCodeManager();
        $manager->invalidateBackupCode($user, 'a');
        try {
            $manager->invalidateBackupCode($user, 'a');
            self::fail('A consumed backup code cannot authenticate again.');
        } catch (BadCredentialsException) {
            self::assertSame(['hash:b'], $this->storedCodes());
        }
    }

    public function testDisablingTwoFactorBeforeConsumptionRejectsAuthentication(): void
    {
        $user = $this->backupCodeUser(['hash:a']);
        $this->repository->saveBackupCodes($this->employeeId, ['hash:a'], null);
        $this->updateEmployee(['two_factor_enabled' => 0]);
        try {
            $this->backupCodeManager()->invalidateBackupCode($user, 'a');
            self::fail('A disabled account must not authenticate with a backup code.');
        } catch (BadCredentialsException) {
            self::assertSame(['hash:a'], $this->storedCodes());
        }
    }

    public function testDifferentCodesCanBeConsumedWithoutRestoringAnEarlierCode(): void
    {
        $user = $this->backupCodeUser(['hash:a', 'hash:b']);
        $this->repository->saveBackupCodes($this->employeeId, ['hash:a', 'hash:b'], null);
        $manager = $this->backupCodeManager();
        $manager->invalidateBackupCode($user, 'a');
        $manager->invalidateBackupCode($user, 'b');
        self::assertSame([], $this->storedCodes());
    }

    public function testRegenerationBetweenConsumptionReadAndWriteRejectsAuthentication(): void
    {
        $user = $this->backupCodeUser(['hash:a']);
        $this->repository->saveBackupCodes($this->employeeId, ['hash:a'], null);
        $hasher = $this->createMock(PasswordHasherInterface::class);
        $hasher->method('verify')->willReturnCallback(function (): bool {
            $snapshot = $this->repository->getBackupCodesSnapshot($this->employeeId);
            $this->repository->saveBackupCodes($this->employeeId, ['hash:new'], $snapshot);

            return true;
        });
        $manager = new EmployeeBackupCodeManager($hasher, $this->repository);
        try {
            $manager->invalidateBackupCode($user, 'a');
            self::fail('Authentication must not succeed when consumption loses a concurrent update.');
        } catch (BadCredentialsException) {
            self::assertSame(['hash:new'], $this->storedCodes());
        }
    }

    /**
     * @param array<string, int|string|null> $changes
     */
    private function updateEmployee(array $changes): void
    {
        $qb = $this->connection->createQueryBuilder()
            ->update($this->employeeTable)
            ->where('id_employee = :employeeId')
            ->setParameter('employeeId', $this->employeeId);
        foreach ($changes as $column => $value) {
            $qb->set($column, ':' . $column)->setParameter($column, $value);
        }
        $qb->executeStatement();
    }

    /**
     * @return array<string, mixed>
     */
    private function employeeRow(): array
    {
        $row = $this->connection->createQueryBuilder()
            ->select('*')
            ->from($this->employeeTable)
            ->where('id_employee = :employeeId')
            ->setParameter('employeeId', $this->employeeId)
            ->setMaxResults(1)
            ->executeQuery()->fetchAssociative();
        self::assertIsArray($row);

        return $row;
    }

    private function employeeValue(string $column): mixed
    {
        return $this->employeeRow()[$column];
    }

    private function removeEmployeeFixture(): void
    {
        $this->connection->createQueryBuilder()
            ->delete($this->employeeTable)
            ->where('id_employee = :employeeId')
            ->setParameter('employeeId', $this->employeeId)
            ->executeStatement();
    }

    private function backupCodeUser(array $codes): Employee
    {
        $user = $this->createMock(Employee::class);
        $user->method('getId')->willReturn($this->employeeId);
        $user->method('getTwoFactorBackupCodes')->willReturn($codes);
        $user->expects(self::never())->method('setTwoFactorBackupCodes');

        return $user;
    }

    private function backupCodeManager(): EmployeeBackupCodeManager
    {
        $hasher = $this->createMock(PasswordHasherInterface::class);
        $hasher->method('verify')->willReturnCallback(static fn (string $hash, string $plain): bool => $hash === 'hash:' . $plain);

        return new EmployeeBackupCodeManager($hasher, $this->repository);
    }

    private function storedCodes(): ?array
    {
        $stored = $this->employeeValue('two_factor_backup_codes');

        return null === $stored ? null : json_decode($stored, true, 512, JSON_THROW_ON_ERROR);
    }
}
