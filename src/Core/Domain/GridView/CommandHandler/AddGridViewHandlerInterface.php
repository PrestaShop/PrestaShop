<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\GridView\CommandHandler;

use PrestaShop\PrestaShop\Core\Domain\GridView\Command\AddGridViewCommand;
use PrestaShop\PrestaShop\Core\Domain\GridView\ValueObject\GridViewId;

interface AddGridViewHandlerInterface
{
    /**
     * @param AddGridViewCommand $command
     *
     * @return GridViewId
     */
    public function handle(AddGridViewCommand $command): GridViewId;
}
