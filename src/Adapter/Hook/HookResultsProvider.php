<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Hook;

use Hook;
use PrestaShop\PrestaShop\Core\Hook\HookResultsProviderInterface;

final class HookResultsProvider implements HookResultsProviderInterface
{
    public function getResults(string $hookName, array $parameters = []): array
    {
        /*
         * WHY the legacy Hook::exec rather than HookManager: HookManager only forwards $array_return
         * when Symfony is not booted; under the kernel it goes through
         * HookDispatcher::dispatchRendering(), which flattens every return with
         * `empty($partialContent) ? '' : current($partialContent)` and so turns a false into an empty
         * string. This is the adapter that keeps the values intact.
         *
         * WHY nothing is caught here: HookManager swallows a module exception and returns nothing, which
         * for a hook that answers "may this happen?" means the answer silently becomes yes. Letting it
         * bubble keeps a broken module visible instead of quietly permissive.
         */
        $results = Hook::exec($hookName, $parameters, null, true);

        return is_array($results) ? $results : [];
    }
}
