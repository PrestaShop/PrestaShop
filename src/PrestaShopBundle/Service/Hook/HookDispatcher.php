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
namespace PrestaShopBundle\Service\Hook;

use PrestaShop\PrestaShop\Adapter\Hook\HookDispatcher as HookDispatcherAdapter;

/**
 * This dispatcher is used to trigger hook listeners.
 *
 * The dispatch process cannot be stopped like a common EventDispatcher.
 *
 * If the event is a RenderingHookEvent, then the final result is
 * an array of contents accessed from $event->getContent().
 *
 * @deprecated since 1.7.5, to be removed in 1.8. Use PrestaShop\PrestaShop\Adapter\Hook\HookDispatcher instead.
 */
class HookDispatcher extends HookDispatcherAdapter
{
    public function __construct()
    {
        trigger_error('Deprecated since 1.7.5, to be removed in 1.8. Use PrestaShop\PrestaShop\Core\Hook\HookDispatcher instead.', E_USER_DEPRECATED);
        parent::__construct();
    }
}
