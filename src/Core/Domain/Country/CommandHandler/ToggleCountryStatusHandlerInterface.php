<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Country\CommandHandler;

use PrestaShop\PrestaShop\Core\Domain\Country\Command\ToggleCountryStatusCommand;

interface ToggleCountryStatusHandlerInterface
{
    public function handle(ToggleCountryStatusCommand $command): void;
}
