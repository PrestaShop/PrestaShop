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

namespace PrestaShop\PrestaShop\Adapter\Alias\CommandHandler;

use PrestaShop\PrestaShop\Adapter\Alias\Repository\AliasRepository;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\AbstractBulkCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\Alias\Command\BulkDeleteAliasCommand;
use PrestaShop\PrestaShop\Core\Domain\Alias\CommandHandler\BulkDeleteAliasHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Alias\Exception\AliasException;
use PrestaShop\PrestaShop\Core\Domain\Alias\Exception\BulkAliasException;
use PrestaShop\PrestaShop\Core\Domain\Alias\ValueObject\AliasId;
use PrestaShop\PrestaShop\Core\Domain\Exception\BulkCommandExceptionInterface;

/**
 * Handles command which deletes aliases in bulk action
 */
#[AsCommandHandler]
class BulkDeleteAliasHandler extends AbstractBulkCommandHandler implements BulkDeleteAliasHandlerInterface
{
    public function __construct(protected AliasRepository $aliasRepository)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function handle(BulkDeleteAliasCommand $command): void
    {
        $this->handleBulkAction($command->getAliasIds(), AliasException::class);
    }

    /**
     * @param AliasId $id
     * @param mixed $command
     *
     * @return void
     */
    protected function handleSingleAction(mixed $id, mixed $command): void
    {
        $this->aliasRepository->delete($id);
    }

    /**
     * {@inheritDoc}
     */
    protected function buildBulkException(array $caughtExceptions): BulkCommandExceptionInterface
    {
        return new BulkAliasException(
            $caughtExceptions,
            'Errors occurred during Alias bulk delete action',
        );
    }

    /**
     * {@inheritDoc}
     */
    protected function supports(mixed $id): bool
    {
        return $id instanceof AliasId;
    }
}
