<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Configuration\CommandHandler;

use PrestaShop\PrestaShop\Adapter\Debug\DebugMode;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\Configuration\Command\SwitchDebugModeCommand;
use PrestaShop\PrestaShop\Core\Domain\Configuration\CommandHandler\SwitchDebugModeHandlerInterface;

/**
 * Handles command that switches debug mode
 *
 * @internal
 */
#[AsCommandHandler]
final class SwitchDebugModeHandler implements SwitchDebugModeHandlerInterface
{
    /**
     * @var DebugMode
     */
    private $debugMode;

    /**
     * @param DebugMode $debugMode
     */
    public function __construct(DebugMode $debugMode)
    {
        $this->debugMode = $debugMode;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(SwitchDebugModeCommand $command)
    {
        $isDebugModeEnabled = $this->debugMode->isDebugModeEnabled();

        if (!$isDebugModeEnabled && $command->enableDebugMode()) {
            $this->debugMode->enable();

            return;
        }

        if ($isDebugModeEnabled && !$command->enableDebugMode()) {
            $this->debugMode->disable();
        }
    }
}
