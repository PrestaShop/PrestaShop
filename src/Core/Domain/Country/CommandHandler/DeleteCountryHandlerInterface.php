<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Country\CommandHandler;

use PrestaShop\PrestaShop\Core\Domain\Country\Command\DeleteCountryCommand;

/**
 * Interface for service that deletes country
 */
interface DeleteCountryHandlerInterface
{
    /**
     * @param DeleteCountryCommand $command
     */
    public function handle(DeleteCountryCommand $command): void;
}
