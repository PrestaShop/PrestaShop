<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Form\ChoiceProvider;

use PrestaShop\PrestaShop\Core\Form\FormChoiceFormatter;
use PrestaShop\PrestaShop\Core\Form\FormChoiceProviderInterface;
use Shop;

/**
 * Provides shop names by name => id value pairs
 */
final class ShopNameByIdChoiceProvider implements FormChoiceProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function getChoices()
    {
        return FormChoiceFormatter::formatFormChoices(
            Shop::getShops(),
            'id_shop',
            'name'
        );
    }
}
