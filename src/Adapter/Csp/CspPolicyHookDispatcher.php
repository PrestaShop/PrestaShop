<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Csp;

use Hook;
use PrestaShop\PrestaShop\Core\Csp\CspPolicy;
use PrestaShop\PrestaShop\Core\Csp\CspPolicyHookDispatcherInterface;

/** Dispatches the actionCspPolicyModifier hook via legacy Hook::exec(), the only dispatcher the front-office legacy container has. */
final class CspPolicyHookDispatcher implements CspPolicyHookDispatcherInterface
{
    public function dispatch(CspPolicy $policy): void
    {
        Hook::exec('actionCspPolicyModifier', ['policy' => $policy]);
    }
}
