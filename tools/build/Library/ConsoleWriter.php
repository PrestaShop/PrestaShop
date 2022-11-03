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
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

/**
 * Use to display text in color on the terminal.
 */
class ConsoleWriter
{
    /** @var string */
    const COLOR_BLACK = 'black';

    /** @var string */
    const COLOR_DARK_GRAY = 'dark_gray';

    /** @var string */
    const COLOR_BLUE = 'blue';

    /** @var string */
    const COLOR_LIGHT_BLUE = 'light_blue';

    /** @var string */
    const COLOR_GREEN = 'green';

    /** @var string */
    const COLOR_LIGHT_GREEN = 'light_green';

    /** @var string */
    const COLOR_CYAN = 'cyan';

    /** @var string */
    const COLOR_LIGHT_CYAN = 'light_cyan';

    /** @var string */
    const COLOR_RED = 'red';

    /** @var string */
    const COLOR_LIGHT_RED = 'light_red';

    /** @var string */
    const COLOR_PURPLE = 'purple';

    /** @var string */
    const COLOR_LIGHT_PURPLE = 'light_purple';

    /** @var string */
    const COLOR_BROWN = 'brown';

    /** @var string */
    const COLOR_YELLOW = 'yellow';

    /** @var string */
    const COLOR_LIGHT_GRAY = 'light_gray';

    /** @var string */
    const COLOR_WHITE = 'white';

    /**
     * Display a colored text on the terminal.
     *
     * @param string $text
     * @param string $color
     *
     * @return $this
     *
     * @throws BuildException
     */
    public function displayText($text, $color = self::COLOR_WHITE)
    {
        $cliColors = array(
            self::COLOR_BLACK => '0;30',
            self::COLOR_DARK_GRAY => '1;30',
            self::COLOR_BLUE => '0;34',
            self::COLOR_LIGHT_BLUE => '1;34',
            self::COLOR_GREEN => '0;32',
            self::COLOR_LIGHT_GREEN => '1;32',
            self::COLOR_CYAN => '0;36',
            self::COLOR_LIGHT_CYAN => '1;36',
            self::COLOR_RED => '0;31',
            self::COLOR_LIGHT_RED => '1;31',
            self::COLOR_PURPLE => '0;35',
            self::COLOR_LIGHT_PURPLE => '1;35',
            self::COLOR_BROWN => '0;33',
            self::COLOR_YELLOW => '1;33',
            self::COLOR_LIGHT_GRAY => '0;37',
            self::COLOR_WHITE => '1;37',
        );

        if (empty($cliColors[$color])) {
            throw new BuildException('CLI color does not exist');
        }
        echo "\e[{$cliColors[$color]}m$text\e[0m";

        return $this;
    }
}
