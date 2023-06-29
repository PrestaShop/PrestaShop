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

namespace PrestaShop\PrestaShop\Adapter\Feature\CommandHandler;

use PrestaShop\PrestaShop\Adapter\Feature\Repository\FeatureValueRepository;
use PrestaShop\PrestaShop\Core\Domain\AbstractBulkCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\Exception\BulkCommandExceptionInterface;
use PrestaShop\PrestaShop\Core\Domain\Feature\Command\BulkDeleteFeatureValueCommand;
use PrestaShop\PrestaShop\Core\Domain\Feature\CommandHandler\BulkDeleteFeatureValueHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Feature\Exception\BulkFeatureValueException;
use PrestaShop\PrestaShop\Core\Domain\Feature\Exception\FeatureValueException;
use PrestaShop\PrestaShop\Core\Domain\Feature\ValueObject\FeatureValueId;

class BulkDeleteFeatureValueHandler extends AbstractBulkCommandHandler implements BulkDeleteFeatureValueHandlerInterface
{
    public function __construct(
        protected readonly FeatureValueRepository $featureValueRepository
    ) {
    }

    public function handle(BulkDeleteFeatureValueCommand $command): void
    {
        $this->handleBulkAction($command->getFeatureValueIds(), FeatureValueException::class);
    }

    /**
     * @param FeatureValueId $id
     * @param mixed $command
     *
     * @return void
     */
    protected function handleSingleAction(mixed $id, mixed $command): void
    {
        $this->featureValueRepository->delete($id);
    }

    /**
     * {@inheritDoc}
     */
    protected function buildBulkException(array $caughtExceptions): BulkCommandExceptionInterface
    {
        return new BulkFeatureValueException(
            $caughtExceptions,
            'Errors occurred during Feature value bulk delete action',
            BulkFeatureValueException::FAILED_BULK_DELETE
        );
    }

    /**
     * {@inheritDoc}
     */
    protected function supports($id): bool
    {
        return $id instanceof FeatureValueId;
    }
}
