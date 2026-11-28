<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Adapter\Employee\TwoFactor;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Adapter\Employee\TwoFactor\EmployeeTwoFactorRateLimiter;
use PrestaShop\PrestaShop\Core\Domain\Employee\Exception\EmployeeTwoFactorException;
use Symfony\Component\Lock\LockFactory;
use Symfony\Component\Lock\Store\FlockStore;
use Symfony\Component\RateLimiter\RateLimiterFactory;
use Symfony\Component\RateLimiter\Storage\InMemoryStorage;

/**
 * Independent Symfony lock factories contend on the same native filesystem lock.
 */
final class EmployeeTwoFactorVerificationLockTest extends TestCase
{
    private string $directory;

    protected function setUp(): void
    {
        $this->directory = sys_get_temp_dir() . '/prestashop-2fa-lock-' . bin2hex(random_bytes(8));
        self::assertTrue(mkdir($this->directory, 0700));
    }

    protected function tearDown(): void
    {
        try {
            if (isset($this->directory) && is_dir($this->directory)) {
                foreach (glob($this->directory . '/*') ?: [] as $file) {
                    unlink($file);
                }
                rmdir($this->directory);
            }
        } finally {
            parent::tearDown();
        }
    }

    public function testContendingVerificationsCannotBothUseTheLastAvailableFailure(): void
    {
        $storage = new InMemoryStorage();
        $firstFactory = new LockFactory(new FlockStore($this->directory));
        $secondFactory = new LockFactory(new FlockStore($this->directory));
        $first = $this->limiter($firstFactory, $storage);
        $second = $this->limiter($secondFactory, $storage);
        for ($i = 0; $i < 4; ++$i) {
            $lock = $first->startVerification(42);
            try {
                $first->recordFailure(42, $lock);
            } finally {
                $lock->release();
            }
        }

        $lock = $first->startVerification(42);
        $competitor = $secondFactory->createLock('employee_2fa_verification:42');
        try {
            self::assertFalse($competitor->acquire(), 'The other endpoint cannot enter the verification section concurrently.');
            $unrelated = $second->startVerification(43);
            $unrelated->release();
            $first->recordFailure(42, $lock);
        } finally {
            $lock->release();
            $competitor->release();
        }

        try {
            $second->startVerification(42);
            self::fail('The waiting verification must observe the fifth failure before checking its code.');
        } catch (EmployeeTwoFactorException $e) {
            self::assertSame(EmployeeTwoFactorException::TOO_MANY_ATTEMPTS, $e->getCode());
        }
        try {
            self::assertTrue($competitor->acquire(), 'Rejecting an exhausted budget must release the employee lock.');
        } finally {
            $competitor->release();
        }
    }

    public function testSuccessfulVerificationsReleaseTheLockWithoutResettingFailures(): void
    {
        $storage = new InMemoryStorage();
        $firstFactory = new LockFactory(new FlockStore($this->directory));
        $secondFactory = new LockFactory(new FlockStore($this->directory));
        $first = $this->limiter($firstFactory, $storage);
        $second = $this->limiter($secondFactory, $storage);
        for ($i = 0; $i < 4; ++$i) {
            $lock = $first->startVerification(42);
            try {
                $first->recordFailure(42, $lock);
            } finally {
                $lock->release();
            }
        }
        for ($i = 0; $i < 8; ++$i) {
            $lock = $second->startVerification(42);
            try {
                $second->assertVerificationLock($lock);
            } finally {
                $lock->release();
            }
        }
        $lock = $first->startVerification(42);
        try {
            $first->recordFailure(42, $lock);
        } finally {
            $lock->release();
        }
        $this->expectException(EmployeeTwoFactorException::class);
        $second->startVerification(42);
    }

    private function limiter(LockFactory $factory, InMemoryStorage $storage): EmployeeTwoFactorRateLimiter
    {
        return new EmployeeTwoFactorRateLimiter(
            new RateLimiterFactory(['id' => 'check', 'policy' => 'fixed_window', 'limit' => 5, 'interval' => '15 minutes'], $storage, $factory),
            new RateLimiterFactory(['id' => 'send', 'policy' => 'fixed_window', 'limit' => 3, 'interval' => '15 minutes'], new InMemoryStorage(), $factory),
            $factory
        );
    }
}
