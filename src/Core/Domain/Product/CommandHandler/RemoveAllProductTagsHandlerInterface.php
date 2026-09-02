<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Product\CommandHandler;

use PrestaShop\PrestaShop\Core\Domain\Product\Command\RemoveAllProductTagsCommand;

/**
 * Defines contract to handle @see RemoveAllProductTagsCommand
 */
interface RemoveAllProductTagsHandlerInterface
{
    /**
     * @param RemoveAllProductTagsCommand $command
     */
    public function handle(RemoveAllProductTagsCommand $command): void;
}
