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

namespace PrestaShop\PrestaShop\Adapter\Discount\CommandHandler;

use PrestaShop\PrestaShop\Adapter\Discount\Repository\DiscountRepository;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\AbstractBulkCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\Discount\Command\BulkDeleteDiscountsCommand;
use PrestaShop\PrestaShop\Core\Domain\Discount\CommandHandler\BulkDeleteDiscountsHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Discount\Exception\BulkDiscountException;
use PrestaShop\PrestaShop\Core\Domain\Discount\Exception\CannotDeleteDiscountException;
use PrestaShop\PrestaShop\Core\Domain\Discount\Exception\DiscountException;
use PrestaShop\PrestaShop\Core\Domain\Discount\ValueObject\DiscountId;
use PrestaShop\PrestaShop\Core\Domain\Exception\BulkCommandExceptionInterface;

#[AsCommandHandler]
final class BulkDeleteDiscountsHandler extends AbstractBulkCommandHandler implements BulkDeleteDiscountsHandlerInterface
{
    public function __construct(
        private readonly DiscountRepository $discountRepository,
    ) {
    }

    /**
     * {@inheritdoc}
     *
     * @throws CannotDeleteDiscountException
     */
    public function handle(BulkDeleteDiscountsCommand $command): void
    {
        $this->handleBulkAction($command->getDiscountIds(), DiscountException::class);
    }

    /**
     * @param DiscountId $id
     * @param BulkDeleteDiscountsCommand $command
     *
     * @return void
     */
    protected function handleSingleAction(mixed $id, mixed $command): void
    {
        $this->discountRepository->delete($id);
    }

    /**
     * {@inheritDoc}
     */
    protected function buildBulkException(array $caughtExceptions): BulkCommandExceptionInterface
    {
        return new BulkDiscountException(
            $caughtExceptions,
            'Errors occurred during discount bulk change status action',
            BulkDiscountException::FAILED_BULK_DELETE
        );
    }

    /**
     * {@inheritDoc}
     */
    protected function supports($id): bool
    {
        return $id instanceof DiscountId;
    }
}
