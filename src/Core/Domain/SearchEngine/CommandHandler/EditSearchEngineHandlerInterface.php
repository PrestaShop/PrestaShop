<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\SearchEngine\CommandHandler;

use PrestaShop\PrestaShop\Core\Domain\SearchEngine\Command\EditSearchEngineCommand;

/**
 * Defines contract for EditSearchEngineHandler.
 */
interface EditSearchEngineHandlerInterface
{
    /**
     * @param EditSearchEngineCommand $command
     */
    public function handle(EditSearchEngineCommand $command): void;
}
