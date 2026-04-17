<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

/**
 * @deprecated since 9.0 and will be removed in 10.0, this object model is no longer needed
 */
class StockMvtReasonCore extends ObjectModel
{
    /** @var int identifier of the movement reason */
    public $id;

    /** @var string the name of the movement reason */
    public $name;

    /** @var int detrmine if the movement reason correspond to a positive or negative operation */
    public $sign;

    /** @var string the creation date of the movement reason */
    public $date_add;

    /** @var string the last update date of the movement reason */
    public $date_upd;

    /** @var bool True if the movement reason has been deleted (staying in database as deleted) */
    public $deleted = false;

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = [
        'table' => 'stock_mvt_reason',
        'primary' => 'id_stock_mvt_reason',
        'multilang' => true,
        'fields' => [
            'sign' => ['type' => self::TYPE_INT],
            'deleted' => ['type' => self::TYPE_BOOL],
            'date_add' => ['type' => self::TYPE_DATE, 'validate' => 'isDate'],
            'date_upd' => ['type' => self::TYPE_DATE, 'validate' => 'isDate'],
            'name' => ['type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isGenericName', 'required' => true, 'size' => 255],
        ],
    ];

    /**
     * @see ObjectModel::$webserviceParameters
     */
    protected $webserviceParameters = [
        'objectsNodeName' => 'stock_movement_reasons',
        'objectNodeName' => 'stock_movement_reason',
        'fields' => [
            'sign' => [],
        ],
    ];

    /**
     * Gets Stock Mvt Reasons.
     *
     * @param int $id_lang
     * @param int $sign Optionnal
     *
     * @return array
     */
    public static function getStockMvtReasons($id_lang, $sign = null)
    {
        $query = new DbQuery();
        $query->select('smrl.name, smr.id_stock_mvt_reason, smr.sign');
        $query->from('stock_mvt_reason', 'smr');
        $query->leftjoin('stock_mvt_reason_lang', 'smrl', 'smr.id_stock_mvt_reason = smrl.id_stock_mvt_reason AND smrl.id_lang=' . (int) $id_lang);
        $query->where('smr.deleted = 0');

        if ($sign != null) {
            $query->where('smr.sign = ' . (int) $sign);
        }

        return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query);
    }

    /**
     * Same as StockMvtReason::getStockMvtReasons(), ignoring a specific lists of ids.
     *
     * @param int $id_lang
     * @param array $ids_ignore
     * @param int $sign optional
     */
    public static function getStockMvtReasonsWithFilter($id_lang, $ids_ignore, $sign = null)
    {
        $query = new DbQuery();
        $query->select('smrl.name, smr.id_stock_mvt_reason, smr.sign');
        $query->from('stock_mvt_reason', 'smr');
        $query->leftjoin('stock_mvt_reason_lang', 'smrl', 'smr.id_stock_mvt_reason = smrl.id_stock_mvt_reason AND smrl.id_lang=' . (int) $id_lang);
        $query->where('smr.deleted = 0');

        if ($sign != null) {
            $query->where('smr.sign = ' . (int) $sign);
        }

        if (count($ids_ignore)) {
            $ids_ignore = array_map('intval', $ids_ignore);
            $query->where('smr.id_stock_mvt_reason NOT IN(' . implode(', ', $ids_ignore) . ')');
        }

        return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query);
    }

    /**
     * For a given id_stock_mvt_reason, tells if it exists.
     *
     * @param int $id_stock_mvt_reason
     *
     * @return bool
     */
    public static function exists($id_stock_mvt_reason)
    {
        $query = new DbQuery();
        $query->select('smr.id_stock_mvt_reason');
        $query->from('stock_mvt_reason', 'smr');
        $query->where('smr.id_stock_mvt_reason = ' . (int) $id_stock_mvt_reason);
        $query->where('smr.deleted = 0');

        return Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($query);
    }
}
