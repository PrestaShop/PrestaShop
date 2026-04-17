<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Module\Exception;

use PrestaShop\PrestaShop\Core\Exception\CoreException;

/**
 * Default module error implementation
 * The message of this exception will be displayed to the end-user
 *
 * You can use it in a module hooked on a Symfony page
 */
class ModuleErrorException extends CoreException implements ModuleErrorInterface
{
}
