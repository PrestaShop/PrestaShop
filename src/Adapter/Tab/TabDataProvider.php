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

namespace PrestaShop\PrestaShop\Adapter\Tab;

use PrestaShop\PrestaShop\Adapter\LegacyContext;
use PrestaShop\PrestaShop\Core\ConfigurationInterface;
use Profile;
use Tab;

/**
 * Class TabDataProvider provides Tabs data using legacy logic.
 */
class TabDataProvider
{
    /**
     * @var ConfigurationInterface
     */
    private $legacyConfiguration;

    /**
     * @var LegacyContext
     */
    private $legacyContext;

    /**
     * @param LegacyContext $legacyContext
     * @param ConfigurationInterface $legacyConfiguration
     */
    public function __construct(LegacyContext $legacyContext, ConfigurationInterface $legacyConfiguration)
    {
        $this->legacyContext = $legacyContext;
        $this->legacyConfiguration = $legacyConfiguration;
    }

    /**
     * Gets viewable tabs for current context employee.
     *
     * @param int $languageId
     *
     * @return array
     */
    public function getViewableTabsForContextEmployee($languageId)
    {
        if ($this->legacyContext->getContext()->employee) {
            return $this->getViewableTabs(
                $this->legacyContext->getContext()->employee->id_profile,
                $languageId
            );
        }

        return [];
    }

    /**
     * Gets tabs that given employee profile can view.
     *
     * @param int $profileId
     * @param int $languageId
     *
     * @return array
     */
    public function getViewableTabs($profileId, $languageId)
    {
        $viewableTabs = [];

        foreach (Tab::getTabs($languageId, 0) as $tab) {
            if ($this->canAccessTab($profileId, $tab['id_tab'])) {
                $viewableTabs[$tab['id_tab']] = [
                    'id_tab' => $tab['id_tab'],
                    'name' => $tab['name'],
                    'children' => [],
                ];

                foreach (Tab::getTabs($languageId, $tab['id_tab']) as $children) {
                    if ($this->canAccessTab($profileId, $children['id_tab'])) {
                        foreach (Tab::getTabs($languageId, $children['id_tab']) as $subchild) {
                            if ($this->canAccessTab($profileId, $subchild['id_tab'])) {
                                $viewableTabs[$tab['id_tab']]['children'][] = [
                                    'id_tab' => $subchild['id_tab'],
                                    'name' => $subchild['name'],
                                ];
                            }
                        }
                    }
                }
            }
        }

        return $viewableTabs;
    }

    /**
     * Check if given profile can access a tab.
     *
     * @param int $profileId
     * @param int $tabId
     * @param string $accessLevel view, add, edit or delete
     *
     * @return bool
     */
    private function canAccessTab($profileId, $tabId, $accessLevel = 'view')
    {
        if (!in_array($accessLevel, ['view', 'add', 'edit', 'delete'])) {
            return false;
        }

        if ($profileId === (int) $this->legacyConfiguration->get('_PS_ADMIN_PROFILE_')) {
            return true;
        }

        $tabAccess = Profile::getProfileAccesses($profileId);

        if (isset($tabAccess[$tabId][$accessLevel])) {
            return $tabAccess[$tabId][$accessLevel] === '1';
        }

        return false;
    }

    /**
     * Reset static tab cache
     *
     * @return void
     */
    public function resetTabCache(): void
    {
        Tab::resetTabCache();
    }
}
