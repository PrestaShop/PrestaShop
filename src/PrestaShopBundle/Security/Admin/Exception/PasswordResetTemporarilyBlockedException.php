<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Security\Admin\Exception;

use RuntimeException;

/**
 * This exception is sent by the EmployeePasswordResetter when a reset mail action is performed
 * too soon.
 */
class PasswordResetTemporarilyBlockedException extends RuntimeException
{
}
