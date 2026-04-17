<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\ImageSettings\CommandHandler;

use PrestaShop\PrestaShop\Core\Domain\ImageSettings\Command\DeleteImageTypeCommand;

/**
 * Defines contract for DeleteImageTypeHandler
 */
interface DeleteImageTypeHandlerInterface
{
    /**
     * @param DeleteImageTypeCommand $command
     */
    public function handle(DeleteImageTypeCommand $command): void;
}
