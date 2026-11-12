<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\CustomerB2b\Exception;

use PrestaShop\PrestaShop\Core\Domain\Customer\Exception\CannotSendPasswordSetupLinkException;
use PrestaShop\PrestaShop\Core\Domain\CustomerB2b\ValueObject\CustomerB2bId;

class PasswordSetupLinkNotSentException extends CustomerB2bException
{
    public function __construct(
        public readonly CustomerB2bId $id,
        CannotSendPasswordSetupLinkException $previous,
    ) {
        parent::__construct(\sprintf('Failed to send a password setup link to B2B customer #%d.', $id->getValue()), 0, $previous);
    }
}
