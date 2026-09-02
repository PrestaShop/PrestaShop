<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Zone\CommandHandler;

use PrestaShop\PrestaShop\Core\Domain\Zone\Command\AddZoneCommand;
use PrestaShop\PrestaShop\Core\Domain\Zone\ValueObject\ZoneId;

/**
 * Interface for service that creates new zone
 */
interface AddZoneHandlerInterface
{
    /**
     * @param AddZoneCommand $command
     *
     * @return ZoneId
     */
    public function handle(AddZoneCommand $command): ZoneId;
}
