<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Form\ChoiceProvider;

use PrestaShop\PrestaShop\Adapter\Group\GroupDataProvider;
use PrestaShop\PrestaShop\Core\Form\FormChoiceFormatter;
use PrestaShop\PrestaShop\Core\Form\FormChoiceProviderInterface;

/**
 * Class GroupByIdChoiceProvider is responsible for providing customer group choices with Id values.
 */
final class GroupByIdChoiceProvider implements FormChoiceProviderInterface
{
    /**
     * @var GroupDataProvider
     */
    private $groupDataProvider;

    /**
     * @var int
     */
    private $langId;

    /**
     * @param GroupDataProvider $groupDataProvider
     * @param int $langId
     */
    public function __construct(
        GroupDataProvider $groupDataProvider,
        $langId
    ) {
        $this->groupDataProvider = $groupDataProvider;
        $this->langId = $langId;
    }

    /**
     * {@inheritdoc}
     */
    public function getChoices()
    {
        return FormChoiceFormatter::formatFormChoices(
            $this->groupDataProvider->getGroups($this->langId),
            'id_group',
            'name',
            false
        );
    }
}
