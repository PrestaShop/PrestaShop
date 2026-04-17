<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Form\ChoiceProvider;

use PrestaShop\PrestaShop\Core\Form\FormChoiceFormatter;
use PrestaShop\PrestaShop\Core\Form\FormChoiceProviderInterface;

/**
 * Gets choices for predefined order message types.
 */
final class CustomerServiceOrderMessagesNameChoiceProvider implements FormChoiceProviderInterface
{
    /**
     * @var array
     */
    private $orderMessages;

    public function __construct(array $orderMessages)
    {
        $this->orderMessages = $orderMessages;
    }

    /**
     * {@inheritdoc}
     */
    public function getChoices(): array
    {
        return FormChoiceFormatter::formatFormChoices(
            $this->orderMessages,
            'id_order_message',
            'name'
        );
    }
}
