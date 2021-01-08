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
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\Command\UpdateCombinationPricesCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\CommandHandler\UpdateCombinationPricesHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\Exception\CannotUpdateCombinationException;

/**
 * Handles @see UpdateCombinationPricesCommand using legacy object model
 */
final class UpdateCombinationPricesHandler implements UpdateCombinationPricesHandlerInterface
{
    /**
     * @var CombinationRepository
     */
    private $combinationRepository;

    /**
     * @param CombinationRepository $combinationRepository
     */
    public function __construct(CombinationRepository $combinationRepository)
    {
        $this->combinationRepository = $combinationRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(UpdateCombinationPricesCommand $command): void
    {
        $combination = $this->combinationRepository->get($command->getCombinationId());
        $updatableProperties = $this->fillUpdatableProperties($combination, $command);
        $this->combinationRepository->partialUpdate($combination, $updatableProperties, CannotUpdateCombinationException::FAILED_UPDATE_PRICES);
    }

    /**
     * @param Combination $combination
     * @param UpdateCombinationPricesCommand $command
     *
     * @return array<int, string>
     */
    private function fillUpdatableProperties(Combination $combination, UpdateCombinationPricesCommand $command): array
    {
        $updatableProperties = [];

        if (null !== $command->getImpactOnPrice()) {
            $combination->price = (float) (string) $command->getImpactOnPrice();
            $updatableProperties[] = 'price';
        }

        if (null !== $command->getEcoTax()) {
            $combination->ecotax = (float) (string) $command->getEcoTax();
            $updatableProperties[] = 'ecotax';
        }

        if (null !== $command->getImpactOnUnitPrice()) {
            $combination->unit_price_impact = (float) (string) $command->getImpactOnUnitPrice();
            $updatableProperties[] = 'unit_price_impact';
        }

        if (null !== $command->getWholesalePrice()) {
            $combination->wholesale_price = (float) (string) $command->getWholesalePrice();
            $updatableProperties[] = 'wholesale_price';
        }

        return $updatableProperties;
    }
}
