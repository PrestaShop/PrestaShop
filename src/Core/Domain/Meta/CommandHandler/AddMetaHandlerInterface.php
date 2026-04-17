<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Meta\CommandHandler;

use PrestaShop\PrestaShop\Core\Domain\Meta\Command\AddMetaCommand;
use PrestaShop\PrestaShop\Core\Domain\Meta\ValueObject\MetaId;

/**
 * Interface AddMetaHandlerInterface defines contract for AddMetaHandler.
 */
interface AddMetaHandlerInterface
{
    /**
     * Used to handle the logic required for adding meta data.
     *
     * @param AddMetaCommand $command
     *
     * @return MetaId
     */
    public function handle(AddMetaCommand $command);
}
