<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Form\ChoiceProvider;

use PrestaShop\PrestaShop\Core\Form\FormChoiceProviderInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Provides address type choices
 */
final class TaxAddressTypeChoiceProvider implements FormChoiceProviderInterface
{
    /**
     * @var TranslatorInterface
     */
    private $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    /**
     * Get choices.
     *
     * @return array
     */
    public function getChoices()
    {
        return [
            $this->translator->trans('Invoice address', [], 'Admin.International.Feature') => 'id_address_invoice',
            $this->translator->trans('Delivery address', [], 'Admin.International.Feature') => 'id_address_delivery',
        ];
    }
}
