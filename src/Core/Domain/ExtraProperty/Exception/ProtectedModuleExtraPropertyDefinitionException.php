<?php

/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\ExtraProperty\Exception;

/**
 * Thrown when attempting to edit or delete an extra property definition
 * that is owned by a module. Module-owned definitions are read-only from the BO UI.
 */
class ProtectedModuleExtraPropertyDefinitionException extends ExtraPropertyException
{
}
