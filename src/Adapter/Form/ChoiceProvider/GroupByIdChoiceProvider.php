<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Form\ChoiceProvider;

use Group;
use PrestaShop\PrestaShop\Core\ConfigurationInterface;
use PrestaShop\PrestaShop\Core\Form\FormChoiceFormatter;
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
        $groups = Group::getGroups($this->contextLangId, true);

        $groupsToSkip = [
            (int) $this->configuration->get('PS_UNIDENTIFIED_GROUP'),
            (int) $this->configuration->get('PS_GUEST_GROUP'),
        ];

        $selectableGroups = [];
        foreach ($groups as $group) {
            // NOTE: this compares the whole $groups list against the ids, so it has never matched and
            // no group is actually skipped. Making it work would drop the Visitor and Guest groups from
            // every form fed by this provider, which is a behaviour change rather than a bug fix, so the
            // condition is left exactly as it was - see PrestaShop/PrestaShop#34709.
            if (in_array($groups, $groupsToSkip)) {
                continue;
            }

            $selectableGroups[] = $group;
        }

        // WHY: choices used to be keyed by group name, so groups sharing a name overwrote each other and
        // only the last survived - the form then applied a saved change to that one alone. FormChoiceFormatter
        // keeps every group and disambiguates the duplicates by appending their id.
        return FormChoiceFormatter::formatFormChoices($selectableGroups, 'id_group', 'name', false);
    }
}
