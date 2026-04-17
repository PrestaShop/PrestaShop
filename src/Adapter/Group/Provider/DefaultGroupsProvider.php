<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Group\Provider;

use Group;
use PrestaShop\PrestaShop\Core\ConfigurationInterface;
use PrestaShop\PrestaShop\Core\Group\Provider\DefaultGroup;
use PrestaShop\PrestaShop\Core\Group\Provider\DefaultGroups;
use PrestaShop\PrestaShop\Core\Group\Provider\DefaultGroupsProviderInterface;

/**
 * Provides default customer groups
 *
 * @internal
 */
final class DefaultGroupsProvider implements DefaultGroupsProviderInterface
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
    public function __construct(ConfigurationInterface $configuration, $contextLangId)
    {
        $this->configuration = $configuration;
        $this->contextLangId = $contextLangId;
    }

    /**
     * {@inheritdoc}
     */
    public function getGroups()
    {
        $visitorsGroup = new Group($this->configuration->get('PS_UNIDENTIFIED_GROUP'));
        $guestsGroup = new Group($this->configuration->get('PS_GUEST_GROUP'));
        $customersGroup = new Group($this->configuration->get('PS_CUSTOMER_GROUP'));

        $visitorName = isset($visitorsGroup->name[$this->contextLangId]) ?
            $visitorsGroup->name[$this->contextLangId] :
            reset($visitorsGroup->name)
        ;
        $visitorsGroupDto = new DefaultGroup((int) $visitorsGroup->id, $visitorName);

        $groupsName = isset($guestsGroup->name[$this->contextLangId]) ?
            $guestsGroup->name[$this->contextLangId] :
            $guestsGroup->name
        ;
        $guestsGroupDto = new DefaultGroup((int) $guestsGroup->id, $groupsName);

        $customersName = isset($customersGroup->name[$this->contextLangId]) ?
            $customersGroup->name[$this->contextLangId] :
            reset($customersGroup->name)
        ;
        $customersGroupDto = new DefaultGroup((int) $customersGroup->id, $customersName);

        return new DefaultGroups(
            $visitorsGroupDto,
            $guestsGroupDto,
            $customersGroupDto
        );
    }
}
