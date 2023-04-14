<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
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
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace PrestaShopBundle\Service\Hook;

use Symfony\Contracts\EventDispatcher\Event;

/**
 * HookEvent is used in HookDispatcher.
 *
 * A HookEvent can contain parameters to give to the listeners through getHookParameters.
 */
class HookEvent extends Event
{
    /**
     * Hook extra parameters
     *
     * @var array
     */
    private $hookParameters = [];

    /**
     * Hook context, such as PrestaShop version or request and controller
     *
     * @var array
     */
    private $contextParameters = [];

    /**
     * @param array $contextParameters
     * @param array $hookParameters
     */
    public function __construct(array $contextParameters = null, array $hookParameters = null)
    {
        if (null !== $contextParameters) {
            $this->contextParameters = $contextParameters;
        }

        if (null !== $hookParameters) {
            $this->hookParameters = $hookParameters;
        }
    }

    /**
     * Sets the Hook parameters.
     *
     * @param array $parameters
     *
     * @return self
     */
    public function setHookParameters($parameters)
    {
        $this->hookParameters = $parameters;

        return $this;
    }

    /**
     * Returns Hook parameters and context parameters
     *
     * @return array
     */
    public function getHookParameters()
    {
        return array_merge($this->contextParameters, $this->hookParameters);
    }
}
