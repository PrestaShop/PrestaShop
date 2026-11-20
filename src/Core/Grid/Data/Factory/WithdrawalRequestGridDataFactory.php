<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Grid\Data\Factory;

use PrestaShop\PrestaShop\Core\Domain\WithdrawalRequest\ValueObject\WithdrawalRequestStatus;
use PrestaShop\PrestaShop\Core\Form\ChoiceProvider\WithdrawalRequestStatusChoiceProvider;
use PrestaShop\PrestaShop\Core\Grid\Data\GridData;
use PrestaShop\PrestaShop\Core\Grid\Record\RecordCollection;
use PrestaShop\PrestaShop\Core\Grid\Search\SearchCriteriaInterface;

/**
 * Adds the translated status label and the badge type expected by BadgeColumn to the withdrawal request grid.
 */
final class WithdrawalRequestGridDataFactory implements GridDataFactoryInterface
{
    public function __construct(
        private readonly GridDataFactoryInterface $withdrawalRequestDataFactory,
        private readonly WithdrawalRequestStatusChoiceProvider $statusChoiceProvider,
    ) {
    }

    /**
     * {@inheritdoc}
     */
    public function getData(SearchCriteriaInterface $searchCriteria): GridData
    {
        $data = $this->withdrawalRequestDataFactory->getData($searchCriteria);
        $labels = array_flip($this->statusChoiceProvider->getChoices());

        $records = [];
        foreach ($data->getRecords() as $record) {
            $status = WithdrawalRequestStatus::tryFrom((string) $record['status']);
            $record['status_label'] = null === $status ? (string) $record['status'] : $labels[$status->value];
            $record['status_badge_type'] = match ($status) {
                WithdrawalRequestStatus::PENDING => 'warning',
                WithdrawalRequestStatus::PROCESSED => 'success',
                WithdrawalRequestStatus::REFUSED => 'danger',
                default => 'secondary',
            };
            $records[] = $record;
        }

        return new GridData(new RecordCollection($records), $data->getRecordsTotal(), $data->getQuery());
    }
}
