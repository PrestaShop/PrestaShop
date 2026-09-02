<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
