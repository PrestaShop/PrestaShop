<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Country\CommandHandler;

use PrestaShop\PrestaShop\Core\Domain\Country\Command\EditCountryCommand;

/**
 * Defines a contract for EditCountryHandler
 */
interface EditCountryHandlerInterface
{
    public function handle(EditCountryCommand $command): void;
}
