<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\Service\Log;

use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Determines whether Back Office activity logging is enabled for the current request.
 */
final class AdminActivityScope
{
    public const REQUEST_ATTRIBUTE = '_admin_activity_logging';

    public function __construct(
        private readonly RequestStack $requestStack,
    ) {
    }

    public function isEnabled(): bool
    {
        return true === $this->requestStack
            ->getMainRequest()
            ?->attributes
            ->get(self::REQUEST_ATTRIBUTE);
    }
}
