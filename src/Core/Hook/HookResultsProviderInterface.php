<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Hook;

/**
 * Executes a hook and hands back what every listening module returned.
 *
 * HookDispatcherInterface renders hooks: it flattens each module return into a string, so a hook whose
 * contract is a decision rather than a display - "may this happen?" - cannot be dispatched through it.
 * This interface exists for that second kind of hook.
 */
interface HookResultsProviderInterface
{
    /**
     * @param array<string, mixed> $parameters
     *
     * @return array<string, mixed> the value each module returned, keyed by module name
     */
    public function getResults(string $hookName, array $parameters = []): array;
}
