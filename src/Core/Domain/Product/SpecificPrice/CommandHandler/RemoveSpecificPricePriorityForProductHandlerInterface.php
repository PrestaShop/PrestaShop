<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Product\SpecificPrice\CommandHandler;

use PrestaShop\PrestaShop\Core\Domain\Product\SpecificPrice\Command\RemoveSpecificPricePriorityForProductCommand;

interface RemoveSpecificPricePriorityForProductHandlerInterface
{
    public function handle(RemoveSpecificPricePriorityForProductCommand $command): void;
}
