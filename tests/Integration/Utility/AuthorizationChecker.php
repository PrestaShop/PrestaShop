<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Utility;

use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class AuthorizationChecker implements AuthorizationCheckerInterface
{
    public function isGranted($attributes, $subject = null): bool
    {
        return true;
    }
}
