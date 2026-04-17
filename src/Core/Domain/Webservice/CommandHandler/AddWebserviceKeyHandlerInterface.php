<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Webservice\CommandHandler;

use PrestaShop\PrestaShop\Core\Domain\Webservice\Command\AddWebserviceKeyCommand;
use PrestaShop\PrestaShop\Core\Domain\Webservice\ValueObject\WebserviceKeyId;

/**
 * Interface for service that handles adding new webservice key
 */
interface AddWebserviceKeyHandlerInterface
{
    /**
     * @param AddWebserviceKeyCommand $command
     *
     * @return WebserviceKeyId
     */
    public function handle(AddWebserviceKeyCommand $command);
}
