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

namespace PrestaShop\PrestaShop\Adapter\Product\Combination\QueryHandler;

use Combination;
use DateTime;
use PrestaShop\Decimal\DecimalNumber;
use PrestaShop\PrestaShop\Adapter\Product\Combination\Repository\CombinationRepository;
use PrestaShop\PrestaShop\Adapter\Product\Stock\Repository\StockAvailableRepository;
use PrestaShop\PrestaShop\Core\Domain\Language\ValueObject\LanguageId;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\Query\GetCombinationForEditing;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\QueryHandler\GetCombinationForEditingHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\QueryResult\CombinationDetails;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\QueryResult\CombinationForEditing;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\QueryResult\CombinationPrices;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\QueryResult\CombinationStock;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\ValueObject\CombinationId;
use PrestaShop\PrestaShop\Core\Util\DateTime\DateTime as DateTimeUtil;

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
     * @var StockAvailableRepository
     */
    private $stockAvailableRepository;

    /**
     * @var int
     */
    private $contextLanguageId;

    /**
     * @param CombinationRepository $combinationRepository
     * @param StockAvailableRepository $stockAvailableRepository
     * @param int $contextLanguageId
     */
    public function __construct(
        CombinationRepository $combinationRepository,
        StockAvailableRepository $stockAvailableRepository,
        int $contextLanguageId
    ) {
        $this->combinationRepository = $combinationRepository;
        $this->stockAvailableRepository = $stockAvailableRepository;
        $this->contextLanguageId = $contextLanguageId;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(GetCombinationForEditing $query): CombinationForEditing
    {
        $combination = $this->combinationRepository->get($query->getCombinationId());

        return new CombinationForEditing(
            $this->getCombinationName($query->getCombinationId()),
            $this->getDetails($combination),
            $this->getPrices($combination),
            $this->getStock($combination)
        );
    }

    /**
     * @param CombinationId $combinationId
     *
     * @return string
     */
    private function getCombinationName(CombinationId $combinationId): string
    {
        $attributesInformation = $this->combinationRepository->getAttributesInfoByCombinationIds(
            [$combinationId->getValue()],
            new LanguageId($this->contextLanguageId)
        );
        $attributes = $attributesInformation[$combinationId->getValue()];

        return implode(', ', array_map(function ($attribute) {
            return sprintf(
                '%s - %s',
                $attribute['attribute_group_name'],
                $attribute['attribute_name']
            );
        }, $attributes));
    }

    /**
     * @param Combination $combination
     *
     * @return CombinationDetails
     */
    private function getDetails(Combination $combination): CombinationDetails
    {
        return new CombinationDetails(
            $combination->ean13,
            $combination->isbn,
            $combination->mpn,
            $combination->reference,
            $combination->upc,
            new DecimalNumber($combination->weight)
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

    /**
     * @param Combination $combination
     *
     * @return CombinationStock
     */
    private function getStock(Combination $combination): CombinationStock
    {
        $stockAvailable = $this->stockAvailableRepository->getForCombination(new Combinationid($combination->id));

        return new CombinationStock(
            (int) $stockAvailable->quantity,
            (int) $combination->minimal_quantity,
            (int) $combination->low_stock_threshold,
            (bool) $combination->low_stock_alert,
            $stockAvailable->location,
            DateTimeUtil::NULL_DATE === $combination->available_date ? null : new DateTime($combination->available_date)
        );
    }
}
