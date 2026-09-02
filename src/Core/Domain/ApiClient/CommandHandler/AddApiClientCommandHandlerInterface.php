<?php

/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\ApiClient\CommandHandler;

use PrestaShop\PrestaShop\Core\Domain\ApiClient\Command\AddApiClientCommand;
use PrestaShop\PrestaShop\Core\Domain\ApiClient\ValueObject\CreatedApiClient;

interface AddApiClientCommandHandlerInterface
{
    public function handle(AddApiClientCommand $command): CreatedApiClient;
}
