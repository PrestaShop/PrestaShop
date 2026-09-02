<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Contact\CommandHandler;

use PrestaShop\PrestaShop\Adapter\Contact\Repository\ContactRepository;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\Contact\Command\DeleteContactCommand;

#[AsCommandHandler]
class DeleteContactHandler implements DeleteContactHandlerInterface
{
    /**
     * @var ContactRepository
     */
    private $contactRepository;

    public function __construct(
        ContactRepository $contactRepository
    ) {
        $this->contactRepository = $contactRepository;
    }

    public function handle(DeleteContactCommand $command): void
    {
        $this->contactRepository->delete($command->getContactId());
    }
}
