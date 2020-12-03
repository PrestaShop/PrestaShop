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

namespace PrestaShop\PrestaShop\Adapter\Product\QueryHandler;

use Combination;
use PrestaShop\Decimal\DecimalNumber;
use PrestaShop\PrestaShop\Adapter\Product\Repository\CombinationRepository;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\Query\GetCombinationForEditing;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\QueryHandler\GetCombinationForEditingHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\QueryResult\CombinationForEditing;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\QueryResult\CombinationOptions;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\QueryResult\CombinationPrices;

/**
 * Handles @see GetCombinationForEditing query using legacy object model
 */
final class GetCombinationForEditingHandler implements GetCombinationForEditingHandlerInterface
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
    public function handle(GetCombinationForEditing $query): CombinationForEditing
    {
        $combination = $this->combinationRepository->get($query->getCombinationId());

        return new CombinationForEditing(
            $this->getOptions($combination),
            $this->getPrices($combination)
        );
    }

    /**
     * @param Combination $combination
     *
     * @return CombinationOptions
     */
    private function getOptions(Combination $combination): CombinationOptions
    {
        return new CombinationOptions(
            $combination->ean13,
            $combination->isbn,
            $combination->mpn,
            $combination->reference,
            $combination->upc
        );
    }

    /**
     * @param Combination $combination
     *
     * @return CombinationPrices
     */
    private function getPrices(Combination $combination): CombinationPrices
    {
        return new CombinationPrices(
            new DecimalNumber($combination->ecotax),
            new DecimalNumber($combination->price),
            new DecimalNumber($combination->unit_price_impact),
            new DecimalNumber($combination->wholesale_price)
        );
    }
}
