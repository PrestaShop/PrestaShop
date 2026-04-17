<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Webservice\CommandHandler;

use PrestaShop\PrestaShop\Core\Domain\Webservice\Command\DeleteWebserviceKeyCommand;

/**
 * Defines contract for DeleteWebserviceHandler
 */
interface DeleteWebserviceKeyHandlerInterface
{
    /**
     * @param DeleteWebserviceKeyCommand $command
     */
    public function handle(DeleteWebserviceKeyCommand $command): void;
}
