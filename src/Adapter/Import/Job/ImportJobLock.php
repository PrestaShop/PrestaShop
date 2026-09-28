<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Import\Job;

use Symfony\Component\Lock\LockFactory;
use Symfony\Component\Lock\LockInterface;

/**
 * Per-job mutual exclusion, and the only place the lock key is spelled.
 *
 * Acquisition never blocks: a second batch of the same job must be refused, not queued, or the
 * caller would hold a request open waiting for work it already asked for.
 */
final class ImportJobLock
{
    private const KEY_PREFIX = 'prestashop-import-job-';

    public function __construct(
        private readonly LockFactory $lockFactory,
    ) {
    }

    /**
     * @return LockInterface|null null when another request holds it
     */
    public function acquire(string $importJobUuid): ?LockInterface
    {
        $lock = $this->lockFactory->createLock(self::KEY_PREFIX . $importJobUuid);

        return $lock->acquire() ? $lock : null;
    }
}
