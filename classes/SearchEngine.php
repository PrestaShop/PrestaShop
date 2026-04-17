<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

/**
 * Class SearchEngineCore.
 */
class SearchEngineCore extends ObjectModel
{
    public $server;
    public $getvar;

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = [
        'table' => 'search_engine',
        'primary' => 'id_search_engine',
        'fields' => [
            'server' => ['type' => self::TYPE_STRING, 'validate' => 'isUrl', 'required' => true, 'size' => 64],
            'getvar' => ['type' => self::TYPE_STRING, 'validate' => 'isModuleName', 'required' => true, 'size' => 16],
        ],
    ];

    /**
     * Get keywords.
     *
     * @param string $url
     *
     * @return bool|string
     */
    public static function getKeywords($url)
    {
        $parsedUrl = @parse_url($url);
        if (!isset($parsedUrl['host']) || !isset($parsedUrl['query'])) {
            return false;
        }
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('SELECT `server`, `getvar` FROM `' . _DB_PREFIX_ . 'search_engine`');
        foreach ($result as $row) {
            $host = &$row['server'];
            $varname = &$row['getvar'];
            if (strstr($parsedUrl['host'], $host)) {
                $array = [];
                preg_match('/[^a-z]' . $varname . '=.+\&/U', $parsedUrl['query'], $array);
                if (empty($array[0])) {
                    preg_match('/[^a-z]' . $varname . '=.+$/', $parsedUrl['query'], $array);
                }
                if (empty($array[0])) {
                    return false;
                }
                $str = urldecode(str_replace('+', ' ', ltrim(substr(rtrim($array[0], '&'), strlen($varname) + 1), '=')));
                if (!Validate::isMessage($str)) {
                    return false;
                }

                return $str;
            }
        }

        return '';
    }
}
