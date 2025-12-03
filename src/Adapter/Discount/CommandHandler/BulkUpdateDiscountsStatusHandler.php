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

use CartRule;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\AbstractBulkCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\Discount\Command\BulkUpdateDiscountsStatusCommand;
use PrestaShop\PrestaShop\Core\Domain\Discount\CommandHandler\BulkUpdateDiscountsStatusHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Discount\Exception\BulkDiscountException;
use PrestaShop\PrestaShop\Core\Domain\Discount\Exception\CannotUpdateDiscountException;
use PrestaShop\PrestaShop\Core\Domain\Discount\Exception\DiscountException;
use PrestaShop\PrestaShop\Core\Domain\Discount\Exception\DiscountNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Discount\ValueObject\DiscountId;
use PrestaShop\PrestaShop\Core\Domain\Exception\BulkCommandExceptionInterface;

#[AsCommandHandler]
final class BulkUpdateDiscountsStatusHandler extends AbstractBulkCommandHandler implements BulkUpdateDiscountsStatusHandlerInterface
{
    /**
     * {@inheritdoc}
     *
     * @throws CannotUpdateDiscountException
     * @throws DiscountNotFoundException
     */
    public function handle(BulkUpdateDiscountsStatusCommand $command): void
    {
        $this->handleBulkAction($command->getDiscountIds(), DiscountException::class, $command);
    }

    /**
     * @param DiscountId $id
     * @param BulkUpdateDiscountsStatusCommand $command
     *
     * @return void
     */
    protected function handleSingleAction(mixed $id, mixed $command): void
    {
        $entity = new CartRule($id->getValue());
        $entity->active = $command->getNewStatus();

        if (!$entity->id) {
            throw new DiscountNotFoundException(sprintf('Discount with id "%s" was not found', $id->getValue()));
        }
        if (!$entity->update()) {
            throw new CannotUpdateDiscountException(sprintf('Cannot update status for discount with id "%s"', $id->getValue()));
        }
    }

    /**
     * {@inheritDoc}
     */
    protected function buildBulkException(array $caughtExceptions): BulkCommandExceptionInterface
    {
        return new BulkDiscountException(
            $caughtExceptions,
            'Errors occurred during discount bulk change status action',
            BulkDiscountException::FAILED_BULK_UPDATE_STATUS
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
