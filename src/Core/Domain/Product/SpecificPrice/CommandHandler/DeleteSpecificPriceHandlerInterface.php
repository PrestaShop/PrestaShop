<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Product\SpecificPrice\CommandHandler;

use PrestaShop\PrestaShop\Core\Domain\Product\SpecificPrice\Command\DeleteSpecificPriceCommand;

/**
 * Interface for handling DeleteSpecificPriceCommand command
 */
interface DeleteSpecificPriceHandlerInterface
{
    /**
     * @param DeleteSpecificPriceCommand $command
     *
     * @return void
     */
    public function handle(DeleteSpecificPriceCommand $command): void;
}
