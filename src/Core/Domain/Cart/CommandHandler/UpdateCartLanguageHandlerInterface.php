<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Cart\CommandHandler;

use PrestaShop\PrestaShop\Core\Domain\Cart\Command\UpdateCartLanguageCommand;

/**
 * Interface for service that updates cart language
 */
interface UpdateCartLanguageHandlerInterface
{
    /**
     * @param UpdateCartLanguageCommand $command
     */
    public function handle(UpdateCartLanguageCommand $command): void;
}
