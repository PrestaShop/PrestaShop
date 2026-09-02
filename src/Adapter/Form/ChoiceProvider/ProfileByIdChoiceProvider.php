<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Form\ChoiceProvider;

use PrestaShop\PrestaShop\Core\Form\FormChoiceFormatter;
use PrestaShop\PrestaShop\Core\Form\FormChoiceProviderInterface;
use Profile;

/**
 * Class ProfileByIdChoiceProvider provides employee profile choices with name as label and profile id as value.
 */
final class ProfileByIdChoiceProvider implements FormChoiceProviderInterface
{
    /**
     * @var int
     */
    private $contextLangId;

    /**
     * @param int $contextLangId
     */
    public function __construct($contextLangId)
    {
        $this->contextLangId = $contextLangId;
    }

    /**
     * {@inheritdoc}
     */
    public function getChoices()
    {
        return FormChoiceFormatter::formatFormChoices(
            Profile::getProfiles($this->contextLangId),
            'id_profile',
            'name',
            false
        );
    }
}
