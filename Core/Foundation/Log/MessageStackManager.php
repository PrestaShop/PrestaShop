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
namespace PrestaShop\PrestaShop\Core\Foundation\Log;

use PrestaShop\PrestaShop\Core\Foundation\Dispatcher\BaseEvent;

/**
 * This singleton will contains 4 SPplQueue objects, for 4 levels of messages:
 * - error and warning, filled during Exception instantiations
 * - info and success, filled by actions (or other Core code).
 *
 * The singleton is also a listener registered on the 'message' EventDispatcher.
 */
class MessageStackManager
{
    private static $instance = null;

    /**
     * Way to retrieve singleton object.
     *
     * @return \PrestaShop\PrestaShop\Core\Foundation\Log\MessageStackManager
     */
    public static function getInstance()
    {
        if (self::$instance == null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private $errorQueue;
    private $warningQueue;
    private $infoQueue;
    private $successQueue;

    /**
     * Private constructor. Use singleton getter instead: getInstance()
     */
    final private function __construct()
    {
        $this->errorQueue = new \SplQueue();
        $this->warningQueue = new \SplQueue();
        $this->infoQueue = new \SplQueue();
        $this->successQueue = new \SplQueue();
    }

    /**
     * Triggered when a ErrorException or DevelopmentErrorException is instantiated.
     *
     * Do not call it by yourself.
     *
     * @param BaseEvent $event
     */
    final public function onError(BaseEvent $event)
    {
        $this->errorQueue->enqueue($event->getException());
    }

    /**
     * Triggered when a WarningException is instantiated.
     *
     * Do not call it by yourself.
     *
     * @param BaseEvent $event
     */
    final public function onWarning(BaseEvent $event)
    {
        $this->warningQueue->enqueue($event->getException());
    }

    /**
     * Triggered by the 'message' EventDisptacher
     *
     * Do not call it by yourself.
     *
     * @param BaseEvent $event
     */
    final public function onInfo(BaseEvent $event)
    {
        $this->infoQueue->enqueue($event->getMessage());
    }

    /**
     * Triggered by the 'message' EventDisptacher
     *
     * Do not call it by yourself.
     *
     * @param BaseEvent $event
     */
    final public function onSuccess(BaseEvent $event)
    {
        $this->successQueue->enqueue($event->getMessage());
    }

    /**
     * Gets the Error SplQueue.
     *
     * @return \SplQueue
     */
    final public function getErrorIterator()
    {
        return $this->errorQueue;
    }

    /**
     * Gets the Warning SplQueue.
     *
     * @return \SplQueue
     */
    final public function getWarningIterator()
    {
        return $this->warningQueue;
    }

    /**
     * Gets the Info SplQueue.
     *
     * @return \SplQueue
     */
    final public function getInfoIterator()
    {
        return $this->infoQueue;
    }

    /**
     * Gets the Success SplQueue.
     *
     * @return \SplQueue
     */
    final public function getSuccessIterator()
    {
        return $this->successQueue;
    }
}
