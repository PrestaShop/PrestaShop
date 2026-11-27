<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Employee\QueryResult;

final class EmployeeTwoFactorSettings
{
    /**
     * @param string[] $availableProviders
     */
    public function __construct(
        private readonly bool $enabled,
        private readonly bool $hasBackupCodes,
        private readonly array $availableProviders,
        private readonly string $preferredProvider,
        private readonly string $totpSecret,
        private readonly string $qrCodeSrc,
        private readonly bool $emailCodePreparationRequired = false,
    ) {
    }

    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    public function hasBackupCodes(): bool
    {
        return $this->hasBackupCodes;
    }

    /**
     * @return string[]
     */
    public function getAvailableProviders(): array
    {
        return $this->availableProviders;
    }

    public function getPreferredProvider(): string
    {
        return $this->preferredProvider;
    }

    public function isEmailCodePreparationRequired(): bool
    {
        return $this->emailCodePreparationRequired;
    }

    public function getTotpSecret(): string
    {
        return $this->totpSecret;
    }

    public function getQrCodeSrc(): string
    {
        return $this->qrCodeSrc;
    }
}
