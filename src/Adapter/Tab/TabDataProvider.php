<?php
/**
 * 2007-2018 PrestaShop
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

namespace PrestaShop\PrestaShop\Adapter\Tab;

use Tab;

/**
 * Class TabDataProvider provides Tabs data using legacy logic.
 */
class TabDataProvider
{
    /**
     * Gets accessible tabs.
     *
     * @param int $languageId
     *
     * @return array
     */
    public function getAccessibleTabs($languageId)
    {
        $accessibleTabs = [];

        foreach (Tab::getTabs($languageId, 0) as $tab) {
            if (Tab::checkTabRights($tab['id_tab'])) {
                $accessibleTabs[$tab['id_tab']] = $tab;
                foreach (Tab::getTabs($languageId, $tab['id_tab']) as $children) {
                    if (Tab::checkTabRights($children['id_tab'])) {
                        foreach (Tab::getTabs($languageId, $children['id_tab']) as $subchild) {
                            if (Tab::checkTabRights($subchild['id_tab'])) {
                                $accessibleTabs[$tab['id_tab']]['children'][] = $subchild;
                            }
                        }
                    }
                }
            }
        }

        return $accessibleTabs;
    }
}
