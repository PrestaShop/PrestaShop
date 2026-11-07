<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Form\ChoiceProvider;

use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\Domain\Shop\Query\GetShopTree;
use PrestaShop\PrestaShop\Core\Form\FormChoiceProviderInterface;

/**
 * Provides the shops the context employee has access to, grouped by shop group name.
 */
final class ShopByGroupChoiceProvider implements FormChoiceProviderInterface
{
    public function __construct(
        private readonly CommandBusInterface $queryBus,
    ) {
    }

    /**
     * @return array<string, array<string, int>>
     */
    public function getChoices(): array
    {
        $choices = [];
        foreach ($this->queryBus->handle(new GetShopTree()) as $group) {
            foreach ($group['shops'] as $shop) {
                $label = isset($choices[$group['name']][$shop['name']]) ? sprintf('%s (%d)', $shop['name'], $shop['id']) : $shop['name'];
                $choices[$group['name']][$label] = $shop['id'];
            }
        }

        return $choices;
    }
}
