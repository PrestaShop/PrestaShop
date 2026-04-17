<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Webservice\Exception;

/**
 * Is thrown when some constraint is violated in Webservice subdomain
 */
class WebserviceConstraintException extends WebserviceException
{
    /**
     * @var int Code is used when invalid webservice key is encountered
     */
    public const INVALID_KEY = 1;
}
