<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Form\IdentifiableObject\CommandBuilder;

use PrestaShop\PrestaShop\Core\Exception\CoreException;

/**
 * Thrown when trying to create a CommandField with invalid type.
 */
class DataFieldException extends CoreException
{
}
