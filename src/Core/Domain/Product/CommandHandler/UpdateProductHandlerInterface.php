<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Product\CommandHandler;

use PrestaShop\PrestaShop\Core\Domain\Product\Command\UpdateProductCommand;

/**
 * Defines contract to handle @see UpdateProductCommand
 */
interface UpdateProductHandlerInterface
{
    /**
     * @param UpdateProductCommand $command
     */
    public function handle(UpdateProductCommand $command): void;
}
