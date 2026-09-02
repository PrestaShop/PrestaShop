<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Form\ChoiceProvider;

use PrestaShop\PrestaShop\Core\Form\FormChoiceFormatter;
use PrestaShop\PrestaShop\Core\Form\FormChoiceProviderInterface;

/**
 * Provides choices of manufacturers with manufacturer name as key and id as value
 */
final class ManufacturerNameByIdChoiceProvider implements FormChoiceProviderInterface
{
    /**
     * @var array
     */
    private $manufacturers;

    public function __construct(array $manufacturers)
    {
        $this->manufacturers = $manufacturers;
    }

    /**
     * Get choices.
     *
     * @return array
     */
    public function getChoices()
    {
        return FormChoiceFormatter::formatFormChoices(
            $this->manufacturers,
            'id_manufacturer',
            'name'
        );
    }
}
