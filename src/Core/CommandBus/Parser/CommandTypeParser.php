<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\CommandBus\Parser;

final class CommandTypeParser
{
    /**
     * @param string $commandName Fully-qualified class name of command
     *
     * @return string
     */
    public function parse($commandName)
    {
        if (strpos($commandName, '\Command\\')) {
            return 'Command';
        }

        return 'Query';
    }
}
