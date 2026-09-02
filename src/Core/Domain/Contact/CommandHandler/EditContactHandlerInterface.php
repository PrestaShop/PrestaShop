<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Contact\CommandHandler;

use PrestaShop\PrestaShop\Core\Domain\Contact\Command\EditContactCommand;

/**
 * Interface EditContactHandlerInterface defines contract for EditContactHandler.
 */
interface EditContactHandlerInterface
{
    /**
     * @param EditContactCommand $command
     */
    public function handle(EditContactCommand $command);
}
