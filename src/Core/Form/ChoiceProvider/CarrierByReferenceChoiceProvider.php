<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Form\ChoiceProvider;

use PrestaShop\PrestaShop\Adapter\Carrier\CarrierDataProvider;
use PrestaShop\PrestaShop\Adapter\Entity\Carrier;
use PrestaShop\PrestaShop\Core\Form\FormChoiceProviderInterface;

/**
 * Class CarrierByReferenceChoiceProvider is responsible for providing carrier choices with value reference.
 */
final class CarrierByReferenceChoiceProvider implements FormChoiceProviderInterface
{
    /**
     * @var CarrierDataProvider
     */
    private $carrierDataProvider;

    /**
     * @var int
     */
    private $langId;

    /**
     * @param CarrierDataProvider $carrierDataProvider
     * @param int $langId
     */
    public function __construct(CarrierDataProvider $carrierDataProvider, $langId)
    {
        $this->carrierDataProvider = $carrierDataProvider;
        $this->langId = $langId;
    }

    /**
     * {@inheritdoc}
     */
    public function getChoices()
    {
        $choices = [];

        $carriers = $this->carrierDataProvider->getCarriers(
            $this->langId,
            false,
            false,
            false,
            null,
            Carrier::ALL_CARRIERS
        );

        foreach ($carriers as $carrier) {
            $choiceId = $carrier['id_carrier'] . ' - ' . $carrier['name'];
            if (!empty($carrier['delay'])) {
                $choiceId .= ' (' . $carrier['delay'] . ')';
            }

            $choices[$choiceId] = $carrier['id_reference'];
        }

        return $choices;
    }
}
