<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
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
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
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
    public function __construct(ConfigurationInterface $configuration, $contextLangId)
    {
        $this->configuration = $configuration;
        $this->contextLangId = $contextLangId;
    }

    /**
     * {@inheritdoc}
     */
    public function getChoices()
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
