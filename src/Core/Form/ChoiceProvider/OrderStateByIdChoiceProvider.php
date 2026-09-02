<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Form\ChoiceProvider;

use PrestaShop\PrestaShop\Core\Form\ConfigurableFormChoiceProviderInterface;
use PrestaShop\PrestaShop\Core\Form\FormChoiceAttributeProviderInterface;
use PrestaShop\PrestaShop\Core\Form\FormChoiceFormatter;
use PrestaShop\PrestaShop\Core\Form\FormChoiceProviderInterface;
use PrestaShop\PrestaShop\Core\Order\OrderStateDataProviderInterface;
use PrestaShop\PrestaShop\Core\Util\ColorBrightnessCalculator;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class OrderStateByIdChoiceProvider provides order state choices with ID values.
 */
final class OrderStateByIdChoiceProvider implements FormChoiceProviderInterface, FormChoiceAttributeProviderInterface, ConfigurableFormChoiceProviderInterface
{
    /**
     * @var int language ID
     */
    private $langId;

    /**
     * @var OrderStateDataProviderInterface
     */
    private $orderStateDataProvider;

    /**
     * @var ColorBrightnessCalculator
     */
    private $colorBrightnessCalculator;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @param int $langId language ID
     * @param OrderStateDataProviderInterface $orderStateDataProvider
     * @param ColorBrightnessCalculator $colorBrightnessCalculator
     * @param TranslatorInterface $translator
     */
    public function __construct(
        $langId,
        OrderStateDataProviderInterface $orderStateDataProvider,
        ColorBrightnessCalculator $colorBrightnessCalculator,
        TranslatorInterface $translator
    ) {
        $this->langId = $langId;
        $this->orderStateDataProvider = $orderStateDataProvider;
        $this->colorBrightnessCalculator = $colorBrightnessCalculator;
        $this->translator = $translator;
    }

    /**
     * Get order state choices.
     *
     * @param array $options
     *
     * @return array
     */
    public function getChoices(array $options = [])
    {
        $orderStates = $this->orderStateDataProvider->getOrderStates($this->langId);

        // Filters on non-deleted order state
        // or deleted & active order state
        $orderStates = array_filter($orderStates, function (array $item) use ($options) {
            if ($item['deleted'] == 1) {
                if (!empty($options['current_state']) && $options['current_state'] != $item['id_order_state']) {
                    return false;
                }
            }

            return true;
        });

        // Modify name for deleted order states
        $orderStates = $this->updateOrderStatesNames($orderStates);

        return FormChoiceFormatter::formatFormChoices(
            $orderStates,
            'id_order_state',
            'name'
        );
    }

    /**
     * Get order state choices attributes.
     *
     * @return array
     */
    public function getChoicesAttributes()
    {
        $orderStates = $this->orderStateDataProvider->getOrderStates($this->langId);
        $orderStates = $this->updateOrderStatesNames($orderStates);
        $attrs = [];

        foreach ($orderStates as $orderState) {
            $attrs[$orderState['name']]['data-background-color'] = $orderState['color'];
            $attrs[$orderState['name']]['data-is-bright'] = $this->colorBrightnessCalculator->isBright($orderState['color']);
        }

        return $attrs;
    }

    /**
     * Update name for deleted order states
     *
     * @return array
     */
    protected function updateOrderStatesNames(array $orderStates): array
    {
        return array_map(function (array $item) {
            $item['name'] .= $item['deleted'] == 1 ? ' ' . $this->translator->trans('(deleted)', [], 'Admin.Global') : '';

            return $item;
        }, $orderStates);
    }
}
