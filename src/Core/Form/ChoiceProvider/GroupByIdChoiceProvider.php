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
     * @var bool whether choices must be restricted to the groups of the current shop context
     */
    private $filterByShop;

    /**
     * @param GroupDataProvider $groupDataProvider
     * @param int $langId
     * @param bool $filterByShop
     */
    public function __construct(
        GroupDataProvider $groupDataProvider,
        $langId,
        bool $filterByShop = false
    ) {
        $this->groupDataProvider = $groupDataProvider;
        $this->langId = $langId;
        $this->filterByShop = $filterByShop;
    }

    /**
     * {@inheritdoc}
     */
    public function getChoices()
    {
        return FormChoiceFormatter::formatFormChoices(
            $this->groupDataProvider->getGroups($this->langId, $this->filterByShop),
            'id_group',
            'name',
            false
        );
    }
}
