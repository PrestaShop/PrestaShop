<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Order\CommandHandler;

use PrestaShop\PrestaShop\Core\Domain\Order\Command\SetInternalOrderNoteCommand;

/**
 * Defines interface for service that handles command which sets internal order note
 */
interface SetInternalOrderNoteHandlerInterface
{
    /**
     * @param SetInternalOrderNoteCommand $command
     */
    public function handle(SetInternalOrderNoteCommand $command);
}
