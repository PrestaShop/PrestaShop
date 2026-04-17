<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\State\CommandHandler;

use PrestaShop\PrestaShop\Core\Domain\State\Command\AddStateCommand;
use PrestaShop\PrestaShop\Core\Domain\State\ValueObject\StateId;

interface AddStateHandlerInterface
{
    /**
     * @param AddStateCommand $command
     *
     * @return StateId
     */
    public function handle(AddStateCommand $command): StateId;
}
