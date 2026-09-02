<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Discount\CommandHandler;

use PrestaShop\PrestaShop\Core\Domain\Discount\Command\AddDiscountCommand;
use PrestaShop\PrestaShop\Core\Domain\Discount\ValueObject\DiscountId;

interface AddDiscountHandlerInterface
{
    public function handle(AddDiscountCommand $command): DiscountId;
}
