<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Grid\Data\Factory;

use PrestaShop\PrestaShop\Core\Grid\Data\GridData;
use PrestaShop\PrestaShop\Core\Grid\Search\SearchCriteriaInterface;

/**
 * Class CustomerDiscountGridDataFactoryDecorator decorates data from customer addresses doctrine data factory.
 */
final class CustomerDiscountGridDataFactoryDecorator implements GridDataFactoryInterface
{
    /**
     * @var GridDataFactoryInterface
     */
    private $customerDiscountDoctrineGridDataFactory;

    /**
     * @param GridDataFactoryInterface $customerDiscountDoctrineGridDataFactory
     */
    public function __construct(
        GridDataFactoryInterface $customerDiscountDoctrineGridDataFactory
    ) {
        $this->customerDiscountDoctrineGridDataFactory = $customerDiscountDoctrineGridDataFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function getData(SearchCriteriaInterface $searchCriteria)
    {
        $customerData = $this->customerDiscountDoctrineGridDataFactory->getData($searchCriteria);

        return new GridData(
            $customerData->getRecords(),
            $customerData->getRecordsTotal(),
            $customerData->getQuery()
        );
    }
}
