<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Tag\CommandHandler;

use PrestaShop\PrestaShop\Core\Domain\Tag\Command\DeleteTagCommand;

/**
 * Defines contract for DeleteTagHandler
 */
interface DeleteTagHandlerInterface
{
    /**
     * @param DeleteTagCommand $command
     */
    public function handle(DeleteTagCommand $command): void;
}
