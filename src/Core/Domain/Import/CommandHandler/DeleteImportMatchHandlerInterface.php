<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Import\CommandHandler;

use PrestaShop\PrestaShop\Core\Domain\Import\Command\DeleteImportMatchCommand;

/**
 * Defines the contract for handling @see DeleteImportMatchCommand.
 */
interface DeleteImportMatchHandlerInterface
{
    public function handle(DeleteImportMatchCommand $command): void;
}
