<?php
/**
 * 2007-2018 PrestaShop
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
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Core\Backup;

use PrestaShop\PrestaShop\Core\Grid\DataProvider\GridData;
use PrestaShop\PrestaShop\Core\Grid\DataProvider\GridDataProviderInterface;
use PrestaShop\PrestaShop\Core\Grid\Row\RowCollection;
use PrestaShop\PrestaShop\Core\Grid\Search\SearchCriteriaInterface;
use PrestaShop\PrestaShop\Core\Util\DateTime\TimeDefinition;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Class BackupGridDataProvider provides backups for listing in grid
 */
final class BackupGridDataProvider implements GridDataProviderInterface
{
    /**
     * @var BackupProviderInterface
     */
    private $backupProvider;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var string
     */
    private $languageDateTimeFormat;

    /**
     * @param BackupProviderInterface $backupProvider
     * @param TranslatorInterface $translator
     * @param string $languageDateTimeFormat
     */
    public function __construct(
        BackupProviderInterface $backupProvider,
        TranslatorInterface $translator,
        $languageDateTimeFormat
    ) {
        $this->backupProvider = $backupProvider;
        $this->translator = $translator;
        $this->languageDateTimeFormat = $languageDateTimeFormat;
    }

    /**
     * {@inheritdoc}
     */
    public function getData(SearchCriteriaInterface $searchCriteria)
    {
        $backups = $this->backupProvider->getBackups();
        usort($backups, [$this, 'sortingCompare']);

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

        $backupCollection = new RowCollection($backupsArray);

        return new GridData(
            $backupCollection,
            count($backups)
        );
    }

    /**
     * Compare to backup records
     *
     * @param BackupInterface $backup1
     * @param BackupInterface $backup2
     *
     * @return bool
     */
    private function sortingCompare(BackupInterface $backup1, BackupInterface $backup2)
    {
        return $backup2->getDate()->getTimestamp() - $backup1->getDate()->getTimestamp();
    }

    /**
     * Get formatted age
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
     * Get formatted backup size
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
