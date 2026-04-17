<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Employee\Command;

use PrestaShop\PrestaShop\Core\Domain\ValueObject\Email;

class SendEmployeePasswordResetEmailCommand
{
    private Email $email;

    public function __construct(
        string $email
    ) {
        $this->email = new Email($email);
    }

    public function getEmail(): Email
    {
        return $this->email;
    }
}
