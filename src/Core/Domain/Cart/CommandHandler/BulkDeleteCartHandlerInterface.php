<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Cart\CommandHandler;

use PrestaShop\PrestaShop\Core\Domain\Cart\Command\BulkDeleteCartCommand;
use PrestaShop\PrestaShop\Core\Domain\Cart\Exception\CartException;

/**
 * Defines contract for bulk delete cart handler
 */
interface BulkDeleteCartHandlerInterface
{
    /**
     * @param BulkDeleteCartCommand $command
     *
     * @throw CartException
     */
    public function handle(BulkDeleteCartCommand $command): void;
}
