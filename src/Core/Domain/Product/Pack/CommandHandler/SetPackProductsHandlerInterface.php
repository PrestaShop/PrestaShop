<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Product\Pack\CommandHandler;

use PrestaShop\PrestaShop\Core\Domain\Product\Pack\Command\SetPackProductsCommand;

/**
 * Defines contract to handle @see SetPackProductsCommand
 */
interface SetPackProductsHandlerInterface
{
    /**
     * @param SetPackProductsCommand $command
     */
    public function handle(SetPackProductsCommand $command): void;
}
