<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Grid\Data\Factory;

use CartRule;
use PrestaShop\PrestaShop\Core\Grid\Data\GridData;
use PrestaShop\PrestaShop\Core\Grid\Record\RecordCollection;
use PrestaShop\PrestaShop\Core\Grid\Search\SearchCriteriaInterface;

/**
 * Class CustomerDiscountGridDataFactory is responsible for returning grid data for customer's discounts.
 */
final class CustomerDiscountGridDataFactory implements GridDataFactoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function getData(SearchCriteriaInterface $searchCriteria)
    {
        $customerFilters = $searchCriteria->getFilters();
        $allDiscounts = CartRule::getAllCustomerCartRules(
            $customerFilters['id_customer']
        );

        $discountsToDisplay = array_slice(
            $allDiscounts,
            (int) $searchCriteria->getOffset(),
            (int) $searchCriteria->getLimit()
        );

        $records = new RecordCollection($discountsToDisplay);

        return new GridData(
            $records,
            count($allDiscounts)
        );
    }
}
