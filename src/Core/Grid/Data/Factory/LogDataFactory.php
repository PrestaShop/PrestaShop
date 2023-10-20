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
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
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
