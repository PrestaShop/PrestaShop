<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Grid\Data\Factory;

use PrestaShop\PrestaShop\Core\Grid\Data\GridData;
use PrestaShop\PrestaShop\Core\Grid\Record\RecordCollection;
use PrestaShop\PrestaShop\Core\Grid\Record\RecordCollectionInterface;
use PrestaShop\PrestaShop\Core\Grid\Search\SearchCriteriaInterface;
use PrestaShopBundle\Entity\Enum\BusinessEntityStatus;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Decorates the business entity grid data with the translated status label and the badge
 * type expected by BadgeColumn, so the Status badge is localized and colored like the
 * detail view and the status filter choices.
 */
final class BusinessEntityGridDataFactory implements GridDataFactoryInterface
{
    public function __construct(
        private readonly GridDataFactoryInterface $businessEntityDataFactory,
        private readonly TranslatorInterface $translator,
    ) {
    }

    /**
     * {@inheritdoc}
     */
    public function getData(SearchCriteriaInterface $searchCriteria): GridData
    {
        $data = $this->businessEntityDataFactory->getData($searchCriteria);

        return new GridData(
            $this->addStatusPresentation($data->getRecords()),
            $data->getRecordsTotal(),
            $data->getQuery()
        );
    }

    private function addStatusPresentation(RecordCollectionInterface $records): RecordCollectionInterface
    {
        $modifiedRecords = [];
        foreach ($records as $record) {
            $status = BusinessEntityStatus::from((string) $record['status']);
            $record['status_label'] = $status->trans($this->translator);
            $record['status_badge_type'] = $status->badgeType();
            $modifiedRecords[] = $record;
        }

        return new RecordCollection($modifiedRecords);
    }
}
