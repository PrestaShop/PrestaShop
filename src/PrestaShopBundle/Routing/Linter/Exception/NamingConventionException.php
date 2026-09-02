<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Routing\Linter\Exception;

use RuntimeException;

/**
 * Thrown when naming convention is not followed
 */
class NamingConventionException extends LinterException
{
    /**
     * @var string
     */
    protected $expectedRouteName;

    public function __construct($message = '', $code = 0, ?RuntimeException $previous = null, $expectedRouteName = null)
    {
        $this->expectedRouteName = $expectedRouteName;
        parent::__construct($message, $code, $previous);
    }

    /**
     * @return string
     */
    public function getExpectedRouteName(): string
    {
        return $this->expectedRouteName;
    }
}
