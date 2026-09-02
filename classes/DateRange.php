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
		WHERE `time_end` = (SELECT MAX(`time_end`) FROM `' . _DB_PREFIX_ . 'date_range`)');
        if (!isset($result['id_date_range']) || strtotime($result['time_end']) < strtotime(date('Y-m-d H:i:s'))) {
            // The default range is set to 1 day less 1 second (in seconds)
            $rangeSize = 86399;
            $dateRange = new DateRange();
            $dateRange->time_start = date('Y-m-d');
            $dateRange->time_end = strftime('%Y-%m-%d %H:%M:%S', strtotime($dateRange->time_start) + $rangeSize);
            $dateRange->add();

            return $dateRange->id;
        }

        return $result['id_date_range'];
    }
}
