<?php
/**
 * 2007-2020 PrestaShop SA and Contributors
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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2020 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShopBundle\Service\Hook;

use AppKernel;
use PrestaShop\PrestaShop\Adapter\SymfonyContainer;
use Symfony\Component\EventDispatcher\Event;

/**
 * HookEvent is used in HookDispatcher.
 *
 * A HookEvent can contains parameters to give to the listeners through getHookParameters.
 */
class HookEvent extends Event
{
    private $hookParameters = [];

    /**
     * Sets the Hook parameters.
     *
     * @param array hook parameters
     *
     * @return $this, for fluent use of object
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
     * These values can either be overriden by setHookParameters using the same parameter key.
     *
     * @return array the array of hook parameters, more default fixed values
     */
    public function getHookParameters()
    {
        $globalParameters = ['_ps_version' => AppKernel::VERSION];

        $sfContainer = SymfonyContainer::getInstance();
        if (null !== $sfContainer && null !== $sfContainer->get('request_stack')->getCurrentRequest()) {
            $request = $sfContainer->get('request_stack')->getCurrentRequest();
            $globalParameters['request'] = $request;
            $globalParameters['route'] = $request->get('_route');
        }

        return array_merge($globalParameters, $this->hookParameters);
    }
}
