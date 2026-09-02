<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Form\ChoiceProvider;

use PrestaShop\PrestaShop\Core\Form\FormChoiceFormatter;
use PrestaShop\PrestaShop\Core\Form\FormChoiceProviderInterface;

/**
 * Class ImportMatchConfigurationChoiceProvider is responsible for providing choices
 * in Advanced parameters -> Import -> Step 2 -> Load a data matching configuration.
 */
final class ImportMatchConfigurationChoiceProvider implements FormChoiceProviderInterface
{
    /**
     * @var array
     */
    private $matchConfigurations;

    /**
     * @param array $matchConfigurations
     */
    public function __construct(array $matchConfigurations)
    {
        $this->matchConfigurations = $matchConfigurations;
    }

    /**
     * {@inheritdoc}
     */
    public function getChoices()
    {
        return FormChoiceFormatter::formatFormChoices(
            $this->matchConfigurations,
            'id_import_match',
            'name'
        );
    }
}
