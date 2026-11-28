<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Employee\TwoFactor;

use PrestaShop\PrestaShop\Core\Domain\Employee\Exception\EmployeeTwoFactorException;
use Symfony\Component\Lock\Exception\LockAcquiringException;
use Symfony\Component\Lock\Exception\LockExpiredException;
use Symfony\Component\Lock\LockFactory;
use Symfony\Component\Lock\LockInterface;
use Symfony\Component\RateLimiter\RateLimit;
use Symfony\Component\RateLimiter\RateLimiterFactory;

final class EmployeeTwoFactorRateLimiter
{
    // This lease protects one verification, not the fifteen-minute failure window.
    private const VERIFICATION_LOCK_TTL = 300.0;

    public function __construct(
        private readonly RateLimiterFactory $checkLimiter,
        private readonly RateLimiterFactory $sendLimiter,
        private readonly LockFactory $lockFactory,
    ) {
    }

    public function startVerification(int $employeeId): LockInterface
    {
        // Use a different resource from the limiter's own short-lived storage lock.
        $lock = $this->lockFactory->createLock('employee_2fa_verification:' . $employeeId, self::VERIFICATION_LOCK_TTL);
        $keepLock = false;
        try {
            if (!$lock->acquire(true)) {
                throw new LockAcquiringException('Cannot acquire the employee verification lock.');
            }
            $limit = $this->checkLimiter->create((string) $employeeId)->consume(0);
            if (!$limit->isAccepted() || $limit->getRemainingTokens() <= 0) {
                throw new EmployeeTwoFactorException('Too many verification attempts.', EmployeeTwoFactorException::TOO_MANY_ATTEMPTS);
            }
            $this->assertVerificationLock($lock);
            $keepLock = true;

            return $lock;
        } finally {
            if (!$keepLock) {
                $lock->release();
            }
        }
    }

    public function recordFailure(int $employeeId, LockInterface $lock): void
    {
        $this->assertVerificationLock($lock);
        $this->checkLimiter->create((string) $employeeId)->consume();
    }

    public function assertVerificationLock(LockInterface $lock): void
    {
        if ($lock->isExpired() || !$lock->isAcquired()) {
            throw new LockExpiredException('The employee verification lock expired before verification completed.');
        }
    }

    public function consumeSend(int $employeeId): RateLimit
    {
        return $this->sendLimiter->create((string) $employeeId)->consume();
    }
}
