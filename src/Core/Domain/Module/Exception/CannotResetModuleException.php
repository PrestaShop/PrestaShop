<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Module\Exception;

/**
 * Is thrown when required module cannot be found
 */
class CannotResetModuleException extends ModuleException
{
    public const NOT_INSTALLED = 1;

    public const NOT_ACTIVE = 1;
}
