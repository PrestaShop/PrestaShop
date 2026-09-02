<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Alias\CommandHandler;

use PrestaShop\PrestaShop\Core\Domain\Alias\Command\BulkDeleteSearchTermsAliasesCommand;

/**
 * Defines contract to handle @see BulkDeleteSearchTermsAliasesCommand
 */
interface BulkDeleteSearchTermsAliasesHandlerInterface
{
    /**
     * @param BulkDeleteSearchTermsAliasesCommand $command
     */
    public function handle(BulkDeleteSearchTermsAliasesCommand $command): void;
}
