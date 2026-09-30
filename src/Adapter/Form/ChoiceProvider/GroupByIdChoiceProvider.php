<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Form\ChoiceProvider;

use PrestaShop\PrestaShop\Adapter\Group\GroupDataProvider;
use PrestaShop\PrestaShop\Core\Form\FormChoiceProviderInterface;

/**
 * Provides choices for customer groups
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
    private $contextLangId;

    /**
     * @param GroupDataProvider $groupDataProvider
     * @param int $contextLangId
     */
    public function __construct(GroupDataProvider $groupDataProvider, int $contextLangId)
    {
        $this->groupDataProvider = $groupDataProvider;
        $this->contextLangId = $contextLangId;
    }

    /**
     * {@inheritdoc}
     */
    public function getChoices(): array
    {
        $choices = [];

        foreach ($this->groupDataProvider->getGroups($this->contextLangId, true) as $group) {
            $choices[$group['name']] = (int) $group['id_group'];
        }

        return $choices;
    }
}
