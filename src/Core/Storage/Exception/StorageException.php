<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Storage\Exception;

use PrestaShop\PrestaShop\Core\Exception\CoreException;

/**
 * Base exception for every storage failure, so that callers do not have to catch the
 * exceptions of the underlying library.
 */
class StorageException extends CoreException
{
}
