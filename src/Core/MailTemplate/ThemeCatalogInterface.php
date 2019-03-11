<?php
/**
 * 2007-2019 PrestaShop SA and Contributors
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
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Core\MailTemplate;

use PrestaShop\PrestaShop\Core\Exception\InvalidArgumentException;

/**
 * Interface ThemeCatalogInterface is used to list the available themes to generate
 * mail templates, each one containing its own layouts. Layouts are divided in two
 * categories "core" and "modules" layouts. You can change the themes collection or
 * modify a theme's layout collection via the hook:
 *  ThemeCatalogInterface::LIST_MAIL_THEMES_HOOK = actionListMailThemes
 */
interface ThemeCatalogInterface
{
    const LIST_MAIL_THEMES_HOOK = 'actionListMailThemes';

    /**
     * Returns the list of existing themes.
     *
     * @return ThemeCollectionInterface
     */
    public function listThemes();

    /**
     * @param string $theme
     *
     * @throws InvalidArgumentException
     *
     * @return ThemeInterface
     */
    public function getByName($theme);
}
