<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Meta\CommandHandler;

use PrestaShop\PrestaShop\Core\Domain\Meta\Command\EditMetaCommand;

/**
 * Interface EditMetaHandlerInterface defines contract for EditMetaHandler.
 */
interface EditMetaHandlerInterface
{
    /**
     * Handles meta entity editing.
     *
     * @param EditMetaCommand $command
     */
    public function handle(EditMetaCommand $command);
}
