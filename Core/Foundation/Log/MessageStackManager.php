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
use PrestaShop\PrestaShop\Core\Foundation\Exception\DevelopmentErrorException;
use PrestaShop\PrestaShop\Core\Foundation\Exception\WarningException;

/**
 * This class instance will contains 4 MessageQueue (extending SPplQueue) objects, for 4 levels of messages:
 * - error and warning, filled during Exception instantiations,
 * - info and success, filled by actions (or other Core code).
 *
 * The instance is also a listener registered on the 'message' EventDispatcher.
 * To stack a message from a Controller/action, just call $this->enqueueMessage().
 * To stack a message from elsewhere, you need the container:
 * $container->make('final:EventDispatcher/message')->dispatch('success_message', new BaseEvent('your message here'));
 */
class MessageStackManager
{
    protected $errorQueue;
    protected $warningQueue;
    protected $infoQueue;
    protected $successQueue;
    private $configuration;

    /**
     * Constructor. Gives 4 levels of SplQueues.
     *
     * This class should not been instantiated multiple times. Please use $container->make('MessageStack') instead.
     */
    final public function __construct(\Core_Business_ConfigurationInterface $configuration)
    {
        $this->errorQueue = new MessageQueue(15, $this, 'error_queue');
        $this->warningQueue = new MessageQueue(15, $this, 'warning_queue');
        $this->infoQueue = new MessageQueue(10, $this, 'info_queue');
        $this->successQueue = new MessageQueue(10, $this, 'success_queue');
        $this->configuration = $configuration;
    }

    /**
     * Tries to restore queues from persistence layer.
     *
     * If failed (throws WarningException), the persistence layer returned a corrupted data.
     *
     * @throws WarningException
     */
    final public function restoreQueues()
    {
        try {
            if ($errorData = $this->configuration->getPersistedUserData('error_queue')) {
                $this->errorQueue->unserialize($errorData);
            }
            if ($warningData = $this->configuration->getPersistedUserData('warning_queue')) {
                $this->warningQueue->unserialize($warningData);
            }
            if ($infoData = $this->configuration->getPersistedUserData('info_queue')) {
                $this->infoQueue->unserialize($infoData);
            }
            if ($successData = $this->configuration->getPersistedUserData('success_queue')) {
                $this->successQueue->unserialize($successData);
            }
        } catch (\UnexpectedValueException $uve) {
            throw new WarningException('Cannot restore message stacks from cookie', 'Maybe your PrestaShop cookie is corrupted. Try cleaning your browser cookies.');
        }
    }

    /**
     * Called by the message queue itself to trigger a persistence operation on the message stacks.
     *
     * Do not call it by yourself.
     *
     * @param MessageQueue $queue
     */
    final public function onQueueChanged(MessageQueue $queue)
    {
        $this->configuration->persistUserData($queue->name, $queue->serialize());
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
     * Dequeue and return all the error messages.
     *
     * The messages are removed (=dequeued) during this operation.
     *
     * @return string[] All error messages stacked in the queue.
     */
    final public function dequeueAllErrors()
    {
        return $this->dequeueAll($this->errorQueue);
    }

    /**
     * Dequeue and return all the warning messages.
     *
     * The messages are removed (=dequeued) during this operation.
     *
     * @return string[] All warning messages stacked in the queue.
     */
    final public function dequeueAllWarnings()
    {
        return $this->dequeueAll($this->warningQueue);
    }

    /**
     * Dequeue and return all the info messages.
     *
     * The messages are removed (=dequeued) during this operation.
     *
     * @return string[] All info messages stacked in the queue.
     */
    final public function dequeueAllInfos()
    {
        return $this->dequeueAll($this->infoQueue);
    }

    /**
     * Dequeue and return all the success messages.
     *
     * The messages are removed (=dequeued) during this operation.
     *
     * @return string[] All success messages stacked in the queue.
     */
    final public function dequeueAllSuccesses()
    {
        return $this->dequeueAll($this->successQueue);
    }

    final private function dequeueAll(MessageQueue $queue)
    {
        $return = array();
        while ($queue->count() > 0) {
            $return[] = $queue->dequeue();
        }
        return $return;
    }
}
