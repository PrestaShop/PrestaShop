<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Title\CommandHandler;

use PrestaShop\PrestaShop\Adapter\Title\AbstractTitleHandler;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\Title\Command\BulkDeleteTitleCommand;
use PrestaShop\PrestaShop\Core\Domain\Title\CommandHandler\BulkDeleteTitleHandlerInterface;

/**
 * Handles command that bulk delete titles
 */
#[AsCommandHandler]
class BulkDeleteTitleHandler extends AbstractTitleHandler implements BulkDeleteTitleHandlerInterface
{
    /**
     * {@inheritdoc}
     */
    public function handle(BulkDeleteTitleCommand $command): void
    {
        foreach ($command->getTitleIds() as $titleId) {
            $this->titleRepository->delete(
                $this->titleRepository->get($titleId)
            );
        }
    }
}
