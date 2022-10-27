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

namespace PrestaShop\PrestaShop\Core\Grid\Data\Factory;

use PrestaShop\PrestaShop\Core\Domain\CustomerService\Status\CustomerThreadStatusColor;
use PrestaShop\PrestaShop\Core\Grid\Data\GridData;
use PrestaShop\PrestaShop\Core\Grid\Record\RecordCollection;
use PrestaShop\PrestaShop\Core\Grid\Record\RecordCollectionInterface;
use PrestaShop\PrestaShop\Core\Grid\Search\SearchCriteriaInterface;

/**
 * Class decorates data from customer thread doctrine data factory by adding colors for status inputs.
 */
final class CustomerThreadGridDataFactoryDecorator implements GridDataFactoryInterface
{
    /**
     * @var GridDataFactoryInterface
     */
    private $customerDoctrineGridDataFactory;

    /**
     * @param GridDataFactoryInterface $customerDoctrineGridDataFactory
     */
    public function __construct(
        GridDataFactoryInterface $customerDoctrineGridDataFactory
    ) {
        $this->customerDoctrineGridDataFactory = $customerDoctrineGridDataFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function getData(SearchCriteriaInterface $searchCriteria): GridData
    {
        $customerThreadData = $this->customerDoctrineGridDataFactory->getData($searchCriteria);

        $customerThreadRecords = $this->applyModifications($customerThreadData->getRecords());

        return new GridData(
            $customerThreadRecords,
            $customerThreadData->getRecordsTotal(),
            $customerThreadData->getQuery()
        );
    }

    /**
     * @param RecordCollectionInterface $customerThreads
     *
     * @return RecordCollection
     */
    private function applyModifications(RecordCollectionInterface $customerThreads): RecordCollection
    {
        $modifiedCustomerThreads = [];

        foreach ($customerThreads as $customerThread) {
            $customerThread['status_color'] = CustomerThreadStatusColor::CUSTOMER_THREAD_STATUSES[$customerThread['status']];
            $modifiedCustomerThreads[] = $customerThread;
        }

        return new RecordCollection($modifiedCustomerThreads);
    }
}
