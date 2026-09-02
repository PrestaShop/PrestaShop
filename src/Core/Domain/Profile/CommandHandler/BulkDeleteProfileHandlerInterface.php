<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Profile\CommandHandler;

use PrestaShop\PrestaShop\Core\Domain\Profile\Command\BulkDeleteProfileCommand;

/**
 * Interface BulkDeleteProfileHandlerInterface defines profile bulk deletion handler.
 */
interface BulkDeleteProfileHandlerInterface
{
    /**
     * Delete multiple profiles.
     *
     * @param BulkDeleteProfileCommand $command
     */
    public function handle(BulkDeleteProfileCommand $command);
}
