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

namespace PrestaShop\PrestaShop\Core\Backup\Listing;

use PrestaShop\PrestaShop\Core\Backup\BackupInterface;
use PrestaShop\PrestaShop\Core\Backup\Comparator\BackupComparatorInterface;
use PrestaShop\PrestaShop\Core\Backup\Repository\BackupRepositoryInterface;
use PrestaShop\PrestaShop\Core\Grid\Data\Factory\GridDataFactoryInterface;
use PrestaShop\PrestaShop\Core\Grid\Data\GridData;
use PrestaShop\PrestaShop\Core\Grid\Record\RecordCollection;
use PrestaShop\PrestaShop\Core\Grid\Search\SearchCriteriaInterface;
use PrestaShop\PrestaShop\Core\Util\DateTime\TimeDefinition;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * This class provides backups for listing in grid.
 */
final class BackupGridDataFactory implements GridDataFactoryInterface
{
    /**
     * @var BackupRepositoryInterface
     */
    private $backupRepository;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var string
     */
    private $languageDateTimeFormat;

    /**
     * @var BackupComparatorInterface
     */
    private $backupByDateComparator;

    /**
     * @param BackupRepositoryInterface $backupRepository
     * @param BackupComparatorInterface $backupByDateComparator
     * @param TranslatorInterface $translator
     * @param string $languageDateTimeFormat
     */
    public function __construct(
        BackupRepositoryInterface $backupRepository,
        BackupComparatorInterface $backupByDateComparator,
        TranslatorInterface $translator,
        $languageDateTimeFormat
    ) {
        $this->backupRepository = $backupRepository;
        $this->translator = $translator;
        $this->languageDateTimeFormat = $languageDateTimeFormat;
        $this->backupByDateComparator = $backupByDateComparator;
    }

    /**
     * {@inheritdoc}
     */
    public function getData(SearchCriteriaInterface $searchCriteria)
    {
        $backups = $this->backupRepository->retrieveBackups()->all();
        usort($backups, [$this->backupByDateComparator, 'compare']);

        $paginatedBackups = null !== $searchCriteria->getOffset() && null !== $searchCriteria->getLimit() ?
            array_slice($backups, $searchCriteria->getOffset(), $searchCriteria->getLimit()) :
            $backups;

        $backupsArray = [];

        foreach ($paginatedBackups as $backup) {
            $backupsArray[] = [
                'file_name' => $backup->getFileName(),
                'file_size' => $backup->getSize(),
                'date' => $backup->getDate(),
                'age' => $backup->getAge(),
                'age_formatted' => $this->getFormattedAge($backup),
                'date_formatted' => date($this->languageDateTimeFormat, $backup->getDate()->getTimestamp()),
                'file_size_formatted' => $this->getFormattedSize($backup),
            ];
        }

        $backupCollection = new RecordCollection($backupsArray);

        return new GridData(
            $backupCollection,
            count($backups)
        );
    }

    /**
     * Get formatted age.
     *
     * @param BackupInterface $backup
     *
     * @return string
     */
    private function getFormattedAge(BackupInterface $backup)
    {
        if (TimeDefinition::HOUR_IN_SECONDS > $backup->getAge()) {
            return sprintf('< 1 %s', $this->translator->trans('Hour', [], 'Admin.Global'));
        }

        if (TimeDefinition::DAY_IN_SECONDS > $backup->getAge()) {
            $hours = (int) floor($backup->getAge() / TimeDefinition::HOUR_IN_SECONDS);
            $label = 1 === $hours ?
                $this->translator->trans('Hour', [], 'Admin.Global') :
                $this->translator->trans('Hours', [], 'Admin.Global');

            return sprintf('%s %s', $hours, $label);
        }

        $days = (int) floor($backup->getAge() / TimeDefinition::DAY_IN_SECONDS);
        $label = 1 === $days ?
            $this->translator->trans('Day', [], 'Admin.Global') :
            $this->translator->trans('Days', [], 'Admin.Global');

        return sprintf('%s %s', $days, $label);
    }

    /**
     * Get formatted backup size.
     *
     * @param BackupInterface $backup
     *
     * @return string
     */
    private function getFormattedSize(BackupInterface $backup)
    {
        return sprintf('%s Kb', number_format($backup->getSize() / 1000, 2));
    }
}
