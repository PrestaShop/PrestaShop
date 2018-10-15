<?php
/**
 * 2007-2018 PrestaShop.
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

namespace PrestaShop\PrestaShop\Core\Grid\Data;

use PrestaShop\PrestaShop\Core\Grid\Record\RecordCollectionInterface;

/**
 * Class GridData is responsible for storing grid data.
 */
final class GridData implements GridDataInterface
{
    /**
     * @var array
     */
    private $records;

    /**
     * @var int
     */
    private $recordsTotal;

    /**
     * @var string
     */
    private $query;

    /**
     * @param RecordCollectionInterface $records Filtered & paginated rows data
     * @param int $recordsTotal Total number of rows (without pagination)
     * @param string $query Query used to get rows
     */
    public function __construct(RecordCollectionInterface $records, $recordsTotal, $query = '')
    {
        $this->records = $records;
        $this->recordsTotal = $recordsTotal;
        $this->query = $query;
    }

    /**
     * {@inheritdoc}
     */
    public function getRecords()
    {
        return $this->records;
    }

    /**
     * {@inheritdoc}
     */
    public function getRecordsTotal()
    {
        return $this->recordsTotal;
    }

    /**
     * {@inheritdoc}
     */
    public function getQuery()
    {
        return $this->query;
    }
}
