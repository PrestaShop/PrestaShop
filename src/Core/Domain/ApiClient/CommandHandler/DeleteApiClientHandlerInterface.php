<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\ApiClient\CommandHandler;

use PrestaShop\PrestaShop\Core\Domain\ApiClient\Command\DeleteApiClientCommand;

interface DeleteApiClientHandlerInterface
{
    public function handle(DeleteApiClientCommand $command): void;
}
