<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Form\ChoiceProvider;

use PrestaShop\PrestaShop\Core\Domain\WithdrawalRequest\ValueObject\WithdrawalRequestStatus;
use PrestaShop\PrestaShop\Core\Form\FormChoiceProviderInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Provides the withdrawal request statuses, keyed by their translated label.
 */
final class WithdrawalRequestStatusChoiceProvider implements FormChoiceProviderInterface
{
    public function __construct(
        private readonly TranslatorInterface $translator,
    ) {
    }

    /**
     * @return array<string, string>
     */
    public function getChoices(): array
    {
        return [
            $this->translator->trans('Pending', [], 'Admin.Global') => WithdrawalRequestStatus::PENDING->value,
            $this->translator->trans('Processed', [], 'Admin.Orderscustomers.Feature') => WithdrawalRequestStatus::PROCESSED->value,
            $this->translator->trans('Refused', [], 'Admin.Orderscustomers.Feature') => WithdrawalRequestStatus::REFUSED->value,
        ];
    }
}
