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

namespace PrestaShop\PrestaShop\Adapter\Product\Combination\CommandHandler;

use Combination;
use PrestaShop\PrestaShop\Adapter\Product\Combination\Repository\CombinationRepository;
use PrestaShop\PrestaShop\Adapter\Product\Combination\Update\CombinationStockProperties;
use PrestaShop\PrestaShop\Adapter\Product\Combination\Update\CombinationStockUpdater;
use PrestaShop\PrestaShop\Adapter\Product\Combination\Update\DefaultCombinationUpdater;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\Command\BulkUpdateCombinationsFromListingCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\CommandHandler\BulkUpdateCombinationsFromListingHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\Exception\CannotUpdateCombinationException;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\ListedCombinationForEditing;

/**
 * Handles @see BulkUpdateCombinationsFromListingCommand using legacy object model
 */
final class BulkUpdateCombinationsFromListingHandler implements BulkUpdateCombinationsFromListingHandlerInterface
{
    /**
     * @var CombinationRepository
     */
    private $combinationRepository;

    /**
     * @var DefaultCombinationUpdater
     */
    private $defaultCombinationUpdater;

    /**
     * @var CombinationStockUpdater
     */
    private $combinationStockUpdater;

    /**
     * @param CombinationRepository $combinationRepository
     * @param DefaultCombinationUpdater $defaultCombinationUpdater
     * @param CombinationStockUpdater $combinationStockUpdater
     */
    public function __construct(
        CombinationRepository $combinationRepository,
        DefaultCombinationUpdater $defaultCombinationUpdater,
        CombinationStockUpdater $combinationStockUpdater
    ) {
        $this->combinationRepository = $combinationRepository;
        $this->defaultCombinationUpdater = $defaultCombinationUpdater;
        $this->combinationStockUpdater = $combinationStockUpdater;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(BulkUpdateCombinationsFromListingCommand $command): void
    {
        foreach ($command->getListedCombinationsForEditing() as $listedCombinationForEditing) {
            $this->updateOne($listedCombinationForEditing);
        }
    }

    /**
     * @param ListedCombinationForEditing $listedCombinationForEditing
     */
    private function updateOne(ListedCombinationForEditing $listedCombinationForEditing): void
    {
        $combination = $this->combinationRepository->get($listedCombinationForEditing->getCombinationId());
        $this->combinationRepository->partialUpdate(
            $combination,
            $this->fillUpdatableProperties($combination, $listedCombinationForEditing),
            CannotUpdateCombinationException::FAILED_UPDATE_LISTED_COMBINATION
        );

        if (true === $listedCombinationForEditing->isDefault()) {
            $this->defaultCombinationUpdater->setDefaultCombination($listedCombinationForEditing->getCombinationId());
        }

        $this->combinationStockUpdater->update(
            $listedCombinationForEditing->getCombinationId(),
            new CombinationStockProperties($listedCombinationForEditing->getQuantity())
        );
    }

    /**
     * @param Combination $combination
     * @param ListedCombinationForEditing $listedCombinationForEditing
     *
     * @return array<int, string>
     */
    private function fillUpdatableProperties(Combination $combination, ListedCombinationForEditing $listedCombinationForEditing): array
    {
        $updatableProperties = [];

        if (null !== $listedCombinationForEditing->getImpactOnPrice()) {
            $combination->price = (float) (string) $listedCombinationForEditing->getImpactOnPrice();
            $updatableProperties[] = 'price';
        }

        if (null !== $listedCombinationForEditing->getQuantity()) {
            $combination->quantity = $listedCombinationForEditing->getQuantity();
            $updatableProperties[] = 'quantity';
        }

        return $updatableProperties;
    }
}
