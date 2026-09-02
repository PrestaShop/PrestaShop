<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Title\CommandHandler;

use PrestaShop\PrestaShop\Core\Domain\Title\Command\AddTitleCommand;
use PrestaShop\PrestaShop\Core\Domain\Title\ValueObject\TitleId;

/**
 * Defines contract for AddTitleHandler
 */
interface AddTitleHandlerInterface
{
    /**
     * @param AddTitleCommand $command
     *
     * @return TitleId
     */
    public function handle(AddTitleCommand $command): TitleId;
}
