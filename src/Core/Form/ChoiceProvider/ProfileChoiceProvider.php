<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Form\ChoiceProvider;

use PrestaShop\PrestaShop\Core\Form\FormChoiceFormatter;
use PrestaShop\PrestaShop\Core\Form\FormChoiceProviderInterface;

/**
 * Class ProfileChoiceProvider provides employee profile choices.
 */
final class ProfileChoiceProvider implements FormChoiceProviderInterface
{
    /**
     * @var array
     */
    private $profiles;

    /**
     * @param array $profiles
     */
    public function __construct(array $profiles)
    {
        $this->profiles = $profiles;
    }

    /**
     * {@inheritdoc}
     */
    public function getChoices()
    {
        return FormChoiceFormatter::formatFormChoices(
            $this->profiles,
            'id_profile',
            'name'
        );
    }
}
