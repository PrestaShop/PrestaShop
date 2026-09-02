<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Employee\Command;

class ResetEmployeePasswordCommand
{
    public function __construct(
        private readonly string $resetToken,
        private readonly string $password
    ) {
    }

    public function getResetToken(): string
    {
        return $this->resetToken;
    }

    public function getPassword(): string
    {
        return $this->password;
    }
}
