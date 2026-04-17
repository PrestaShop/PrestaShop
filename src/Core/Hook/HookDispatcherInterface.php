<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Hook;

use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

/**
 * Interface HookDispatcherInterface defines contract for hook dispatcher
 * Extends EventDispatcherInterface for compatibility with the Event Dispatcher component.
 */
interface HookDispatcherInterface extends EventDispatcherInterface
{
    /**
     * Dispatch given hook.
     *
     * @param HookInterface $hook
     */
    public function dispatchHook(HookInterface $hook);

    /**
     * Dispatch hook with raw parameters.
     *
     * @param string $hookName
     * @param array $hookParameters
     */
    public function dispatchWithParameters($hookName, array $hookParameters = []);

    /**
     * Dispatch rendering hook.
     *
     * @param HookInterface $hook
     *
     * @return RenderedHookInterface
     */
    public function dispatchRendering(HookInterface $hook);

    /**
     * Dispatch rendering hook with parameters.
     *
     * @param string $hookName
     * @param array $hookParameters
     *
     * @return RenderedHookInterface
     */
    public function dispatchRenderingWithParameters($hookName, array $hookParameters = []);
}
