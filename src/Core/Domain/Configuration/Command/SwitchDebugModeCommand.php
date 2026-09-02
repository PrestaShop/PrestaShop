<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Configuration\Command;

/**
 * Switches debug mode on/off
 */
class SwitchDebugModeCommand
{
    /**
     * @var bool
     */
    private $enableDebugMode;

    /**
     * @param bool $enableDebugMode
     */
    public function __construct($enableDebugMode)
    {
        $this->enableDebugMode = $enableDebugMode;
    }

    /**
     * @return bool
     */
    public function enableDebugMode()
    {
        return true === $this->enableDebugMode;
    }
}
