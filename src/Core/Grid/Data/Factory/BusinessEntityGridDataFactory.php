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
            $this->addPresentationFields($data->getRecords()),
            $data->getRecordsTotal(),
            $data->getQuery()
        );
    }

    private function addPresentationFields(RecordCollectionInterface $records): RecordCollectionInterface
    {
        $modifiedRecords = [];
        foreach ($records as $record) {
            $status = BusinessEntityStatus::from((string) $record['status']);
            $record['status_label'] = $status->trans($this->translator);
            $record['status_badge_type'] = $status->badgeType();
            // The confirmation modal injects this message with innerHTML, so the merchant-entered
            // name is escaped here and only the emphasis markup is meant to be interpreted.
            $record['delete_confirm_message'] = $this->translator->trans(
                'Are you sure you want to delete %name% from the list of business entities?',
                ['%name%' => '<strong>' . htmlspecialchars((string) $record['name'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . '</strong>'],
                'Admin.Orderscustomers.Feature'
            )
                . '<br><small class="text-muted">'
                . $this->translator->trans('This action is irreversible.', [], 'Admin.Notifications.Warning')
                . $this->linkedCustomersWarning((int) ($record['customers_count'] ?? 0))
                . '</small>';
            $modifiedRecords[] = $record;
        }

        return new RecordCollection($modifiedRecords);
    }

    private function linkedCustomersWarning(int $linkedCustomersCount): string
    {
        if ($linkedCustomersCount < 1) {
            return '';
        }

        $warning = 1 === $linkedCustomersCount
            ? $this->translator->trans(
                '1 B2B customer is linked to this business entity. It will be kept, but the entity will no longer appear in the list.',
                [],
                'Admin.Orderscustomers.Notification'
            )
            : $this->translator->trans(
                '%count% B2B customers are linked to this business entity. They will be kept, but the entity will no longer appear in the list.',
                ['%count%' => $linkedCustomersCount],
                'Admin.Orderscustomers.Notification'
            );

        return '<br>' . $warning;
    }
}
