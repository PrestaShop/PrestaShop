<?php

/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\ExtraProperty\CommandHandler;

use PrestaShop\PrestaShop\Core\Domain\ExtraProperty\Command\AddExtraPropertyDefinitionCommand;
use PrestaShop\PrestaShop\Core\Domain\ExtraProperty\ValueObject\ExtraPropertyDefinitionId;

/**
 * Handler contract for AddExtraPropertyDefinitionCommand.
 */
interface AddExtraPropertyDefinitionHandlerInterface
{
    /**
     * @param AddExtraPropertyDefinitionCommand $command
     *
     * @return ExtraPropertyDefinitionId
     */
    public function handle(AddExtraPropertyDefinitionCommand $command): ExtraPropertyDefinitionId;
}
