<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
class TaxRulesGroupCore extends ObjectModel
{
    public $name;

    /** @var bool active state */
    public $active;

    public $deleted = 0;

    /** @var string Object creation date */
    public $date_add;

    /** @var string Object last modification date */
    public $date_upd;

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = [
        'table' => 'tax_rules_group',
        'primary' => 'id_tax_rules_group',
        'fields' => [
            'name' => ['type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'required' => true, 'size' => 64],
            'active' => ['type' => self::TYPE_BOOL, 'validate' => 'isBool'],
            'deleted' => ['type' => self::TYPE_BOOL, 'validate' => 'isBool'],
            'date_add' => ['type' => self::TYPE_DATE, 'validate' => 'isDate'],
            'date_upd' => ['type' => self::TYPE_DATE, 'validate' => 'isDate'],
        ],
    ];

    protected $webserviceParameters = [
        'objectsNodeName' => 'tax_rule_groups',
        'objectNodeName' => 'tax_rule_group',
        'fields' => [
        ],
    ];

    protected static $_taxes = [];

    /**
     * @param bool $null_values
     *
     * @return bool
     *
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    public function update($null_values = false)
    {
        if (!$this->deleted && $this->isUsed()) {
            $current_tax_rules_group = new TaxRulesGroup((int) $this->id);
            /** @var TaxRulesGroup|false $new_tax_rules_group */
            $new_tax_rules_group = $current_tax_rules_group->duplicateObject();
            if (!$new_tax_rules_group || !$current_tax_rules_group->historize($new_tax_rules_group)) {
                return false;
            }

            $this->id = (int) $new_tax_rules_group->id;
        }

        return parent::update($null_values);
    }

    /**
     * Save the object with the field deleted to true.
     *
     * @return bool
     */
    public function historize(TaxRulesGroup $tax_rules_group)
    {
        return $this->softDelete()
        && Db::getInstance()->execute('
		INSERT INTO ' . _DB_PREFIX_ . 'tax_rule
		(id_tax_rules_group, id_country, id_state, zipcode_from, zipcode_to, id_tax, behavior, description)
		(
			SELECT ' . (int) $tax_rules_group->id . ', id_country, id_state, zipcode_from, zipcode_to, id_tax, behavior, description
			FROM ' . _DB_PREFIX_ . 'tax_rule
			WHERE id_tax_rules_group=' . (int) $this->id . '
		)')
        && Db::getInstance()->execute('
		UPDATE ' . _DB_PREFIX_ . 'product
		SET id_tax_rules_group=' . (int) $tax_rules_group->id . '
		WHERE id_tax_rules_group=' . (int) $this->id)
        && Db::getInstance()->execute('
		UPDATE ' . _DB_PREFIX_ . 'product_shop
		SET id_tax_rules_group=' . (int) $tax_rules_group->id . '
		WHERE id_tax_rules_group=' . (int) $this->id)
        && Db::getInstance()->execute('
		UPDATE ' . _DB_PREFIX_ . 'carrier_tax_rules_group_shop
		SET id_tax_rules_group=' . (int) $tax_rules_group->id . '
		WHERE id_tax_rules_group=' . (int) $this->id);
    }

    public function getIdTaxRuleGroupFromHistorizedId($id_tax_rule)
    {
        $params = Db::getInstance()->getRow(
            '
		SELECT id_country, id_state, zipcode_from, zipcode_to, id_tax, behavior
		FROM ' . _DB_PREFIX_ . 'tax_rule
		WHERE id_tax_rule=' . (int) $id_tax_rule
        );

        return Db::getInstance()->getValue(
            '
		SELECT id_tax_rule
		FROM ' . _DB_PREFIX_ . 'tax_rule
		WHERE
			id_tax_rules_group = ' . (int) $this->id . ' AND
			id_country=' . (int) $params['id_country'] . ' AND id_state=' . (int) $params['id_state'] . ' AND id_tax=' . (int) $params['id_tax'] . ' AND
			zipcode_from=\'' . pSQL($params['zipcode_from']) . '\' AND zipcode_to=\'' . pSQL($params['zipcode_to']) . '\' AND behavior=' . (int) $params['behavior']
        );
    }

    public static function getTaxRulesGroups($only_active = true)
    {
        return self::getTaxRulesGroupsData($only_active);
    }

    /**
     * This method returns the list of TaxRulesGroup as array with default placeholder
     * it is used to populate a select box. The returned array is formatted like this:
     * [
     *   [
     *     'id_tax_rules_group' => ...,
     *     'name' => ...,
     *     'active' => ...,
     *     'rate' => ...,
     *   ],
     *   ...
     * ]
     *
     * @return array an array of tax rules group formatted as:
     */
    public static function getTaxRulesGroupsForOptions()
    {
        $tax_rules[] = [
            'id_tax_rules_group' => 0,
            'name' => Context::getContext()->getTranslator()->trans('No tax', [], 'Admin.International.Notification'),
        ];

        return array_merge($tax_rules, TaxRulesGroup::getTaxRulesGroupsData(true, true));
    }

    /**
     * @param bool $onlyActive Filter active tax rules group only
     * @param bool $includeRates Include tax rate amount in returned data
     *
     * @return array|false
     *
     * @throws PrestaShopDatabaseException
     */
    private static function getTaxRulesGroupsData($onlyActive = true, bool $includeRates = false)
    {
        $sql = 'SELECT DISTINCT g.id_tax_rules_group, g.name, g.active';

        if ($includeRates) {
            $sql .= ', t.rate';
        }

        $sql .= ' FROM `' . _DB_PREFIX_ . 'tax_rules_group` g';

        if ($includeRates) {
            $sql .= '
                INNER JOIN ' . _DB_PREFIX_ . 'tax_rule tr
                ON g.id_tax_rules_group = tr.id_tax_rules_group
                INNER JOIN ' . _DB_PREFIX_ . 'tax t
                ON (tr.id_tax = t.id_tax AND t.active = 1)
            ';
        }

        $sql .= Shop::addSqlAssociation('tax_rules_group', 'g') . ' WHERE g.deleted = 0'
            . ($onlyActive ? ' AND g.`active` = 1' : '')
            . ' ORDER BY name ASC';

        return Db::getInstance()->executeS($sql);
    }

    public function delete()
    {
        $res = Db::getInstance()->execute('DELETE FROM `' . _DB_PREFIX_ . 'tax_rule` WHERE `id_tax_rules_group`=' . (int) $this->id);

        return parent::delete() && $res;
    }

    /**
     * @return array
     */
    public static function getAssociatedTaxRatesByIdCountry($id_country)
    {
        $rows = Db::getInstance()->executeS(
            '
			SELECT rg.`id_tax_rules_group`, t.`rate`
			FROM `' . _DB_PREFIX_ . 'tax_rules_group` rg
			LEFT JOIN `' . _DB_PREFIX_ . 'tax_rule` tr ON (tr.`id_tax_rules_group` = rg.`id_tax_rules_group`)
			LEFT JOIN `' . _DB_PREFIX_ . 'tax` t ON (t.`id_tax` = tr.`id_tax`)
			WHERE tr.`id_country` = ' . (int) $id_country . '
			AND tr.`id_state` = 0
			AND 0 between `zipcode_from` AND `zipcode_to`'
        );

        $res = [];
        foreach ($rows as $row) {
            $res[$row['id_tax_rules_group']] = $row['rate'];
        }

        return $res;
    }

    /**
     * Returns the tax rules group id corresponding to the name.
     *
     * @param string $name
     *
     * @return int id of the tax rules
     */
    public static function getIdByName($name)
    {
        return (int) Db::getInstance()->getValue(
            'SELECT `id_tax_rules_group`
			FROM `' . _DB_PREFIX_ . 'tax_rules_group` rg
			WHERE `name` = \'' . pSQL($name) . '\'
            ORDER BY `active` DESC, `deleted` ASC'
        );
    }

    public function hasUniqueTaxRuleForCountry($id_country, $id_state, $id_tax_rule = false)
    {
        $rules = TaxRule::getTaxRulesByGroupId((int) Context::getContext()->language->id, (int) $this->id);
        foreach ($rules as $rule) {
            if ($rule['id_country'] == $id_country && $id_state == $rule['id_state'] && !$rule['behavior'] && (int) $id_tax_rule != $rule['id_tax_rule']) {
                return true;
            }
        }

        return false;
    }

    public function isUsed()
    {
        return Db::getInstance()->getValue(
            '
		SELECT `id_tax_rules_group`
		FROM `' . _DB_PREFIX_ . 'order_detail`
		WHERE `id_tax_rules_group` = ' . (int) $this->id
        );
    }
}
