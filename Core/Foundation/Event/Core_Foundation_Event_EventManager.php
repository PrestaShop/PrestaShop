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
 *  @author 	PrestaShop SA <contact@prestashop.com>
 *  @copyright  2007-2015 PrestaShop SA
 *  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */

class Core_Foundation_Event_EventManager
{
    private $events;
    private static $instance;

    public function __construct()
    {
        $this->events = new SplPriorityQueue;
    }

    public static function getInstance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function attach($name, $callback, $priority = 0)
    {
        $this->events->insert(array($name, $callback), $priority);
    }

    public function trigger($name, $params = array(), $callback = null)
    {
        $newQueue = [];

        foreach ($this->events as $event) {
            if ($event[0] === $name) {
                $e = new Core_Foundation_Event_Event($name, $params);
                if ($r = $event[1]($e)) {
                    if (is_callable($callback)) {
                        $callback($r);
                    }
                }
            } else {
                $newQueue[] = $event;
            }
        }

        $this->events = $newQueue;
    }
}
