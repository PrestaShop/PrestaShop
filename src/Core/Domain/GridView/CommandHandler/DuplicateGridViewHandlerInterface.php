<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\GridView\CommandHandler;

use PrestaShop\PrestaShop\Core\Domain\GridView\Command\DuplicateGridViewCommand;
use PrestaShop\PrestaShop\Core\Domain\GridView\ValueObject\GridViewId;

interface DuplicateGridViewHandlerInterface
{
    /**
     * @param DuplicateGridViewCommand $command
     *
     * @return GridViewId id of the duplicated view
     */
    public function handle(DuplicateGridViewCommand $command): GridViewId;
}
