<?php
/**
 * 2007-2018 PrestaShop
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

namespace PrestaShopBundle\DataCollector;

use Symfony\Component\HttpKernel\DataCollector\DataCollector;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Collect all information about Legacy hooks and make it available
 * in the Symfony Web profiler.
 */
final class HookDataCollector extends DataCollector
{
    /**
     * @var HookRegistry
     */
    private $registry;

    public function __construct(HookRegistry $registry)
    {
        $this->registry = $registry;
    }

    /**
     * @{inheritdoc}
     */
    public function collect(Request $request, Response $response, \Exception $exception = null)
    {
        $hooks = $this->registry->getHooks();
        $calledHooks = $this->registry->getCalledHooks();
        $notCalledHooks = $this->registry->getNotCalledHooks();
        $this->data = array(
            'hooks' => $this->stringifyHookArguments($hooks),
            'calledHooks' => $this->stringifyHookArguments($calledHooks),
            'notCalledHooks' => $this->stringifyHookArguments($notCalledHooks),
        );
    }

    /**
     * Return the list of every dispatched legacy hooks during one request.
     * @return array
     */
    public function getHooks()
    {
        return $this->data['hooks'];
    }

    /**
     * Return the list of every called legacy hooks during one request.
     * @return array
     */
    public function getCalledHooks()
    {
        return $this->data['calledHooks'];
    }

    /**
     * Return the list of every uncalled legacy hooks during oHookne request.
     * @return array
     */
    public function getNotCalledHooks()
    {
        return $this->data['notCalledHooks'];
    }

    /**
     * @{inheritdoc}
     */
    public function getName()
    {
        return 'ps.hooks_collector';
    }

    /**
     * @param array $hooksList
     * @return array a better representation of arguments for HTML rendering.
     */
    private function stringifyHookArguments(array &$hooksList)
    {
        foreach ($hooksList as &$hookList) {
            foreach ($hookList as &$hook) {
                $hook['args'] = $this->cloneVar($hook['args']);

                foreach ($hook['modules'] as &$modulesByType) {
                    foreach ($modulesByType as $type => &$module) {
                        if (empty($module)) {
                            unset($modulesByType[$type]);
                        }

                        if (array_key_exists('args', $module)) {
                            $module['args'] = $this->cloneVar($module['args']);
                        }
                    }
                }
            }
        }

        return $hooksList;
    }
}
