<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

/**
 * Class AliasCore.
 */
class AliasCore extends ObjectModel
{
    public $alias;
    public $search;
    public $active = true;

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = [
        'table' => 'alias',
        'primary' => 'id_alias',
        'fields' => [
            'search' => ['type' => self::TYPE_STRING, 'validate' => 'isValidSearch', 'required' => true, 'size' => 255],
            'alias' => ['type' => self::TYPE_STRING, 'validate' => 'isValidSearch', 'required' => true, 'size' => 191],
            'active' => ['type' => self::TYPE_BOOL, 'validate' => 'isBool'],
        ],
    ];

    /**
     * AliasCore constructor.
     *
     * @param int|null $id Alias ID
     * @param string|null $alias Alias
     * @param string|null $search Search string
     */
    public function __construct($id = null, $alias = null, $search = null)
    {
        $this->def = Alias::getDefinition($this);

        if ($id) {
            parent::__construct($id);
        } elseif ($alias && Validate::isValidSearch($alias)) {
            if (!Alias::isFeatureActive()) {
                $this->alias = trim($alias);
                $this->search = trim($search);
            } else {
                $row = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow('
				SELECT a.id_alias, a.search, a.alias
				FROM `' . _DB_PREFIX_ . 'alias` a
				WHERE `alias` = \'' . pSQL($alias) . '\' AND `active` = 1');

                if ($row) {
                    $this->id = (int) $row['id_alias'];
                    $this->search = $search ? trim($search) : $row['search'];
                    $this->alias = $row['alias'];
                } else {
                    $this->alias = trim($alias);
                    $this->search = trim($search);
                }
            }
        }
    }

    /**
     * @see ObjectModel::add();
     */
    public function add($autoDate = true, $nullValues = false)
    {
        $this->alias = Tools::replaceAccentedChars($this->alias);
        $this->search = Tools::replaceAccentedChars($this->search);

        if (parent::add($autoDate, $nullValues)) {
            // Set cache of feature detachable to true
            Configuration::updateGlobalValue('PS_ALIAS_FEATURE_ACTIVE', '1');

            return true;
        }

        return false;
    }

    /**
     * @see ObjectModel::delete();
     */
    public function delete()
    {
        if (parent::delete()) {
            // Refresh cache of feature detachable
            Configuration::updateGlobalValue('PS_ALIAS_FEATURE_ACTIVE', Alias::isCurrentlyUsed($this->def['table'], true));

            return true;
        }

        return false;
    }

    /**
     * Get all found aliases from DB with search query.
     *
     * @return string Comma separated aliases
     */
    public function getAliases()
    {
        if (!Alias::isFeatureActive()) {
            return '';
        }

        $aliases = Db::getInstance()->executeS('
		SELECT a.alias
		FROM `' . _DB_PREFIX_ . 'alias` a
		WHERE `search` = \'' . pSQL($this->search) . '\'');

        $aliases = array_map('implode', $aliases);

        return implode(', ', $aliases);
    }

    /**
     * This method is allowed to know if a feature is used or active.
     *
     * @return bool
     */
    public static function isFeatureActive()
    {
        return Configuration::get('PS_ALIAS_FEATURE_ACTIVE');
    }

    /**
     * This method is allowed to know if an alias exist for AdminImportController.
     *
     * @param int $idAlias Alias ID
     *
     * @return bool
     */
    public static function aliasExists($idAlias)
    {
        $sql = new DbQuery();
        $sql->select('a.`id_alias`');
        $sql->from('alias', 'a');
        $sql->where('a.`id_alias` = ' . (int) $idAlias);
        $row = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($sql, false);

        return isset($row['id_alias']);
    }
}
