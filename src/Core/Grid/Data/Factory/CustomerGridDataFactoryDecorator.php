<?php
/**
 * 2007-2018 PrestaShop.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
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
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Core\Grid\Data\Factory;

use PrestaShop\PrestaShop\Core\Cldr\Repository;
use PrestaShop\PrestaShop\Core\Grid\Data\GridData;
use PrestaShop\PrestaShop\Core\Grid\Record\RecordCollection;
use PrestaShop\PrestaShop\Core\Grid\Record\RecordCollectionInterface;
use PrestaShop\PrestaShop\Core\Grid\Search\SearchCriteriaInterface;

/**
 * Class CustomerGridDataFactoryDecorator decorates data from customer doctrine data factory.
 */
final class CustomerGridDataFactoryDecorator implements GridDataFactoryInterface
{
    /**
     * @var GridDataFactoryInterface
     */
    private $customerDoctrineGridDataFactory;

    /**
     * @var Repository
     */
    private $cldrRepository;

    /**
     * @var string
     */
    private $contextCurrencyIsoCode;

    /**
     * @param GridDataFactoryInterface $customerDoctrineGridDataFactory
     * @param Repository $cldrRepository
     * @param string $contextCurrencyIsoCode
     */
    public function __construct(
        GridDataFactoryInterface $customerDoctrineGridDataFactory,
        Repository $cldrRepository,
        $contextCurrencyIsoCode
    ) {
        $this->customerDoctrineGridDataFactory = $customerDoctrineGridDataFactory;
        $this->cldrRepository = $cldrRepository;
        $this->contextCurrencyIsoCode = $contextCurrencyIsoCode;
    }

    /**
     * {@inheritdoc}
     */
    public function getData(SearchCriteriaInterface $searchCriteria)
    {
        $customerData = $this->customerDoctrineGridDataFactory->getData($searchCriteria);

        $customerRecords = $this->applyModifications($customerData->getRecords());

        return new GridData(
            $customerRecords,
            $customerData->getRecordsTotal(),
            $customerData->getQuery()
        );
    }

    /**
     * @param RecordCollectionInterface $customers
     *
     * @return RecordCollection
     */
    private function applyModifications(RecordCollectionInterface $customers)
    {
        $modifiedCustomers = [];

        foreach ($customers as $customer) {
            if (empty($customer['social_title'])) {
                $customer['social_title'] = '--';
            }

            if (null === $customer['company']) {
                $customer['company'] = '--';
            }

            if (!empty($customer['total_spent'])) {
                $customer['total_spent'] = $this->cldrRepository->getPrice(
                    $customer['total_spent'],
                    $this->contextCurrencyIsoCode
                );
            }

            if (null === $customer['connect']) {
                $customer['connect'] = '--';
            }

            $modifiedCustomers[] = $customer;
        }

        return new RecordCollection($modifiedCustomers);
    }
}
