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
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\Command\UpdateCombinationDetailsCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\CommandHandler\UpdateCombinationDetailsHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\Exception\CannotUpdateCombinationException;

/**
 * Handles @see UpdateCombinationDetailsCommand using legacy object model
 */
final class UpdateCombinationDetailsHandler implements UpdateCombinationDetailsHandlerInterface
{
    /**
     * @var CombinationRepository
     */
    private $combinationRepository;

    /**
     * @param CombinationRepository $combinationRepository
     */
    public function __construct(
        CombinationRepository $combinationRepository
    ) {
        $this->combinationRepository = $combinationRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(UpdateCombinationDetailsCommand $command): void
    {
        $combination = $this->combinationRepository->get($command->getCombinationId());
        $updatableProperties = $this->fillUpdatableProperties($combination, $command);

        $this->combinationRepository->partialUpdate(
            $combination,
            $updatableProperties,
            CannotUpdateCombinationException::FAILED_UPDATE_DETAILS
        );
    }

    /**
     * @param Combination $combination
     * @param UpdateCombinationDetailsCommand $command
     *
     * @return string[]|array<string, int[]>
     */
    private function fillUpdatableProperties(Combination $combination, UpdateCombinationDetailsCommand $command): array
    {
        //@todo: ps_stock table contains properties(reference, ean13 etc.) that should be updated too depending if we still support ADVANCED_STOCK_MANAGEMENT
        //  check Product::updateAttribute L 2165
        $updatableProperties = [];

        if (null !== $command->getEan13()) {
            $combination->ean13 = $command->getEan13()->getValue();
            $updatableProperties[] = 'ean13';
        }

        if (null !== $command->getIsbn()) {
            $combination->isbn = $command->getIsbn()->getValue();
            $updatableProperties[] = 'isbn';
        }

        if (null !== $command->getMpn()) {
            $combination->mpn = $command->getMpn();
            $updatableProperties[] = 'mpn';
        }

        if (null !== $command->getReference()) {
            $combination->reference = $command->getReference()->getValue();
            $updatableProperties[] = 'reference';
        }

        if (null !== $command->getUpc()) {
            $combination->upc = $command->getUpc()->getValue();
            $updatableProperties[] = 'upc';
        }

        if (null !== $command->getWeight()) {
            $combination->weight = (float) (string) $command->getWeight();
        }

        return $updatableProperties;
    }
}
