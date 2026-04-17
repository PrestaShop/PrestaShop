<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Profile\CommandHandler;

use PrestaShop\PrestaShop\Core\Domain\Profile\Command\DeleteProfileCommand;

/**
 * Interface DeleteProfileHandlerInterface defines profile deletion handler.
 */
interface DeleteProfileHandlerInterface
{
    /**
     * Delete profile.
     *
     * @param DeleteProfileCommand $command
     */
    public function handle(DeleteProfileCommand $command);
}
