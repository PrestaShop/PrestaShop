<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */
declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Contact\CommandHandler;

use PrestaShop\PrestaShop\Adapter\Contact\Repository\ContactRepository;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\AbstractBulkCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\Contact\Command\BulkDeleteContactCommand;
use PrestaShop\PrestaShop\Core\Domain\Contact\CommandHandler\BulkDeleteContactHandlerInterface;
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
