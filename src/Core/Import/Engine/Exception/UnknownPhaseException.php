<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Import\Engine\Exception;

/**
 * Thrown when a phase id does not match any phase declared by the importer
 * (e.g. a deploy changed the phase list mid-run).
 */
class UnknownPhaseException extends ImportEngineException
{
}
