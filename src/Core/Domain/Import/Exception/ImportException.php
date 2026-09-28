<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Import\Exception;

use PrestaShop\PrestaShop\Core\Domain\Exception\DomainException;

/**
 * Base exception for the Import domain.
 *
 * Engine failures (ImportEngineException) are outside this hierarchy; handlers convert them at the
 * boundary.
 */
class ImportException extends DomainException
{
}
