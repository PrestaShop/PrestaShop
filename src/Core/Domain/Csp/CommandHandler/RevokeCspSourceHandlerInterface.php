<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Csp\CommandHandler;

use PrestaShop\PrestaShop\Core\Domain\Csp\Command\RevokeCspSourceCommand;

interface RevokeCspSourceHandlerInterface
{
    public function handle(RevokeCspSourceCommand $command): void;
}
