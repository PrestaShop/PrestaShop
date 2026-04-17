<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Profile\CommandHandler;

use PrestaShop\PrestaShop\Core\Domain\Profile\Command\EditProfileCommand;

/**
 * Interface for service that edits existing Profile
 */
interface EditProfileHandlerInterface
{
    /**
     * @param EditProfileCommand $command
     */
    public function handle(EditProfileCommand $command);
}
