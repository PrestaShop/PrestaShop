<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Form\IdentifiableObject\OptionProvider;

use PrestaShop\PrestaShop\Adapter\Form\ChoiceProvider\DiscountTypeChoiceProvider;

class DiscountFormOptionsProvider implements FormOptionsProviderInterface
{
    public function __construct(
        private readonly DiscountTypeChoiceProvider $discountTypeChoiceProvider,
    ) {
    }

    public function getOptions(int $id, array $data): array
    {
        return [
            'discount_type' => $data['information']['discount_type'] ?? '',
            'available_discount_types' => $this->discountTypeChoiceProvider->getChoices(),
        ];
    }

    public function getDefaultOptions(array $data): array
    {
        return [
            'available_discount_types' => $this->discountTypeChoiceProvider->getChoices(),
        ];
    }
}
