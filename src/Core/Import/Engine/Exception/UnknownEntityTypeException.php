<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Import\Engine\Exception;

/**
 * Thrown when no importer is registered for the requested entity type.
 */
class UnknownEntityTypeException extends ImportEngineException
{
}
