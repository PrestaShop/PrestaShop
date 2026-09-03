<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Form\ChoiceProvider;

use PrestaShop\PrestaShop\Adapter\Carrier\CarrierDataProvider;
use PrestaShop\PrestaShop\Adapter\Entity\Carrier;
use PrestaShop\PrestaShop\Core\Form\FormChoiceProviderInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

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
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @param CarrierDataProvider $carrierDataProvider
     * @param int $langId
     * @param TranslatorInterface $translator
     */
    public function __construct(CarrierDataProvider $carrierDataProvider, $langId, TranslatorInterface $translator)
    {
        $this->carrierDataProvider = $carrierDataProvider;
        $this->langId = $langId;
        $this->translator = $translator;
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
            // Disabled carriers stay selectable on purpose: a carrier is often assigned to products
            // before it is turned on, and disabling one temporarily must not silently detach it from
            // every product. They are labelled instead of hidden so the merchant can tell them apart.
            if (!$carrier['active']) {
                $choiceId .= ' - ' . $this->translator->trans('Disabled', [], 'Admin.Global');
            }

            $choices[$choiceId] = $carrier['id_reference'];
        }

        return $choices;
    }
}
