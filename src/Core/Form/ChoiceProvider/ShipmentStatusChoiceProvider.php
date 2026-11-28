<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Form\ChoiceProvider;

use PrestaShop\PrestaShop\Core\Domain\Shipment\ValueObject\ShipmentStatus;
use PrestaShop\PrestaShop\Core\Form\FormChoiceProviderInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Provides the shipment statuses as grid filter choices, using the very labels the grid renders.
 */
final class ShipmentStatusChoiceProvider implements FormChoiceProviderInterface
{
    public function __construct(
        private readonly TranslatorInterface $translator,
    ) {
    }

    /**
     * {@inheritdoc}
     */
    public function getChoices(): array
    {
        $choices = [];

        foreach (ShipmentStatus::cases() as $status) {
            $choices[$status->trans($this->translator)] = $status->value;
        }

        return $choices;
    }
}
