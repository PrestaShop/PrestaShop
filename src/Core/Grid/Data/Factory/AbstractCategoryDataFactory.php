<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Grid\Data\Factory;

use PrestaShop\PrestaShop\Core\Grid\Data\GridData;
use PrestaShop\PrestaShop\Core\Grid\Record\RecordCollection;
use PrestaShop\PrestaShop\Core\Grid\Search\SearchCriteriaInterface;

/**
 * Class AbstractCategoryDataFactory decorates DoctrineGridDataFactory configured for categories to modify category records.
 */
abstract class AbstractCategoryDataFactory implements GridDataFactoryInterface
{
    public const DESCRIPTION_MAX_LENGTH = 150;

    /**
     * @var GridDataFactoryInterface
     */
    protected $doctrineCategoryDataFactory;

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
     */
    protected function modifyRecords(array $records): array
    {
        foreach ($records as $key => $record) {
            if ($record['description'] !== null) {
                $records[$key]['description'] = mb_substr(strip_tags(stripslashes($record['description'])), 0, self::DESCRIPTION_MAX_LENGTH);
            }
        }

        return $records;
    }
}
