<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Form\ChoiceProvider;

use OrderReturnState;
use PrestaShop\PrestaShop\Core\Domain\OrderReturnState\Exception\OrderReturnStateException;
use PrestaShop\PrestaShop\Core\Form\FormChoiceProviderInterface;

class OrderReturnStateChoiceProvider implements FormChoiceProviderInterface
{
    /**
     * @var int
     */
    private $contextLangId;

    /**
     * @param int $contextLangId
     */
    public function __construct(int $contextLangId)
    {
        $this->contextLangId = $contextLangId;
    }

    /**
     * Get available order return states.
     *
     * @return array
     *
     * @throws OrderReturnStateException
     */
    public function getChoices(): array
    {
        $choices = [];
        $orderStates = OrderReturnState::getOrderReturnStates($this->contextLangId);

        foreach ($orderStates as $orderState) {
            $indexName = sprintf('%s - %s', $orderState['id_order_return_state'], $orderState['name']);
            $choices[$indexName] = (int) $orderState['id_order_return_state'];
        }

        return $choices;
    }
}
