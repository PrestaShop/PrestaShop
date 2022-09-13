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
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */
class TaxCore extends ObjectModel
{
    public const TAX_DEFAULT_PRECISION = 3;

    /** @var array<int,string>|string Name */
    public $name;

    /** @var float Rate (%) */
    public $rate;

    /** @var bool active state */
    public $active;

    /** @var bool true if the tax has been historized */
    public $deleted = false;

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = [
        'table' => 'tax',
        'primary' => 'id_tax',
        'multilang' => true,
        'fields' => [
            'rate' => ['type' => self::TYPE_FLOAT, 'validate' => 'isFloat', 'required' => true],
            'active' => ['type' => self::TYPE_BOOL],
            'deleted' => ['type' => self::TYPE_BOOL],
            /* Lang fields */
            'name' => ['type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isGenericName', 'required' => true, 'size' => 32],
        ],
    ];

    protected static $_product_country_tax = [];
    protected static $_product_tax_via_rules = [];

    protected $webserviceParameters = [
        'objectsNodeName' => 'taxes',
    ];

    public function delete()
    {
        /* Clean associations */
        TaxRule::deleteTaxRuleByIdTax((int) $this->id);

        if ($this->isUsed()) {
            return $this->historize();
        } else {
            return parent::delete();
        }
    }

    /**
     * Save the object with the field deleted to true.
     *
     * @return bool
     */
    public function historize()
    {
        $this->deleted = true;

        return parent::update();
    }

    public function toggleStatus()
    {
        if (parent::toggleStatus()) {
            return $this->_onStatusChange();
        }

        return false;
    }

    public function update($null_values = false)
    {
        if (!$this->deleted && $this->isUsed()) {
            $historized_tax = new Tax($this->id);
            $historized_tax->historize();

            // remove the id in order to create a new object
            $this->id = 0;
            $res = $this->add();

            // change tax id in the tax rule table
            $res = $res && TaxRule::swapTaxId($historized_tax->id, $this->id);

            return $res;
        } elseif (parent::update($null_values)) {
            return $this->_onStatusChange();
        }

        return false;
    }

    protected function _onStatusChange()
    {
        if (!$this->active) {
            return TaxRule::deleteTaxRuleByIdTax($this->id);
        }

        return true;
    }

    /**
     * Returns true if the tax is used in an order details.
     *
     * @return bool
     */
    public function isUsed()
    {
        return Db::getInstance()->getValue(
            '
		SELECT `id_tax`
		FROM `' . _DB_PREFIX_ . 'order_detail_tax`
		WHERE `id_tax` = ' . (int) $this->id
        );
    }

    /**
     * Get all available taxes.
     *
     * @param int|bool $id_lang
     * @param bool $active_only (true by default)
     *
     * @return array Taxes
     */
    public static function getTaxes($id_lang = false, $active_only = true)
    {
        $sql = new DbQuery();
        $sql->select('t.id_tax, t.rate');
        $sql->from('tax', 't');
        $sql->where('t.`deleted` != 1');

        if ($id_lang) {
            $sql->select('tl.name, tl.id_lang');
            $sql->leftJoin('tax_lang', 'tl', 't.`id_tax` = tl.`id_tax` AND tl.`id_lang` = ' . (int) $id_lang);
            $sql->orderBy('`name` ASC');
        }

        if ($active_only) {
            $sql->where('t.`active` = 1');
        }

        return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
    }

    public static function excludeTaxeOption()
    {
        return !Configuration::get('PS_TAX');
    }

    /**
     * Return the tax id associated to the specified name.
     *
     * @param string $tax_name
     * @param bool|int $active (true by default)
     */
    public static function getTaxIdByName($tax_name, $active = 1)
    {
        $tax = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow('
			SELECT t.`id_tax`
			FROM `' . _DB_PREFIX_ . 'tax` t
			LEFT JOIN `' . _DB_PREFIX_ . 'tax_lang` tl ON (tl.id_tax = t.id_tax)
			WHERE tl.`name` = \'' . pSQL($tax_name) . '\' ' .
            ($active == 1 ? ' AND t.`active` = 1' : ''));

        return $tax ? (int) $tax['id_tax'] : false;
    }

    /**
     * Returns the ecotax tax rate.
     *
     * @param int $id_address
     *
     * @return float $tax_rate
     */
    public static function getProductEcotaxRate($id_address = null)
    {
        $address = Address::initialize($id_address);

        $tax_manager = TaxManagerFactory::getManager($address, (int) Configuration::get('PS_ECOTAX_TAX_RULES_GROUP_ID'));
        $tax_calculator = $tax_manager->getTaxCalculator();

        return $tax_calculator->getTotalRate();
    }

    /**
     * Returns the carrier tax rate.
     *
     * @param int $id_carrier
     * @param int $id_address
     *
     * @return float $tax_rate
     */
    public static function getCarrierTaxRate($id_carrier, $id_address = null)
    {
        $address = Address::initialize($id_address);
        $id_tax_rules = (int) Carrier::getIdTaxRulesGroupByIdCarrier((int) $id_carrier);

        $tax_manager = TaxManagerFactory::getManager($address, $id_tax_rules);
        $tax_calculator = $tax_manager->getTaxCalculator();

        return $tax_calculator->getTotalRate();
    }

    /**
     * Returns the product tax rate.
     *
     * @param int $id_product
     * @param int $id_address
     * @param Context $context
     *
     * @return float
     */
    public static function getProductTaxRate($id_product, $id_address = null, Context $context = null)
    {
        if ($context == null) {
            $context = Context::getContext();
        }

        $address = Address::initialize($id_address);
        $id_tax_rules = (int) Product::getIdTaxRulesGroupByIdProduct($id_product, $context);

        $tax_manager = TaxManagerFactory::getManager($address, $id_tax_rules);
        $tax_calculator = $tax_manager->getTaxCalculator();

        return $tax_calculator->getTotalRate();
    }
}
