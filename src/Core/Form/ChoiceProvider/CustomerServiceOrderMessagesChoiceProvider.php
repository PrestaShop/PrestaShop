<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Form\ChoiceProvider;

use PrestaShop\PrestaShop\Adapter\OrderMessage\OrderMessageProvider;
use PrestaShop\PrestaShop\Core\Form\ConfigurableFormChoiceProviderInterface;
use PrestaShop\PrestaShop\Core\Form\FormChoiceFormatter;

/**
 * Selects order messages itself.
 */
final class CustomerServiceOrderMessagesChoiceProvider implements ConfigurableFormChoiceProviderInterface
{
    /**
     * @var OrderMessageProvider
     */
    private $orderMessageProvider;

    public function __construct(OrderMessageProvider $orderMessageProvider)
    {
        $this->orderMessageProvider = $orderMessageProvider;
    }

    /**
     * {@inheritdoc}
     */
    public function getChoices(array $options): array
    {
        return FormChoiceFormatter::formatFormChoices(
            $this->orderMessageProvider->getMessages($options['lang_id']),
            'message',
            'id_order_message'
        );
    }
}
