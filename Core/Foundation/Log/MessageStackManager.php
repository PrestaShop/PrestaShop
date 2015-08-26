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

class MessageStackManager
{
    private static $instance = null;

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

    public final function __construct()
    {
        $this->errorQueue = new \SplQueue();
        $this->warningQueue = new \SplQueue();
        $this->infoQueue = new \SplQueue();
        $this->successQueue = new \SplQueue();
    }

    public final function onError(BaseEvent $event)
    {
        $this->errorQueue->enqueue($event->getException());
    }

    public final function onWarning(BaseEvent $event)
    {
        $this->warningQueue->enqueue($event->getException());
    }

    public final function onInfo(BaseEvent $event)
    {
        $this->infoQueue->enqueue($event->getMessage());
    }

    public final function onSuccess(BaseEvent $event)
    {
        $this->successQueue->enqueue($event->getMessage());
    }

    public final function getErrorIterator()
    {
        return $this->errorQueue;
    }

    public final function getWarningIterator()
    {
        return $this->warningQueue;
    }

    public final function getInfoIterator()
    {
        return $this->infoQueue;
    }

    public final function getSuccessIterator()
    {
        return $this->successQueue;
    }
}
