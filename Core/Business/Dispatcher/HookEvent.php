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
namespace PrestaShop\PrestaShop\Core\Business\Dispatcher;

use PrestaShop\PrestaShop\Core\Foundation\Dispatcher\BaseEvent;
use PrestaShop\PrestaShop\Core\Foundation\Exception\WarningException;

/**
 * A HookEvent is a more structured event, when complex operation must be done and could want a result.
 *
 * This class will brings parameters to the listener and will keep the result of each listener.
 * Particulary usefull to call the old Legacy hooks systems (but does not guarantee on the retro-compatibility of the parameters)
 */
class HookEvent extends BaseEvent
{
    private $result = array();
    private $parameters = array();
    private $canBeStopped = false;

    /**
     * Constructor.
     *
     * @param boolean $canBeStopped True to allow the hook event to be stopped during propagation, by listeners.
     */
    public function __construct($canBeStopped = false)
    {
        $this->canBeStopped = $canBeStopped;
    }

    /**
     * Stops the propagation of the event to further event listeners.
     *
     * {@inheritdoc}
     *
     * For a HookEvent, we cannot stop the propagation if the event has been constructed with the option to avoid this.
     */
    public function stopPropagation()
    {
        if ($this->canBeStopped) {
            parent::stopPropagation();
        } else {
            throw new WarningException('A hook listener tried to stop the propagation of a Hook event.', 'Listener: '.WarningException::getCallerInfo());
        }
    }

    /**
     * Sets the result of the event. This will replace the previous one if another listener has been called before.
     *
     * To complete the existing result instead of replacing it, use appendHookResult().
     *
     * If you set a string here, then the appendHookResult() will concat elements in a string
     * instead of adding element in the default array.
     *
     * @param string|array[undefined] $result
     */
    final public function setHookResult($result)
    {
        $this->result = $result;
    }

    /**
     * Add/Merge element to the result of the event.
     *
     * This will complete the result if it's an array (by default), or will concat if it's a string.
     * If the $append is already an array, its content will be merged into the result's event. Else it will be appended.
     *
     * @param string|array[undefined] $append
     */
    final public function appendHookResult($append)
    {
        if (is_array($this->result)) {
            $this->result = array_merge($this->result, (array)$append);
        } else {
            $this->result = (string)$this->result . (string)$append;
        }
    }

    /**
     * Gets the Hook results. Most of the time in an array, but can be a concatened string in specific cases.
     *
     * @return string|array[undefined]
     */
    final public function getHookResult()
    {
        return $this->result;
    }

    /**
     * Sets (and replace) the parameters as an indexed array of elements.
     *
     * The listener will retreive these parameters if needed.
     *
     * @param array[undefined] $parameters
     */
    final public function setHookParameters(array $parameters)
    {
        $this->parameters = $parameters;
    }

    /**
     * Append parameters in the existing indexed array by merging arrays.
     *
     * @param array[undefined] $append
     */
    final public function appendHookParameters(array $append)
    {
        $this->parameters = array_merge($this->parameters, (array)$append);
    }

    /**
     * Add a parameter to the existing indexed array.
     *
     * @param string $key
     * @param mixed $value
     */
    final public function addHookParameter($key, $value)
    {
        $this->parameters[(string)$key] = $value;
    }

    /**
     * Gets the hook parameters as an indexed array.
     *
     * @return array[undefined] An indexed array with Hook parameters.
     */
    final public function getHookParameters()
    {
        return $this->parameters;
    }
}
