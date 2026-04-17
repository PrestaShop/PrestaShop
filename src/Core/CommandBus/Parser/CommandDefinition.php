<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\CommandBus\Parser;

/**
 * Transfers commands and queries definition data
 */
class CommandDefinition
{
    /**
     * @var string
     */
    private $className;

    /**
     * @var string
     */
    private $commandType;

    /**
     * @var string
     */
    private $description;

    /**
     * @param string $className
     * @param string $commandType
     * @param string $description
     */
    public function __construct($className, $commandType, $description)
    {
        $this->className = $className;
        $this->commandType = $commandType;
        $this->description = $description;
    }

    /**
     * @return string
     */
    public function getClassName()
    {
        return $this->className;
    }

    /**
     * @return string
     */
    public function getCommandType()
    {
        return $this->commandType;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }
}
