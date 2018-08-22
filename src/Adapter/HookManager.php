<?php
/**
 * 2007-2018 PrestaShop.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Adapter;

use Hook;
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
     * @throws \PrestaShopException
     *
     * @return string/array modules output
     */
    public function exec(
        $hook_name,
        $hook_args = array(),
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

        if (!is_null($request)) {
            $hook_args = array_merge(array('request' => $request), $hook_args);

            // If Symfony application is booted, we use it to dispatch Hooks
            $hookDispatcher = $sfContainer->get('prestashop.hook.dispatcher');

            return $hookDispatcher->renderForParameters($hook_name, $hook_args)->getContent();
        } else {
            try {
                return Hook::exec($hook_name, $hook_args, $id_module, $array_return, $check_exceptions, $use_push, $id_shop);
            } catch (\Exception $e) {
                $logger = ServiceLocator::get('\\PrestaShop\\PrestaShop\\Adapter\\LegacyLogger');
                $logger->error(sprintf('Exception on hook %s for module %s. %s', $hook_name, $id_module, $e->getMessage()));
            }
        }
    }
}
