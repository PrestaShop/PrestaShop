<?php
/**
 * 2007-2019 PrestaShop and Contributors
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Adapter\Group\Provider;

use Group;
use PrestaShop\PrestaShop\Core\ConfigurationInterface;
use PrestaShop\PrestaShop\Core\Group\Provider\DefaultGroup;
use PrestaShop\PrestaShop\Core\Group\Provider\DefaultGroupsProviderInterface;
use PrestaShop\PrestaShop\Core\Group\Provider\DefaultGroups;

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
