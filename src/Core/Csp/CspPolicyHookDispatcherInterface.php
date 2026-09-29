<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Csp;

/** Lets modules widen the CSP policy through the actionCspPolicyModifier hook, keeping the provider off legacy Hook::exec(). */
interface CspPolicyHookDispatcherInterface
{
    public function dispatch(CspPolicy $policy): void;
}
