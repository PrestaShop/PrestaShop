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

namespace PrestaShop\PrestaShop\Core\Hook;

use PrestaShop\PrestaShop\Adapter\Hook\HookDispatcher as HookDispatcherAdapter;

/**
 * Class HookDispatcher is responsible for dispatching hooks
 */
final class HookDispatcher implements HookDispatcherInterface
{
    /**
     * @var HookDispatcherAdapter
     */
    private $hookDispatcherAdapter;

    /**
     * @param HookDispatcherAdapter $hookDispatcherAdapter
     */
    public function __construct(HookDispatcherAdapter $hookDispatcherAdapter)
    {
        $this->hookDispatcherAdapter = $hookDispatcherAdapter;
    }

    /**
     * {@inheritdoc}
     */
    public function dispatchHook(HookInterface $hook)
    {
        $this->hookDispatcherAdapter->dispatchForParameters(
            $hook->getName(),
            $hook->getParameters()
        );
    }

    /**
     * {@inheritdoc}
     */
    public function dispatchWithParameters($hookName, array $hookParameters = [])
    {
        $this->dispatchHook(new Hook($hookName, $hookParameters));
    }

    /**
     * {@inheritdoc}
     */
    public function dispatchRendering(HookInterface $hook)
    {
        $event = $this->hookDispatcherAdapter->renderForParameters(
            $hook->getName(),
            $hook->getParameters()
        );

        return new RenderedHook($hook, $event->getContent());
    }

    /**
     * {@inheritdoc}
     */
    public function dispatchRenderingWithParameters($hookName, array $hookParameters = [])
    {
        return $this->dispatchRendering(new Hook($hookName, $hookParameters));
    }
}
