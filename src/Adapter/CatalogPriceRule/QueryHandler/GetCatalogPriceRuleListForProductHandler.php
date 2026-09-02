<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\CatalogPriceRule\QueryHandler;

use PrestaShop\Decimal\DecimalNumber;
use PrestaShop\PrestaShop\Adapter\CatalogPriceRule\Repository\CatalogPriceRuleRepository;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsQueryHandler;
use PrestaShop\PrestaShop\Core\Domain\CatalogPriceRule\Query\GetCatalogPriceRuleListForProduct;
use PrestaShop\PrestaShop\Core\Domain\CatalogPriceRule\QueryHandler\GetCatalogPriceRuleListForProductHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\CatalogPriceRule\QueryResult\CatalogPriceRuleForListing;
use PrestaShop\PrestaShop\Core\Domain\CatalogPriceRule\QueryResult\CatalogPriceRuleList;
use PrestaShop\PrestaShop\Core\Util\DateTime\DateTime as DateTimeUtil;

/**
 * Handles @see GetCatalogPriceRuleListForProduct
 */
#[AsQueryHandler]
class GetCatalogPriceRuleListForProductHandler implements GetCatalogPriceRuleListForProductHandlerInterface
{
    /**
     * @var CatalogPriceRuleRepository
     */
    private $catalogPriceRuleRepository;

    /**
     * @param CatalogPriceRuleRepository $catalogPriceRuleRepository
     */
    public function __construct(
        CatalogPriceRuleRepository $catalogPriceRuleRepository
    ) {
        $this->catalogPriceRuleRepository = $catalogPriceRuleRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(GetCatalogPriceRuleListForProduct $query): CatalogPriceRuleList
    {
        $catalogPriceRules = $this->catalogPriceRuleRepository->getByProductId(
            $query->getProductId(),
            $query->getLangId(),
            $query->getLimit(),
            $query->getOffset()
        );

        return new CatalogPriceRuleList(
            $this->formatCatalogPriceRuleList($catalogPriceRules),
            $this->catalogPriceRuleRepository->countByProductId(
                $query->getProductId(),
                $query->getLangId()
            )
        );
    }

    /**
     * @param array<int, array<string, string|null>> $catalogPriceRules
     *
     * @return CatalogPriceRuleForListing[]
     */
    private function formatCatalogPriceRuleList(array $catalogPriceRules): array
    {
        $return = [];
        foreach ($catalogPriceRules as $catalogPriceRule) {
            $return[] = new CatalogPriceRuleForListing(
                (int) $catalogPriceRule['id_specific_price_rule'],
                $catalogPriceRule['specific_price_rule_name'],
                (int) $catalogPriceRule['from_quantity'],
                $catalogPriceRule['reduction_type'],
                new DecimalNumber($catalogPriceRule['reduction']),
                (bool) $catalogPriceRule['reduction_tax'],
                DateTimeUtil::buildNullableDateTime($catalogPriceRule['from']),
                DateTimeUtil::buildNullableDateTime($catalogPriceRule['to']),
                $catalogPriceRule['shop_name'],
                $catalogPriceRule['currency_name'],
                $catalogPriceRule['lang_name'],
                $catalogPriceRule['group_name'],
                $catalogPriceRule['currency_iso']
            );
        }

        return $return;
    }
}
