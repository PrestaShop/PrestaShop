<?php
/**
 * 2007-2015 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
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
 *  @author 	PrestaShop SA <contact@prestashop.com>
 *  @copyright  2007-2015 PrestaShop SA
 *  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *  @since		1.6.0
 *  International Registered Trademark & Property of PrestaShop SA
 */

class ResetTokenCore extends ObjectModel
{
    /** @var int Customer id */
    public $id_customer = null;

    /** @var string Customer Secure key */
    public $secure_key;

    /** @var int Employee id */
    public $id_employee = null;

    /** @var string Unique token */
    public $unique_token;

    /** @var string Reset token creation date */
    public $last_token_gen;

    /** @var string token validity date */
    public $validity_date;

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table' => 'reset_token',
        'primary' => 'id_reset_token',
        'fields' => array(
            'id_customer' =>        array('type' => self::TYPE_INT, 'validate' => 'isNullOrUnsignedId', 'copy_post' => false),
            'secure_key' =>            array('type' => self::TYPE_STRING, 'copy_post' => false),
            'id_employee' =>        array('type' => self::TYPE_INT, 'validate' => 'isNullOrUnsignedId', 'copy_post' => false),
            'unique_token' =>        array('type' => self::TYPE_STRING, 'validate' => 'isSha1', 'size' => 40, 'copy_post' => false),
            'last_token_gen' =>        array('type' => self::TYPE_DATE, 'validate' => 'isDate', 'copy_post' => false),
            'validity_date' =>        array('type' => self::TYPE_DATE, 'validate' => 'isDate', 'copy_post' => false),
        ),
    );

    /**
     * Build a reset token
     *
     * @param int $id_reset_token Existing reset token id in order to load object (optional)
     */
    public function __construct($id_reset_token = null, $id_lang = null, $id_shop = null)
    {
        parent::__construct($id_reset_token);
    }

    public function add($autodate = true, $null_values = true)
    {
        // check either id_customer+secure_key OR id_employee present. We need one or the other (both are not possible).
        if (!(Validate::isUnsignedId($this->id_customer) && Validate::isMd5($this->secure_key)) && !Validate::isUnsignedId($this->id_employee)) {
            return false;
        }

        // check if last_token_gen < validity_date
        if (strtotime($this->validity_date) < strtotime($this->last_token_gen)) {
            return false;
        }

        $success = parent::add($autodate, $null_values);
        return $success;
    }

    /**
     * Fill unique token with random sha1, fill dates too
     */
    public function stampToken()
    {
        $salt = $this->id_customer? '-'.$this->id_customer.'-'.$this->secure_key : '+'.$this->id_employee.'+'.uniqid(rand(), true);
        $this->unique_token = sha1(time().$salt);
        $this->last_token_gen = date('Y-m-d H:i:s');
        $this->validity_date = date('Y-m-d H:i:s', strtotime('+1 day')); // TODO validity to admin setting?
    }

    /**
     * Return the most recent Reset token if already exists in the database for the given customer and generated after the given timestamp.
     * @param int $id_customer
     * @param int $min_timestamp The minimal timestamp value for generation date.
     */
    public function getMostRecentTokenForCustomer($id_customer, $min_timestamp = false)
    {
        $sql = 'SELECT *
				FROM `'._DB_PREFIX_.'reset_token`
				WHERE `id_customer` = '.pSQL($id_customer);

        if ($min_timestamp !== false) {
            $sql .= ' AND `last_token_gen` >= \''.date('Y-m-d H:i:s', $min_timestamp);
        }

        $sql .= '\' ORDER BY `last_token_gen` DESC';

        $result = Db::getInstance()->getRow($sql);
        if (!$result) {
            return false;
        }

        $this->id = $result['id_reset_token'];
        foreach ($result as $key => $value) {
            if (property_exists($this, $key)) {
                $this->{$key} = $value;
            }
        }
        return $this;
    }

    /**
     * Return the most recent AND valid Reset token, for the given customer AND unique_token given
     * @param int $id_customer
     * @param string $unique_token
     */
    public static function getValidByIdCustomerAndUniqueToken($id_customer, $unique_token)
    {
        $sql = 'SELECT *
				FROM `'._DB_PREFIX_.'reset_token`
				WHERE `id_customer` = '.pSQL($id_customer).' AND `unique_token` = \''.$unique_token.'\'
				AND `validity_date` >= NOW()
				ORDER BY `last_token_gen` DESC';

        $result = Db::getInstance()->getRow($sql);
        if (!$result) {
            return false;
        }

        $new = new static(); // http://stackoverflow.com/questions/5197300/new-self-vs-new-static
        $new->id = $result['id_reset_token'];
        foreach ($result as $key => $value) {
            if (property_exists($new, $key)) {
                $new->{$key} = $value;
            }
        }
        return $new;
    }
}
