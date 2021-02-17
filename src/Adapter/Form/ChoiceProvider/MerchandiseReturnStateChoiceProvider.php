<?php

namespace PrestaShop\PrestaShop\Adapter\Form\ChoiceProvider;

use OrderReturnState;
use PrestaShop\PrestaShop\Core\ConfigurationInterface;
use PrestaShop\PrestaShop\Core\Form\FormChoiceProviderInterface;

class MerchandiseReturnStateChoiceProvider implements FormChoiceProviderInterface
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
     * Get choices.
     *
     * @return array
     */
    public function getChoices()
    {
        $choices = [];
        $orderStates = OrderReturnState::getOrderReturnStates($this->contextLangId);

        foreach ($orderStates as $orderState) {
            $orderStateId = $orderState['id_order_return_state'];

            $choices[$orderState['name']] = (int) $orderStateId;
        }

        return $choices;
    }
}
