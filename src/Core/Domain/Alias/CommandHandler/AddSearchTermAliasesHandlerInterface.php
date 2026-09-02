<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Alias\CommandHandler;

use PrestaShop\PrestaShop\Core\Domain\Alias\Command\AddSearchTermAliasesCommand;
use PrestaShop\PrestaShop\Core\Domain\Alias\ValueObject\AliasId;
use PrestaShop\PrestaShop\Core\Exception\CoreException;

/**
 * Interface for services that handle command which adds new alias
 */
interface AddSearchTermAliasesHandlerInterface
{
    /**
     * @param AddSearchTermAliasesCommand $command
     *
     * @return AliasId[]
     *
     * @throws CoreException
     */
    public function handle(AddSearchTermAliasesCommand $command): array;
}
