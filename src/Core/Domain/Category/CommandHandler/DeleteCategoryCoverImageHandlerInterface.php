<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Category\CommandHandler;

use PrestaShop\PrestaShop\Core\Domain\Category\Command\DeleteCategoryCoverImageCommand;

/**
 * Defines contract for service which handles cover image delete command.
 */
interface DeleteCategoryCoverImageHandlerInterface
{
    /**
     * @param DeleteCategoryCoverImageCommand $command
     */
    public function handle(DeleteCategoryCoverImageCommand $command);
}
