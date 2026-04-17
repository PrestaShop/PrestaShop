<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Title\CommandHandler;

use PrestaShop\PrestaShop\Adapter\Title\AbstractTitleHandler;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\Title\Command\DeleteTitleCommand;
use PrestaShop\PrestaShop\Core\Domain\Title\CommandHandler\DeleteTitleHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Title\Exception\CannotDeleteTitleException;

/**
 * Handles command that delete title
 */
#[AsCommandHandler]
class DeleteTitleHandler extends AbstractTitleHandler implements DeleteTitleHandlerInterface
{
    /**
     * {@inheritdoc}
     *
     * @throws CannotDeleteTitleException
     */
    public function handle(DeleteTitleCommand $command): void
    {
        $this->titleRepository->delete(
            $this->titleRepository->get($command->getTitleId())
        );
    }
}
