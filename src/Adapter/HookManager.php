<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter;

use Exception;
use Hook;
use PrestaShop\PrestaShop\Core\Exception\CoreException;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Bridge to execute hooks in modern pages.
 */
class HookManager
{
    /**
     * Execute modules for specified hook.
     *
     * @param string $hook_name Hook Name
     * @param array $hook_args Parameters for the functions
     * @param int $id_module Execute hook for this module only
     * @param bool $array_return If specified, module output will be set by name in an array
     * @param bool $check_exceptions Check permission exceptions
     * @param bool $use_push Force change to be refreshed on Dashboard widgets
     * @param int $id_shop If specified, hook will be execute the shop with this ID
     *
     * @return string|array|void|null modules output
     *
     * @throws CoreException
     */
    public function exec(
        $hook_name,
        $hook_args = [],
        $id_module = null,
        $array_return = false,
        $check_exceptions = true,
        $use_push = false,
        $id_shop = null
    ) {
        $sfContainer = SymfonyContainer::getInstance();
        $request = null;

        if ($sfContainer instanceof ContainerInterface) {
            $request = $sfContainer->get('request_stack')->getCurrentRequest();
        }

        if (null !== $request) {
            $hook_args = array_merge(['request' => $request], $hook_args);

            // If Symfony application is booted, we use it to dispatch Hooks
            $hookDispatcher = $sfContainer->get('prestashop.core.hook.dispatcher');

            return $hookDispatcher
                ->dispatchRenderingWithParameters($hook_name, $hook_args)
                ->getContent();
        } else {
            try {
                return Hook::exec($hook_name, $hook_args, $id_module, $array_return, $check_exceptions, $use_push, $id_shop);
            } catch (Exception $e) {
                $logger = ServiceLocator::get(LegacyLogger::class);
                $environment = ServiceLocator::get(Environment::class);
                $logger->error(
                    sprintf(
                        'Exception on hook %s for module %s. %s',
                        $hook_name,
                        $id_module,
                        $e->getMessage()
                    ),
                    [
                        'object_type' => 'Module',
                        'object_id' => $id_module,
                        'allow_duplicate' => true,
                    ]
                );
                if ($environment->isDebug()) {
                    throw new CoreException($e->getMessage(), $e->getCode(), $e);
                }
            }
        }
    }

    public function disableHooksForModule(int $moduleId): void
    {
        Hook::disableHooksForModule($moduleId);
    }
}
