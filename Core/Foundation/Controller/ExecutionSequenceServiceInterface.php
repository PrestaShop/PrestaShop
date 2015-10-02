<?php
/**
 * 2007-2015 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
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
 * @copyright 2007-2015 PrestaShop SA
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */
namespace PrestaShop\PrestaShop\Core\Foundation\Controller;

/**
 * This is the interface that a Service must implement to be able to register
 * listener to the routing dispatcher (action execution sequence).
 *
 * For partial use of this interface, you can use the Wrapper:
 * @see \PrestaShop\PrestaShop\Core\Foundation\Controller\ExecutionSequenceServiceWrapper
 */
interface ExecutionSequenceServiceInterface
{
    /**
     * Gets an indexed array of callables for the init_action event.
     * The index is the priority of the callable.
     *
     * If at least one listener calls $event->stopPropagation(), the action execution will be forbidden.
     *
     * @return array[callable]
     */
    public function getInitListeners();

    /**
     * Gets an indexed array of callables for the before_action event.
     * The index is the priority of the callable.
     *
     * If at least one listener calls $event->stopPropagation(), the action execution will be forbidden.
     *
     * @return array[callable]
     */
    public function getBeforeListeners();

    /**
     * Gets an indexed array of callables for the after_action event.
     * The index is the priority of the callable.
     *
     * If at least one listener calls $event->stopPropagation(), a redirection to a forbidden URL will be
     * done (but the action was executed before in all cases).
     *
     * @return array[callable]
     */
    public function getAfterListeners();

    /**
     * Gets an indexed array of callables for the close_action event.
     * The index is the priority of the callable.
     *
     * If at least one listener calls $event->stopPropagation(), a redirection to a forbidden URL will be
     * done (but the action was executed before in all cases).
     *
     * @return array[callable]
     */
    public function getCloseListeners();
}
