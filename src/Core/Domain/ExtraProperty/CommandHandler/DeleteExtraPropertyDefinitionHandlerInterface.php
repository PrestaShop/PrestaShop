<?php

/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\ExtraProperty\CommandHandler;

use PrestaShop\PrestaShop\Core\Domain\ExtraProperty\Command\DeleteExtraPropertyDefinitionCommand;

/**
 * Handler contract for DeleteExtraPropertyDefinitionCommand.
 */
interface DeleteExtraPropertyDefinitionHandlerInterface
{
    /**
     * @param DeleteExtraPropertyDefinitionCommand $command
     */
    public function handle(DeleteExtraPropertyDefinitionCommand $command): void;
}
