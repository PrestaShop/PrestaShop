<?php
/**
 * 2007-2019 PrestaShop and Contributors
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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Core\Currency;

use PrestaShop\PrestaShop\Core\Grid\Data\Factory\GridDataFactoryInterface;
use PrestaShop\PrestaShop\Core\Grid\Data\GridData;
use PrestaShop\PrestaShop\Core\Grid\Record\RecordCollection;
use PrestaShop\PrestaShop\Core\Grid\Record\RecordCollectionInterface;
use PrestaShop\PrestaShop\Core\Grid\Search\SearchCriteriaInterface;

/**
 * Class CurrencyGridDataFactory is responsible for providing modified currency list grid data.
 */
final class CurrencyGridDataFactory implements GridDataFactoryInterface
{
    /**
     * @var GridDataFactoryInterface
     */
    private $gridDataFactory;

    /**
     * CurrencyGridDataFactory constructor.
     *
     * @param GridDataFactoryInterface $gridDataFactory
     */
    public function __construct(
        GridDataFactoryInterface $gridDataFactory
    ) {
        $this->gridDataFactory = $gridDataFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function getData(SearchCriteriaInterface $searchCriteria)
    {
        $gridData = $this->gridDataFactory->getData($searchCriteria);

        $records = $gridData->getRecords();

        return new GridData(
            $this->getModifiedRecords($records),
            $gridData->getRecordsTotal(),
            $gridData->getQuery()
        );
    }

    /**
     * Gets record collection with extra and modified rows.
     *
     * @param RecordCollectionInterface $records
     *
     * @return RecordCollection
     */
    private function getModifiedRecords(RecordCollectionInterface $records)
    {
        $result = [];
        foreach ($records as $key => $record) {
            $result[$key] = $record;
            $result[$key]['currency'] = ucfirst($result[$key]['name']);
            $result[$key]['conversion_rate'] = (float) $result[$key]['conversion_rate'];
        }

        return new RecordCollection($result);
    }
}
