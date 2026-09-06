<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Grid\View;

use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use PrestaShopBundle\Entity\AdminGridView;

class GridViewSearchCriteriaFactory
{
    /**
     * @param DynamicDateRuleApplier $dateRuleApplier
     */
    public function __construct(
        private readonly DynamicDateRuleApplier $dateRuleApplier,
    ) {
    }

    /**
     * @param AdminGridView $gridView
     * @param array<string, mixed> $criteriaOverrides
     *
     * @return GridViewSearchCriteria
     */
    public function build(AdminGridView $gridView, array $criteriaOverrides = []): GridViewSearchCriteria
    {
        $searchCriteria = json_decode($gridView->getFilters(), true) ?: [];
        $searchCriteria = $this->dateRuleApplier->apply($searchCriteria, $gridView->getDynamicDateRules() ?? []);
        $searchCriteria = array_merge($searchCriteria, $criteriaOverrides);

        return new GridViewSearchCriteria(
            ShopConstraint::shop($gridView->getGridConfiguration()->getShopId()),
            $searchCriteria,
            $gridView->getFilterId()
        );
    }
}
