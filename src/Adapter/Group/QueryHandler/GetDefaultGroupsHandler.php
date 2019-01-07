<?php
/**
 * 2007-2018 PrestaShop.
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
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Adapter\Group\QueryHandler;

use Group;
use PrestaShop\PrestaShop\Core\ConfigurationInterface;
use PrestaShop\PrestaShop\Core\Domain\Group\DataTransferObject\DefaultGroup;
use PrestaShop\PrestaShop\Core\Domain\Group\DefaultGroups;
use PrestaShop\PrestaShop\Core\Domain\Group\Query\GetDefaultGroups;
use PrestaShop\PrestaShop\Core\Domain\Group\QueryHandler\GetDefaultGroupsHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Group\ValueObject\GroupId;

/**
 * Class GetDefaultGroupsHandler.
 *
 * @internal
 */
final class GetDefaultGroupsHandler implements GetDefaultGroupsHandlerInterface
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
    public function handle(GetDefaultGroups $query)
    {
        $visitorsGroup = new Group($this->configuration->get('PS_UNIDENTIFIED_GROUP'));
        $guestsGroup = new Group($this->configuration->get('PS_GUEST_GROUP'));
        $customersGroup = new Group($this->configuration->get('PS_CUSTOMER_GROUP'));

        $visitorsGroupDto = new DefaultGroup(
            new GroupId($visitorsGroup->id),
            $visitorsGroup->name[$this->contextLangId]
        );

        $guestsGroupDto = new DefaultGroup(
            new GroupId($guestsGroup->id),
            $guestsGroup->name[$this->contextLangId]
        );

        $customersGroupDto = new DefaultGroup(
            new GroupId($customersGroup->id),
            $customersGroup->name[$this->contextLangId]
        );

        return new DefaultGroups(
            $visitorsGroupDto,
            $guestsGroupDto,
            $customersGroupDto
        );
    }
}
