<?php

/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\ApiPlatform\Exception;

use ApiPlatform\Exception\InvalidResourceException;

/**
 * Is thrown when the CQRS query property is not defined on a resource on which it should be.
 */
class CQRSQueryNotFoundException extends InvalidResourceException
{
}
