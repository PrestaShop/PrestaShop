<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Product\SpecificPrice\CommandHandler;

use PrestaShop\PrestaShop\Core\Domain\Product\SpecificPrice\Command\AddSpecificPriceCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\SpecificPrice\ValueObject\SpecificPriceId;

/**
 * Interface for handling AddProductSpecificPriceCommand command
 */
interface AddSpecificPriceHandlerInterface
{
    /**
     * @param AddSpecificPriceCommand $command
     *
     * @return SpecificPriceId
     */
    public function handle(AddSpecificPriceCommand $command): SpecificPriceId;
}
