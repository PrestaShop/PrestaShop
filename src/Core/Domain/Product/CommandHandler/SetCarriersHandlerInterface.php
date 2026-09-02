<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Product\CommandHandler;

use PrestaShop\PrestaShop\Core\Domain\Product\Command\SetCarriersCommand;

/**
 * Defines contract for SetCarriersHandler
 */
interface SetCarriersHandlerInterface
{
    public function handle(SetCarriersCommand $command): void;
}
