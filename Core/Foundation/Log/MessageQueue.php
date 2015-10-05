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

class MessageQueue extends \SplQueue
{
    private $messageStackManager;
    private $maxLength;
    public $name;

    final public function __construct($maxLength = 0, MessageStackManager $messageStackManager = null, $queueName = null)
    {
        $this->messageStackManager = $messageStackManager;
        $this->maxLength = $maxLength;
        $this->name = $queueName;
    }

    final public function enqueue($value)
    {
        // Flatten Exceptions to allow serialization.
        if ($value instanceof \Exception) {
            $value = $value->__toString();
        }
        parent::enqueue($value);

        // limit queue size
        if ($this->maxLength > 0) {
            while ($this->count() > $this->maxLength) {
                $this->shift();
            }
        }

        // call manager to persist if exists.
        if ($this->messageStackManager) {
            $this->messageStackManager->onQueueChanged($this);
        }
    }

    final public function dequeue()
    {
        $item = parent::dequeue();

        // call manager to persist if exists.
        if ($this->messageStackManager) {
            $this->messageStackManager->onQueueChanged($this);
        }

        return $item;
    }
}
