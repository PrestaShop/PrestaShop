<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Profile\CommandHandler;

use PrestaShop\PrestaShop\Core\Domain\Profile\Command\AddProfileCommand;
use PrestaShop\PrestaShop\Core\Domain\Profile\ValueObject\ProfileId;

/**
 * Interface for service that handles adding new profile
 */
interface AddProfileHandlerInterface
{
    /**
     * @param AddProfileCommand $command
     *
     * @return ProfileId
     */
    public function handle(AddProfileCommand $command);
}
