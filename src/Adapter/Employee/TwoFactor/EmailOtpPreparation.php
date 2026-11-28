<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Employee\TwoFactor;

use DateTimeImmutable;

/**
 * Preparation outcome without exposing the email code to the presentation layer.
 */
final class EmailOtpPreparation
{
    public const AVAILABLE = 'available';
    public const SENT = 'sent';
    public const THROTTLED = 'throttled';
    public const FAILED = 'failed';
    public const CHANGED = 'changed';

    public function __construct(
        public readonly string $status,
        public readonly ?DateTimeImmutable $retryAt = null,
    ) {
    }
}
