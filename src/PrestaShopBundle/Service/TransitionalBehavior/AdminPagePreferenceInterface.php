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

namespace PrestaShopBundle\Service\TransitionalBehavior;

/**
 * Contract to know which page's version to display.
 *
 * This interface gives methods to use to take decision:
 * - if we should display the new refactored page, or the old legacy one.
 * - if we should display the switch on the admin layout to change this setting.
 */
interface AdminPagePreferenceInterface
{
    /**
     * Use it to know if we need to redirect to legacy Controllers or not.
     *
     * @param string $page the page to look for
     *
     * @return bool true to redirect to legacy
     */
    public function getTemporaryShouldUseLegacyPage($page);

    /**
     * Set the temporary behavior of the new/old page on Admin interface.
     *
     * @param string $page the page to look for
     * @param bool $useLegacy true to redirect to old legacy pages for Product controller
     */
    public function setTemporaryShouldUseLegacyPage($page, $useLegacy);

    /**
     * Use it to know if we need to display the 'switch to legacy page' button or not.
     * In debug mode, always shown.
     *
     * @param string $page the page to look for
     *
     * @return bool true to show the switch to legacy page button
     */
    public function getTemporaryShouldAllowUseLegacyPage($page = null);
}
