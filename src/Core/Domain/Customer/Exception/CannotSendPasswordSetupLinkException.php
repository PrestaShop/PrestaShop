<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Customer\Exception;

class CannotSendPasswordSetupLinkException extends CustomerException
{
    public const GUEST_CUSTOMER = 1;
    public const DELETED_CUSTOMER = 2;
    public const INACTIVE_CUSTOMER = 3;
}
