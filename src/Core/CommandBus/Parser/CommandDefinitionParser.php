<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\CommandBus\Parser;

use ReflectionClass;
use ReflectionException;

/**
 * Parses commands and queries definitions
 */
final class CommandDefinitionParser
{
    /**
     * @param string $commandName
     *
     * @return CommandDefinition
     *
     * @throws ReflectionException
     */
    public function parseDefinition($commandName)
    {
        return new CommandDefinition(
            $commandName,
            $this->parseType($commandName),
            $this->parseDescription($commandName)
        );
    }

    /**
     * Checks whether the command is of type Query or Command by provided name
     *
     * @param string $commandName
     *
     * @return string
     */
    private function parseType($commandName)
    {
        if (strpos($commandName, '\Command\\')) {
            return 'Command';
        }

        return 'Query';
    }

    /**
     * @param string $commandName
     *
     * @return string
     *
     * @throws ReflectionException
     */
    private function parseDescription($commandName)
    {
        /**
         * Removes comment symbols, annotations, and line breaks.
         */
        $commandName = preg_replace("/\/+\*\*|\*+\/|\*|@(\w+)\b(.*)|\n/",
            '',
            (new ReflectionClass($commandName))->getDocComment()
        );

        /**
         * Replaces multiple spaces to single space
         */
        $commandName = preg_replace('/ +/', ' ', $commandName);

        /*
         * Strips whitespace from the beginning and end
         */
        return trim($commandName);
    }
}
