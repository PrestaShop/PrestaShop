<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Form\ChoiceProvider;

use Group;
use PrestaShop\PrestaShop\Core\ConfigurationInterface;
use PrestaShop\PrestaShop\Core\Form\FormChoiceProviderInterface;

/**
 * Provides choices for customer groups
 */
final class GroupByIdChoiceProvider implements FormChoiceProviderInterface
{
    /**
     * @var ConfigurationInterface
     */
    private $configuration;

    /**
     * @var int
     */
    private $contextLangId;

    /**
     * @param ConfigurationInterface $configuration
     * @param int $contextLangId
     */
    public function __construct(ConfigurationInterface $configuration, int $contextLangId)
    {
        $this->configuration = $configuration;
        $this->contextLangId = $contextLangId;
    }

    /**
     * {@inheritdoc}
     */
    public function getChoices(): array
    {
        $choices = [];
        $groups = Group::getGroups($this->contextLangId, true);

        $groupsToSkip = [
            (int) $this->configuration->get('PS_UNIDENTIFIED_GROUP'),
            (int) $this->configuration->get('PS_GUEST_GROUP'),
        ];

        foreach ($groups as $group) {
            $groupId = $group['id_group'];

            if (in_array($groups, $groupsToSkip)) {
                continue;
            }

            $choices[$group['name']] = (int) $groupId;
        }

        return $choices;
    }
}
