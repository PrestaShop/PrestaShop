<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Contact\CommandHandler;

use PrestaShop\PrestaShop\Core\Domain\Contact\Command\AddContactCommand;
use PrestaShop\PrestaShop\Core\Domain\Contact\ValueObject\ContactId;

/**
 * Interface AddContactHandlerInterface defines contract for AddContactHandler.
 */
interface AddContactHandlerInterface
{
    /**
     * @param AddContactCommand $command
     *
     * @return ContactId
     */
    public function handle(AddContactCommand $command);
}
