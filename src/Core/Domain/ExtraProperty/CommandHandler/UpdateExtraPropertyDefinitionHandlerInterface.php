<?php

/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\ExtraProperty\CommandHandler;

use PrestaShop\PrestaShop\Core\Domain\ExtraProperty\Command\UpdateExtraPropertyDefinitionCommand;

/**
 * Handler contract for UpdateExtraPropertyDefinitionCommand.
 */
interface UpdateExtraPropertyDefinitionHandlerInterface
{
    /**
     * @param UpdateExtraPropertyDefinitionCommand $command
     */
    public function handle(UpdateExtraPropertyDefinitionCommand $command): void;
}
