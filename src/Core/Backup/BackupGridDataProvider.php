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
use PrestaShop\PrestaShop\Core\Grid\Search\SearchCriteriaInterface;

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
     * @param BackupProviderInterface $backupProvider
     */
    public function __construct(BackupProviderInterface $backupProvider)
    {
        $this->backupProvider = $backupProvider;
    }

    /**
     * {@inheritdoc}
     */
    public function getData(SearchCriteriaInterface $searchCriteria)
    {
        $backups = $this->backupProvider->getBackups();

        $paginatedBackups = null !== $searchCriteria->getOffset() && null !== $searchCriteria->getLimit() ?
            array_slice($backups, $searchCriteria->getOffset(), $searchCriteria->getLimit()) :
            $backups;

        return new GridData(
            $paginatedBackups,
            count($backups)
        );
    }
}
