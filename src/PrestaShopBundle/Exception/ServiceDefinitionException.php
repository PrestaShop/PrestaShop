<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Exception;

use Exception;

/**
 * Exception thrown when a service definition failed.
 */
class ServiceDefinitionException extends Exception
{
    /**
     * @var string
     */
    public $serviceId;

    /**
     * @param string $message
     * @param string $serviceId
     * @param int $code
     * @param Exception $previous
     */
    public function __construct($message, $serviceId, $code = 0, ?Exception $previous = null)
    {
        $this->serviceId = $serviceId;

        parent::__construct($message, $code, $previous);
    }
}
