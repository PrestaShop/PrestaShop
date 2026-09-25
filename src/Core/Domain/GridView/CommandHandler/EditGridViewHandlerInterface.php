<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\GridView\CommandHandler;

use PrestaShop\PrestaShop\Core\Domain\GridView\Command\EditGridViewCommand;

interface EditGridViewHandlerInterface
{
    /**
     * @param EditGridViewCommand $command
     *
     * @return void
     */
    public function handle(EditGridViewCommand $command): void;
}
