<?php
/**
 * 2007-2017 PrestaShop
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
 * @copyright 2007-2017 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */
namespace PrestaShopBundle\Service\Hook;

use Symfony\Component\EventDispatcher\Event;

/**
 * HookEvent is used in HookDispatcher.
 *
 * A HookEvent can contains parameters to give to the listeners through getHookParameters.
 */
class HookEvent extends Event
{
    private $hookParameters = array();

    /**
     * Sets the Hook parameters.
     *
     * @param array Hook parameters.
     * @return $this, for fluent use of object.
     */
    public function setHookParameters($parameters)
    {
        $this->hookParameters = $parameters;
        return $this;
    }

    /**
     * Returns Hook parameters and default values.
     *
     * More values than the param set is returned:
     * - _ps_version contains PrestaShop version, and is here only if the Hook is triggered by Symfony architecture.
     * These values can either be overriden by szetHookParameters using the same parameter key.
     *
     * @return array The array of hook parameters, more default fixed values.
     */
    public function getHookParameters()
    {
        global $kernel;

        $globalParameters = array('_ps_version' => _PS_VERSION_);

        if (!is_null($kernel) && !is_null($kernel->getContainer()->get('request_stack')->getCurrentRequest())) {
            $globalParameters['request'] = $kernel->getContainer()->get('request');
        }

        return array_merge($globalParameters, $this->hookParameters);
    }
}
