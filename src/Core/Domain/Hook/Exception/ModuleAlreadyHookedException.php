<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Hook\Exception;

/**
 * Thrown when trying to hook a module that is already registered on the given hook.
 */
class ModuleAlreadyHookedException extends HookException
{
}
