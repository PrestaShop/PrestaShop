<?php

/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\ExtraProperty\Exception;

use PrestaShop\PrestaShop\Core\Exception\CoreException;

/**
 * Base exception class for all ExtraProperty-related exceptions.
 *
 * Extend this class to create more fine-grained exception types within the
 * ExtraProperty namespace so callers can catch them individually or as a group.
 */
class ExtraPropertyException extends CoreException
{
}
