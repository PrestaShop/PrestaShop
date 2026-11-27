<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\PrestaShopBundle\Entity\Repository;

use Db;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Employee as LegacyEmployee;
use PHPUnit\Framework\Attributes\DataProvider;
use PrestaShop\PrestaShop\Adapter\Profile\Employee\CommandHandler\ResetEmployeeTwoFactorHandler;
use PrestaShop\PrestaShop\Core\Domain\Employee\Command\ResetEmployeeTwoFactorCommand;
use PrestaShopBundle\Entity\Employee\Employee;
use PrestaShopBundle\Entity\Repository\EmployeeRepository;
use PrestaShopBundle\SchebTwoFactor\TotpSecretEncryptor;
use PrestaShopBundle\SchebTwoFactor\TrustedDeviceTokenFactory;
use PrestaShopException;
use RuntimeException;
use Scheb\TwoFactorBundle\Security\Authentication\Token\TwoFactorTokenFactory;
use Scheb\TwoFactorBundle\Security\Authentication\Token\TwoFactorTokenInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

/**
 * Exercises the native MySQL/MariaDB comparisons and the real ObjectModel/ORM writers.
 */
final class EmployeeTrustedDevicePersistenceTest extends KernelTestCase
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
        $this->entityManager = self::getContainer()->get('doctrine.orm.default_entity_manager');
        $this->connection = $this->entityManager->getConnection();
        $this->repository = self::getContainer()->get(EmployeeRepository::class);
        $this->employeeTable = $this->entityManager->getClassMetadata(Employee::class)->getTableName();
        self::assertStringStartsWith('test_', (string) $this->connection->getDatabase());

        $fixture = [
            'id_profile' => 1,
            'id_lang' => 1,
            'firstname' => 'Ada',
            'lastname' => 'TrustedDeviceTest',
            'email' => 'trusted-device-' . bin2hex(random_bytes(8)) . '@example.com',
            'passwd' => str_repeat('x', 60),
            'active' => 1,
            'two_factor_enabled' => 1,
            'two_factor_totp_enabled' => 0,
            'two_factor_email_enabled' => 1,
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
                // add() may also create shop associations; remove only this fixture's rows.
                $this->connection->createQueryBuilder()->delete($this->employeeTable . '_shop')
                    ->where('id_employee = :id')->setParameter('id', $this->employeeId)->executeStatement();
                $this->connection->createQueryBuilder()->delete($this->employeeTable)
                    ->where('id_employee = :id')->setParameter('id', $this->employeeId)->executeStatement();
            }
            if (isset($this->entityManager) && $this->entityManager->isOpen()) {
                $this->entityManager->clear();
            }
        } finally {
            parent::tearDown();
        }
    }

    /**
     * @param array<string, int|string|null> $changes
     */
    #[DataProvider('legacyUpdates')]
    public function testLegacyUpdatePolicy(array $changes, bool $activeTotp, int $increment, bool $nullValues = false): void
    {
        if ($activeTotp) {
            $this->seed(['two_factor_totp_enabled' => 1, 'two_factor_totp_secret' => 'old-secret']);
        }
        $employee = new LegacyEmployee($this->employeeId);
        foreach ($changes as $column => $value) {
            $employee->{$column} = $value;
        }
        $employee->setFieldsToUpdate(array_fill_keys(array_keys($changes), true));
        self::assertTrue($employee->update($nullValues));
        self::assertSame(1 + $increment, $this->repository->getTrustedDeviceVersion($this->employeeId));
        $row = $this->row();
        foreach ($changes as $column => $value) {
            if ('two_factor_trusted_version' === $column) {
                continue;
            }
            $expected = $nullValues && (null === $value || '' === $value) ? null : $value;
            self::assertSame($expected, is_int($expected) ? (int) $row[$column] : $row[$column]);
        }
    }

    public static function legacyUpdates(): iterable
    {
        yield 'password, including a rehash' => [['passwd' => str_repeat('y', 60)], false, 1];
        yield 'email' => [['email' => 'changed@example.com'], false, 1];
        yield 'several changes, one increment' => [['passwd' => str_repeat('y', 60), 'email' => 'changed@example.com', 'two_factor_enabled' => 0], false, 1];
        yield 'master disabled' => [['two_factor_enabled' => 0], false, 1];
        yield 'TOTP enabled' => [['two_factor_totp_enabled' => 1], false, 1];
        yield 'TOTP disabled' => [['two_factor_totp_enabled' => 0], true, 1];
        yield 'email provider disabled' => [['two_factor_email_enabled' => 0], false, 1];
        yield 'active secret changed byte for byte' => [['two_factor_totp_secret' => 'OLD-SECRET'], true, 1];
        yield 'active secret removed with NULL' => [['two_factor_totp_secret' => null], true, 1, true];
        yield 'pending secret initialized' => [['two_factor_totp_secret' => 'draft-secret'], false, 0];
        yield 'escaped pending secret' => [['two_factor_totp_secret' => "draft'\\secret"], false, 0];
        yield 'active escaped secret' => [['two_factor_totp_secret' => "active'\\secret"], true, 1];
        yield 'unchanged active secret' => [['two_factor_totp_secret' => 'old-secret'], true, 0];
        yield 'null and empty draft secret' => [['two_factor_totp_secret' => ''], false, 0, true];
        yield 'unchanged flag' => [['two_factor_enabled' => 1], false, 0];
        yield 'requirement flag' => [['two_factor_required' => 1], false, 0];
        yield 'unrelated profile field' => [['lastname' => 'Lovelace'], false, 0];
        yield 'replaying a cached counter' => [['two_factor_trusted_version' => 999], false, 0];
    }

    public function testTwoStaleLegacyObjectsCannotLoseAnIncrement(): void
    {
        $first = new LegacyEmployee($this->employeeId);
        $second = new LegacyEmployee($this->employeeId);
        $first->email = 'first@example.com';
        $first->setFieldsToUpdate(['email' => true]);
        self::assertTrue($first->update());
        $second->email = 'second@example.com';
        $second->setFieldsToUpdate(['email' => true]);
        self::assertTrue($second->update());
        self::assertSame(3, $this->repository->getTrustedDeviceVersion($this->employeeId));
    }

    public function testLegacyUpdateWithSimultaneousAssignment(): void
    {
        // ObjectModel uses a different connection from Doctrine: set the mode on
        // the actual legacy writer, and restore it even when an assertion fails.
        $database = Db::getInstance();
        $serverVersion = (string) $database->getValue('SELECT VERSION()', false);
        if (!preg_match('/(\d+\.\d+\.\d+)-MariaDB/i', $serverVersion, $matches)
            || version_compare($matches[1], '10.3.5', '<')
        ) {
            self::markTestSkipped('SIMULTANEOUS_ASSIGNMENT requires MariaDB 10.3.5 or later; it is unavailable on MySQL.');
        }

        $originalMode = (string) $database->getValue('SELECT @@SESSION.sql_mode', false);
        $modes = array_filter(explode(',', $originalMode));
        if (!in_array('SIMULTANEOUS_ASSIGNMENT', $modes, true)) {
            $modes[] = 'SIMULTANEOUS_ASSIGNMENT';
        }
        try {
            self::assertTrue($database->execute("SET SESSION sql_mode = '" . $database->escape(implode(',', $modes)) . "'", false));
            $employee = new LegacyEmployee($this->employeeId);
            $employee->email = 'simultaneous@example.com';
            $employee->two_factor_totp_enabled = true;
            $employee->two_factor_totp_secret = 'active-secret';
            $employee->setFieldsToUpdate(['email' => true, 'two_factor_totp_enabled' => true, 'two_factor_totp_secret' => true]);
            self::assertTrue($employee->update());
            self::assertSame(2, $this->repository->getTrustedDeviceVersion($this->employeeId));
            self::assertSame('simultaneous@example.com', $this->row()['email']);
            self::assertSame('active-secret', $this->row()['two_factor_totp_secret']);

            self::assertTrue($employee->update());
            self::assertSame(2, $this->repository->getTrustedDeviceVersion($this->employeeId));
            $employee->two_factor_totp_enabled = false;
            $employee->two_factor_totp_secret = null;
            self::assertTrue($employee->update(true));
            self::assertSame(3, $this->repository->getTrustedDeviceVersion($this->employeeId));
            self::assertNull($this->row()['two_factor_totp_secret']);
        } finally {
            self::assertTrue($database->execute("SET SESSION sql_mode = '" . $database->escape($originalMode) . "'", false));
        }
    }

    public function testPartialLegacyUpdateDoesNotRestoreStaleSecurityFields(): void
    {
        $stale = new LegacyEmployee($this->employeeId);
        $current = new LegacyEmployee($this->employeeId);
        $current->two_factor_enabled = false;
        $current->setFieldsToUpdate(['two_factor_enabled' => true]);
        self::assertTrue($current->update());
        $stale->lastname = 'Lovelace';
        $stale->setFieldsToUpdate(['lastname' => true]);
        self::assertTrue($stale->update());
        self::assertSame(0, (int) $this->row()['two_factor_enabled']);
        self::assertSame(2, $this->repository->getTrustedDeviceVersion($this->employeeId));
    }

    public function testInstallationStyleInsertUsesDefaultDespiteForceIdAndCachedVersion(): void
    {
        $this->connection->createQueryBuilder()->delete($this->employeeTable)
            ->where('id_employee = :id')->setParameter('id', $this->employeeId)->executeStatement();
        $employee = new LegacyEmployee();
        $employee->id = $this->employeeId;
        $employee->force_id = true;
        $employee->firstname = 'Ada';
        $employee->lastname = 'Lovelace';
        $employee->email = 'install-' . bin2hex(random_bytes(8)) . '@example.com';
        $employee->passwd = str_repeat('x', 60);
        $employee->id_lang = 1;
        $employee->id_profile = 1;
        $employee->two_factor_trusted_version = 999;
        self::assertTrue($employee->add());
        self::assertSame(1, $this->repository->getTrustedDeviceVersion($this->employeeId));
    }

    public function testExplicitIndividualResetIncrementsOnceEvenWithoutRemainingData(): void
    {
        $handler = new ResetEmployeeTwoFactorHandler($this->repository);
        $handler->handle(new ResetEmployeeTwoFactorCommand($this->employeeId));
        self::assertSame(2, $this->repository->getTrustedDeviceVersion($this->employeeId));
        $handler->handle(new ResetEmployeeTwoFactorCommand($this->employeeId));
        self::assertSame(3, $this->repository->getTrustedDeviceVersion($this->employeeId));
    }

    public function testWebservicePasswordSetterIsCovered(): void
    {
        $employee = new LegacyEmployee($this->employeeId);
        $employee->setWsPasswd('a-new-password');
        self::assertTrue($employee->update());
        self::assertSame(2, $this->repository->getTrustedDeviceVersion($this->employeeId));
    }

    public function testLegacyPasswordRehashRevokesTrust(): void
    {
        $plaintext = 'legacy-password';
        $cookieKey = (string) self::getContainer()->getParameter('cookie_key');
        $this->seed(['passwd' => md5($cookieKey . $plaintext)]);
        $email = $this->row()['email'];
        $employee = new LegacyEmployee();
        self::assertSame($employee, $employee->getByEmail($email, $plaintext));
        self::assertSame(2, $this->repository->getTrustedDeviceVersion($this->employeeId));
        self::assertNotSame(md5($cookieKey . $plaintext), $this->row()['passwd']);
    }

    public function testFailedLegacyValidationDoesNotRevokeOrLeakTheUpdateContext(): void
    {
        $employee = new LegacyEmployee($this->employeeId);
        $employee->email = 'invalid-email';
        try {
            $employee->update();
            self::fail('Employee validation must reject an invalid email.');
        } catch (PrestaShopException) {
            self::assertSame(1, $this->repository->getTrustedDeviceVersion($this->employeeId));
        }
        $employee->email = 'valid@example.com';
        self::assertArrayNotHasKey('two_factor_trusted_version', $employee->getFields());
        self::assertTrue($employee->update());
        self::assertSame(2, $this->repository->getTrustedDeviceVersion($this->employeeId));
    }

    public function testOrmChangesAreAtomicAndAnUnrelatedFlushCannotRestoreTheVersion(): void
    {
        $employee = $this->repository->getForTwoFactor($this->employeeId);
        $employee->setPassword(str_repeat('y', 60))->setEmail('orm@example.com');
        $this->entityManager->flush();
        self::assertSame(2, $this->repository->getTrustedDeviceVersion($this->employeeId));
        self::assertSame(1, $employee->getTrustedTokenVersion(), 'The old managed counter must never be written back.');
        $employee->setLastName('Lovelace');
        $this->entityManager->flush();
        self::assertSame(2, $this->repository->getTrustedDeviceVersion($this->employeeId));
        $this->entityManager->refresh($employee);
        self::assertSame(2, $employee->getTrustedTokenVersion());
    }

    public function testOrmPendingSecretAndEphemeralCodesDoNotRevokeTrust(): void
    {
        $employee = $this->repository->getForTwoFactor($this->employeeId);
        $secret = self::getContainer()->get(TotpSecretEncryptor::class)->encrypt('draft-secret');
        $employee->setTwoFactorSecret($secret)->setTwoFactorBackupCodes(['hash']);
        $employee->setEmailAuthCode('654321');
        $this->entityManager->flush();
        self::assertSame(1, $this->repository->getTrustedDeviceVersion($this->employeeId));
        $employee->setTwoFactorEmailEnabled(false);
        $this->entityManager->flush();
        self::assertSame(2, $this->repository->getTrustedDeviceVersion($this->employeeId));
    }

    public function testOrmReconfigurationOfAnActiveSecretRevokesOnce(): void
    {
        $encryptor = self::getContainer()->get(TotpSecretEncryptor::class);
        $this->seed(['two_factor_totp_enabled' => 1, 'two_factor_totp_secret' => $encryptor->encrypt('old-secret')]);
        $employee = $this->repository->getForTwoFactor($this->employeeId);
        $employee->setTwoFactorSecret($encryptor->encrypt('new-secret'))->setTwoFactorEmailEnabled(false);
        $this->entityManager->flush();
        self::assertSame(2, $this->repository->getTrustedDeviceVersion($this->employeeId));
    }

    public function testPendingAuthenticationKeepsItsVersionAcrossSerializationAndRefresh(): void
    {
        $legacy = new LegacyEmployee($this->employeeId);
        $legacy->requestTrustedDeviceRevocation();
        self::assertTrue($legacy->update());
        $employee = $this->repository->getForTwoFactor($this->employeeId);
        self::assertSame(2, $employee->getTrustedTokenVersion());
        $authenticated = new UsernamePasswordToken($employee, 'main', ['ROLE_EMPLOYEE']);
        $pending = (new TrustedDeviceTokenFactory(new TwoFactorTokenFactory()))->create($authenticated, 'main', ['email']);
        $restored = unserialize(serialize($pending));
        self::assertInstanceOf(TwoFactorTokenInterface::class, $restored);

        // Employee's existing session serialization deliberately omits the live counter.
        // The immutable token attribute must retain 2, independently of the refreshed user.
        $restored->setUser($this->repository->getForTwoFactor($this->employeeId));
        self::assertSame(2, $restored->getAuthenticatedToken()->getAttribute(TrustedDeviceTokenFactory::AUTHENTICATION_ATTRIBUTE)['version']);
        $legacy->requestTrustedDeviceRevocation();
        self::assertTrue($legacy->update());
        $restored->setUser($this->repository->getForTwoFactor($this->employeeId));
        $refreshed = $restored->getUser();
        self::assertInstanceOf(Employee::class, $refreshed);
        self::assertSame(3, $refreshed->getTrustedTokenVersion());
        self::assertSame(2, $restored->getAuthenticatedToken()->getAttribute(TrustedDeviceTokenFactory::AUTHENTICATION_ATTRIBUTE)['version']);
    }

    public function testTwoEntityManagersCannotOverwriteTheVersionOrOtherChangedFields(): void
    {
        $events = $this->entityManager->getEventManager();
        $otherConnection = DriverManager::getConnection(
            $this->connection->getParams(),
            $this->connection->getConfiguration(),
            $events
        );
        $otherManager = null;
        try {
            $otherManager = new EntityManager($otherConnection, $this->entityManager->getConfiguration(), $events);
            $first = $this->repository->getForTwoFactor($this->employeeId);
            $second = $otherManager->find(Employee::class, $this->employeeId);
            self::assertInstanceOf(Employee::class, $second);
            $first->setPassword(str_repeat('y', 60));
            $this->entityManager->flush();
            $second->setEmail('second-manager@example.com');
            $otherManager->flush();
            self::assertSame(3, $this->repository->getTrustedDeviceVersion($this->employeeId));
            self::assertSame(str_repeat('y', 60), $this->row()['passwd']);
        } finally {
            $otherManager?->close();
            $otherConnection->close();
        }
    }

    public function testStaleOrmChangesetDoesNotIncrementWhenTheDatabaseAlreadyHasTheValue(): void
    {
        $employee = $this->repository->getForTwoFactor($this->employeeId);
        $legacy = new LegacyEmployee($this->employeeId);
        $legacy->email = 'same-target@example.com';
        $legacy->setFieldsToUpdate(['email' => true]);
        self::assertTrue($legacy->update());
        $employee->setEmail('same-target@example.com');
        $this->entityManager->flush();
        self::assertSame(2, $this->repository->getTrustedDeviceVersion($this->employeeId));
    }

    public function testVersionAndCredentialsRollBackTogetherOnFlushFailure(): void
    {
        $employee = $this->repository->getForTwoFactor($this->employeeId);
        $employee->setPassword(str_repeat('y', 60));
        $connection = $this->connection;
        $repository = $this->repository;
        $employeeId = $this->employeeId;
        $listener = new class($connection, $repository, $employeeId) {
            public function __construct(private Connection $connection, private EmployeeRepository $repository, private int $employeeId)
            {
            }

            public function preUpdate(PreUpdateEventArgs $event): void
            {
                $employee = $event->getObject();
                if (!$employee instanceof Employee || $employee->getId() !== $this->employeeId) {
                    return;
                }
                EmployeeTrustedDevicePersistenceTest::assertSame($this->connection, $event->getObjectManager()->getConnection());
                EmployeeTrustedDevicePersistenceTest::assertTrue($this->connection->isTransactionActive());
                EmployeeTrustedDevicePersistenceTest::assertSame(2, $this->repository->getTrustedDeviceVersion($this->employeeId));
                throw new RuntimeException('Abort the employee flush after the version write.');
            }
        };
        $events = $this->entityManager->getEventManager();
        $events->addEventListener(['preUpdate'], $listener);
        try {
            $this->entityManager->flush();
            self::fail('The failing flush must propagate its error.');
        } catch (RuntimeException $exception) {
            self::assertSame('Abort the employee flush after the version write.', $exception->getMessage());
        } finally {
            $events->removeEventListener(['preUpdate'], $listener);
        }
        self::assertSame(1, $this->repository->getTrustedDeviceVersion($this->employeeId));
        self::assertSame(str_repeat('x', 60), $this->row()['passwd']);
    }

    public function testGlobalResetRevokesEveryEmployeeExactlyOnce(): void
    {
        // The global operation intentionally affects all employees. Roll back all its effects.
        $this->connection->beginTransaction();
        try {
            $this->seed(['two_factor_enabled' => 0, 'two_factor_email_enabled' => 0]);
            $before = $this->connection->createQueryBuilder()->select('id_employee', 'two_factor_trusted_version')
                ->from($this->employeeTable)->executeQuery()->fetchAllKeyValue();
            $this->repository->resetAllTwoFactorData();
            foreach ($before as $id => $version) {
                self::assertSame((int) $version + 1, $this->repository->getTrustedDeviceVersion((int) $id));
            }
        } finally {
            $this->connection->rollBack();
        }
    }

    public function testDirectBackupAndEmailWritesPreserveTrust(): void
    {
        $this->repository->initializeTotpSecret($this->employeeId, 'draft-secret');
        $this->repository->saveEmailAuthCode($this->employeeId, '654321');
        $this->repository->saveBackupCodes($this->employeeId, ['first'], null);
        $this->repository->saveBackupCodes($this->employeeId, ['second'], $this->repository->getBackupCodesSnapshot($this->employeeId));
        self::assertSame(1, $this->repository->getTrustedDeviceVersion($this->employeeId));
    }

    /**
     * @param array<string, int|string|null> $changes
     */
    private function seed(array $changes): void
    {
        $qb = $this->connection->createQueryBuilder()->update($this->employeeTable)
            ->where('id_employee = :id')->setParameter('id', $this->employeeId);
        foreach ($changes as $column => $value) {
            $qb->set($column, ':' . $column)->setParameter($column, $value);
        }
        $qb->executeStatement();
    }

    /**
     * @return array<string, mixed>
     */
    private function row(): array
    {
        $row = $this->connection->createQueryBuilder()->select('*')->from($this->employeeTable)
            ->where('id_employee = :id')->setParameter('id', $this->employeeId)->setMaxResults(1)
            ->executeQuery()->fetchAssociative();
        self::assertIsArray($row);

        return $row;
    }
}
