<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Profile\Exception;

/**
 * Is thrown when some constraint is violated in Profile subdomain
 */
class ProfileConstraintException extends ProfileException
{
    /**
     * @var int Code is used when invalid profile name is encountered
     */
    public const INVALID_NAME = 1;
}
