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

namespace PrestaShop\PrestaShop\Core\Grid\Data\Factory;

use PrestaShop\PrestaShop\Core\Grid\Data\GridData;
use PrestaShop\PrestaShop\Core\Grid\Record\RecordCollection;
use PrestaShop\PrestaShop\Core\Grid\Search\SearchCriteriaInterface;

/**
 * Class CategoryDataFactory decorates DoctrineGridDataFactory configured for categories to modify category records.
 */
final class CategoryDataFactory implements GridDataFactoryInterface
{
    /**
     * @var GridDataFactoryInterface
     */
    private $doctrineCategoryDataFactory;

    /**
     * @param GridDataFactoryInterface $doctrineCategoryDataFactory
     */
    public function __construct(GridDataFactoryInterface $doctrineCategoryDataFactory)
    {
        $this->doctrineCategoryDataFactory = $doctrineCategoryDataFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function getData(SearchCriteriaInterface $searchCriteria)
    {
        $data = $this->doctrineCategoryDataFactory->getData($searchCriteria);

        $records = $this->modifyRecords($data->getRecords()->all());

        return new GridData(
            new RecordCollection($records),
            $data->getRecordsTotal(),
            $data->getQuery()
        );
    }

    /**
     * Modify category records.
     *
     * @param array $records
     *
     * @return array
     */
    private function modifyRecords(array $records)
    {
        foreach ($records as $key => $record) {
            $records[$key]['description'] = strip_tags(stripslashes($record['description']));
        }

        return $records;
    }
}
