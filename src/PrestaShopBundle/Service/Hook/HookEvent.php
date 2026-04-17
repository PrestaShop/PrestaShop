<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
    public function __construct(?array $contextParameters = null, ?array $hookParameters = null)
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
