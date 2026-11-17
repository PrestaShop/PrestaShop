<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\WithdrawalRequest;

use Configuration;
use DateTimeImmutable;
use Order;
use PrestaShop\PrestaShop\Core\Domain\Order\ValueObject\OrderId;
use Tools;

/**
 * Presents the "Withdraw from contract here" button of the order details and guest tracking pages.
 */
final class WithdrawalButtonPresenter
{
    public function __construct(
        private readonly WithdrawalEligibilityChecker $eligibilityChecker,
    ) {
    }

    /**
     * @param string|null $guestEmail email a guest proved the order with, null for the logged in owner
     *
     * @return array{is_eligible: bool, deadline: string|null, id_order: int|null, order_reference: string, email: string}
     */
    public function present(Order $order, ?string $guestEmail = null): array
    {
        if (!Configuration::get('PS_WITHDRAWAL_FUNCTION', null, null, (int) $order->id_shop)) {
            return ['is_eligible' => false, 'deadline' => null, 'id_order' => null, 'order_reference' => '', 'email' => ''];
        }

        $eligibility = $this->eligibilityChecker->check(new OrderId((int) $order->id), new DateTimeImmutable());
        $deadline = $eligibility->getDeadline();

        return [
            'is_eligible' => $eligibility->isEligible(),
            'deadline' => null === $deadline ? null : Tools::displayDate($deadline->format('Y-m-d H:i:s')),
            'id_order' => null === $guestEmail ? (int) $order->id : null,
            'order_reference' => (string) $order->reference,
            'email' => (string) $guestEmail,
        ];
    }
}
