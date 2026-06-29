<?php

/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\ExtraProperty\Exception;

/**
 * Thrown when an ExtraPropertyDefinition is constructed with invalid arguments.
 *
 * Replaces generic \InvalidArgumentException so callers can catch all
 * ExtraProperty-related exceptions via ExtraPropertyException when needed.
 */
class InvalidExtraPropertyDefinitionException extends ExtraPropertyException
{
}
