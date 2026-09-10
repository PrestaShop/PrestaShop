<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Contact\CommandHandler;

use PrestaShop\PrestaShop\Adapter\Contact\Repository\ContactRepository;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\AbstractBulkCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\Contact\Command\BulkDeleteContactCommand;
use PrestaShop\PrestaShop\Core\Domain\Contact\Exception\BulkContactException;
use PrestaShop\PrestaShop\Core\Domain\Contact\Exception\ContactException;
use PrestaShop\PrestaShop\Core\Domain\Contact\ValueObject\ContactId;
use PrestaShop\PrestaShop\Core\Domain\Exception\BulkCommandExceptionInterface;

#[AsCommandHandler]
class BulkDeleteContactHandler extends AbstractBulkCommandHandler implements BulkDeleteContactHandlerInterface
{
    /**
     * @var ContactRepository
     */
    private $contactRepository;

    public function __construct(
        ContactRepository $ContactRepository
    ) {
        $this->contactRepository = $ContactRepository;
    }

    public function handle(BulkDeleteContactCommand $command): void
    {
        $this->handleBulkAction($command->getContactIds(), ContactException::class);
    }

    /**
     * @param ContactId $id
     * @param BulkDeleteContactCommand $command
     *
     * @return void
     */
    protected function handleSingleAction(mixed $id, mixed $command): void
    {
        $this->contactRepository->delete($id);
    }

    /**
     * {@inheritDoc}
     */
    protected function buildBulkException(array $caughtExceptions): BulkCommandExceptionInterface
    {
        return new BulkContactException(
            $caughtExceptions,
            'Errors occurred during Contact bulk delete action',
            BulkContactException::FAILED_BULK_DELETE
        );
    }

    /**
     * {@inheritDoc}
     */
    protected function supports($id): bool
    {
        return $id instanceof ContactId;
    }
}
