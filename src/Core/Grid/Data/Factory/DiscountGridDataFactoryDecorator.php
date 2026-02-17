<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Grid\Data\Factory;

use PrestaShop\PrestaShop\Core\Grid\Data\GridData;
use PrestaShop\PrestaShop\Core\Grid\Data\GridDataInterface;
use PrestaShop\PrestaShop\Core\Grid\Record\RecordCollection;
use PrestaShop\PrestaShop\Core\Grid\Search\SearchCriteriaInterface;
use PrestaShop\PrestaShop\Core\Util\DateTime\DateTime as DateTimeUtil;

/**
 * Decorates discount grid data to display a fallback when expiration date is null
 */
final class DiscountGridDataFactoryDecorator implements GridDataFactoryInterface
{
    private const EMPTY_DATE_FALLBACK = '—';

    public function __construct(
        private readonly GridDataFactoryInterface $discountGridDataFactory,
    ) {
    }

    /**
     * {@inheritdoc}
     */
    public function getData(SearchCriteriaInterface $searchCriteria): GridDataInterface
    {
        $data = $this->discountGridDataFactory->getData($searchCriteria);
        $records = $data->getRecords()->all();

        foreach ($records as &$record) {
            if (DateTimeUtil::isNull($record['date_to'] ?? null)) {
                $record['date_to'] = self::EMPTY_DATE_FALLBACK;
            }
        }

        return new GridData(
            new RecordCollection($records),
            $data->getRecordsTotal(),
            $data->getQuery()
        );
    }
}
