<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

/**
 * Class DateRangeCore.
 */
class DateRangeCore extends ObjectModel
{
    /** @var string */
    public $time_start;

    /** @var string */
    public $time_end;

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = [
        'table' => 'date_range',
        'primary' => 'id_date_range',
        'fields' => [
            'time_start' => ['type' => self::TYPE_DATE, 'validate' => 'isDate', 'required' => true],
            'time_end' => ['type' => self::TYPE_DATE, 'validate' => 'isDate', 'required' => true],
        ],
    ];

    /**
     * Get current range.
     *
     * @return mixed
     */
    public static function getCurrentRange()
    {
        $result = Db::getInstance()->getRow('
            SELECT `id_date_range`, `time_end`
            FROM `' . _DB_PREFIX_ . 'date_range`
            WHERE `time_end` = (
                SELECT MAX(`time_end`)
                FROM `' . _DB_PREFIX_ . 'date_range`
            )'
        );

        if (!isset($result['id_date_range']) ||
            strtotime($result['time_end']) < time()
        ) {
            // 1 día menos 1 segundo
            $rangeSize = 86399;

            $dateRange = new DateRange();
            $dateRange->time_start = date('Y-m-d');

            $start = new DateTime($dateRange->time_start);
            $start->modify('+' . $rangeSize . ' seconds');
            $dateRange->time_end = $start->format('Y-m-d H:i:s');

            $dateRange->add();

            return $dateRange->id;
        }

        return $result['id_date_range'];
    }
}
