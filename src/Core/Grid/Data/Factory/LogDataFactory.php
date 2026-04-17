<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Grid\Data\Factory;

use Employee;
use PrestaShop\PrestaShop\Adapter\Validate;
use PrestaShop\PrestaShop\Core\Employee\AvatarProviderInterface;
use PrestaShop\PrestaShop\Core\Grid\Data\GridData;
use PrestaShop\PrestaShop\Core\Grid\Record\RecordCollection;
use PrestaShop\PrestaShop\Core\Grid\Search\SearchCriteriaInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class LogDataFactory decorates DoctrineGridDataFactory configured for logs to modify log records.
 */
final class LogDataFactory implements GridDataFactoryInterface
{
    /**
     * @var AvatarProviderInterface
     */
    private $avatarProvider;

    /**
     * @var GridDataFactoryInterface
     */
    private $dataFactory;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var string
     */
    private const DEFAULT_EMPTY_DATA = '---';

    /**
     * @var array<int, string>
     */
    private $avatars = [];

    /**
     * @param GridDataFactoryInterface $dataFactory
     * @param TranslatorInterface $translator
     * @param AvatarProviderInterface $avatarProvider
     */
    public function __construct(
        GridDataFactoryInterface $dataFactory,
        TranslatorInterface $translator,
        AvatarProviderInterface $avatarProvider
    ) {
        $this->dataFactory = $dataFactory;
        $this->translator = $translator;
        $this->avatarProvider = $avatarProvider;
    }

    /**
     * {@inheritdoc}
     */
    public function getData(SearchCriteriaInterface $searchCriteria): GridData
    {
        $data = $this->dataFactory->getData($searchCriteria);

        $records = $this->modifyRecords($data->getRecords()->all());

        return new GridData(
            new RecordCollection($records),
            $data->getRecordsTotal(),
            $data->getQuery()
        );
    }

    /**
     * Modify log records.
     *
     * @param array $records
     *
     * @return array
     */
    private function modifyRecords(array $records): array
    {
        foreach ($records as $key => $record) {
            $records[$key]['shop_name'] = $this->ShopContextFormatted($record);
            $records[$key]['language'] = $records[$key]['language'] ?? self::DEFAULT_EMPTY_DATA;
            $records[$key]['image'] = $this->getEmployeeAvatar((int) $record['id_employee']);
        }

        return $records;
    }

    /**
     * Format shop context for grid.
     *
     * @param array $record
     *
     * @return string
     */
    private function ShopContextFormatted(array $record): string
    {
        if (!empty($record['in_all_shops'])) {
            return $this->translator->trans('All stores', [], 'Admin.Global');
        }

        if (!empty($record['id_shop']) && empty($record['id_shop_group'])) {
            $shop_name = $this->translator->trans('Store', [], 'Admin.Global');
            $shop_name .= ' ' . $record['shop_name'] . ' (id : ' . $record['id_shop'] . ')';

            return $shop_name;
        }

        if (empty($record['id_shop']) && !empty($record['id_shop_group'])) {
            $shop_name = $this->translator->trans('Shop group', [], 'Admin.Global');
            $shop_name .= ' ' . $record['shop_group_name'] . ' (id : ' . $record['id_shop_group'] . ')';

            return $shop_name;
        }

        return self::DEFAULT_EMPTY_DATA;
    }

    /**
     * @param int $idEmployee
     *
     * @return string
     */
    private function getEmployeeAvatar(int $idEmployee): string
    {
        if (!isset($this->avatars[$idEmployee])) {
            $employee = new Employee($idEmployee);
            if (Validate::isLoadedObject($employee)) {
                $this->avatars[$idEmployee] = $employee->getImage();
            } else {
                $this->avatars[$idEmployee] = $this->avatarProvider->getDefaultAvatarUrl();
            }
        }

        return $this->avatars[$idEmployee];
    }
}
