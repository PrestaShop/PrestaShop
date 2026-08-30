<?php

/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\ExtraProperty\CommandHandler;

use PrestaShop\PrestaShop\Core\Domain\ExtraProperty\Command\BulkDeleteExtraPropertyDefinitionCommand;

/**
 * Handler contract for BulkDeleteExtraPropertyDefinitionCommand.
 */
interface BulkDeleteExtraPropertyDefinitionHandlerInterface
{
    public function handle(BulkDeleteExtraPropertyDefinitionCommand $command): void;
}
