<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
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
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace PrestaShopBundle\DataCollector;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\DataCollector\DataCollector;

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
     * {@inheritdoc}
     */
    public function collect(Request $request, Response $response, \Exception $exception = null)
    {
        $hooks = $this->registry->getHooks();
        $calledHooks = $this->registry->getCalledHooks();
        $notCalledHooks = $this->registry->getNotCalledHooks();
        $this->data = [
            'hooks' => $this->stringifyHookArguments($hooks),
            'calledHooks' => $this->stringifyHookArguments($calledHooks),
            'notCalledHooks' => $this->stringifyHookArguments($notCalledHooks),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function unserialize($data)
    {
        // it seems that php 7.3 has a bug with unserialize: https://bugs.php.net/bug.php?id=77302
        $this->data = is_array($data) ? $data : @unserialize($data);
    }

    /**
     * Return the list of every dispatched legacy hooks during one request.
     *
     * @return array
     */
    public function getHooks()
    {
        return $this->data['hooks'];
    }

    /**
     * Return the list of every called legacy hooks during one request.
     *
     * @return array
     */
    public function getCalledHooks()
    {
        return $this->data['calledHooks'];
    }

    /**
     * Return the list of every uncalled legacy hooks during oHookne request.
     *
     * @return array
     */
    public function getNotCalledHooks()
    {
        return $this->data['notCalledHooks'];
    }

    /**
     * {@inheritdoc}
     */
    public function reset()
    {
        $this->data = [];
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'ps.hooks_collector';
    }

    /**
     * @param array $hooksList
     *
     * @return array a better representation of arguments for HTML rendering
     */
    private function stringifyHookArguments(array &$hooksList)
    {
        foreach ($hooksList as &$hookList) {
            foreach ($hookList as &$hook) {
                // Symfony Request object is quite heavy and is already given by debug tooling. Don't clone it again.
                if (isset($hook['args']['request'])) {
                    $hook['args']['request'] = 'Hook use client request as argument. For more details, cf. "Request/Response panel".';
                }

                $hook['args'] = $this->cloneVar($hook['args']);

                foreach ($hook['modules'] as &$modulesByType) {
                    foreach ($modulesByType as $type => &$module) {
                        if (empty($module)) {
                            unset($modulesByType[$type]);
                            continue;
                        }

                        if (array_key_exists('args', $module)) {
                            // Symfony Request object is quite heavy and is already given by debug tooling. Don't clone it again.
                            if (isset($module['args']['request'])) {
                                $module['args']['request'] = 'Module use client request as argument. For more details, cf. "Request/Response panel".';
                            }

                            $module['args'] = $this->cloneVar($module['args']);
                        }
                    }
                }
            }
        }

        return $hooksList;
    }
}
