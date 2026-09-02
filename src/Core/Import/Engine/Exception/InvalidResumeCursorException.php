<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Import\Engine\Exception;

/**
 * Thrown when a persisted resume cursor cannot be interpreted by the reader
 * it is handed back to.
 */
class InvalidResumeCursorException extends ImportEngineException
{
}
