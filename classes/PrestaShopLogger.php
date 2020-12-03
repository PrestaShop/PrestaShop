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

/**
 * Class PrestaShopLoggerCore.
 */
class PrestaShopLoggerCore extends ObjectModel
{
    /** @var int Log id */
    public $id_log;

    /** @var int Log severity */
    public $severity;

    /** @var int Error code */
    public $error_code;

    /** @var string Message */
    public $message;

    /** @var string Object type (eg. Order, Customer...) */
    public $object_type;

    /** @var int Object ID */
    public $object_id;

    /** @var int Object ID */
    public $id_employee;

    /** @var string Object creation date */
    public $date_add;

    /** @var string Object last modification date */
    public $date_upd;

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = [
        'table' => 'log',
        'primary' => 'id_log',
        'fields' => [
            'severity' => ['type' => self::TYPE_INT, 'validate' => 'isInt', 'required' => true],
            'error_code' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'],
            'message' => ['type' => self::TYPE_STRING, 'validate' => 'isString', 'required' => true],
            'object_id' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'],
            'id_employee' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'],
            'object_type' => ['type' => self::TYPE_STRING, 'validate' => 'isName'],
            'date_add' => ['type' => self::TYPE_DATE, 'validate' => 'isDate'],
            'date_upd' => ['type' => self::TYPE_DATE, 'validate' => 'isDate'],
        ],
    ];

    protected static $is_present = [];

    /**
     * Send e-mail to the shop owner only if the minimal severity level has been reached.
     *
     * @param Logger
     * @param PrestaShopLogger $log
     */
    public static function sendByMail($log)
    {
        if ((int) Configuration::get('PS_LOGS_BY_EMAIL') <= (int) $log->severity) {
            $language = new Language((int) Configuration::get('PS_LANG_DEFAULT'));
            Mail::Send(
                (int) Configuration::get('PS_LANG_DEFAULT'),
                'log_alert',
                Context::getContext()->getTranslator()->trans(
                    'Log: You have a new alert from your shop',
                    [],
                    'Emails.Subject',
                    $language->locale
                ),
                [],
                Configuration::get('PS_SHOP_EMAIL')
            );
        }
    }

    /**
     * add a log item to the database and send a mail if configured for this $severity.
     *
     * @param string $message the log message
     * @param int $severity
     * @param int $errorCode
     * @param string $objectType
     * @param int $objectId
     * @param bool $allowDuplicate if set to true, can log several time the same information (not recommended)
     *
     * @return bool true if succeed
     */
    public static function addLog($message, $severity = 1, $errorCode = null, $objectType = null, $objectId = null, $allowDuplicate = false, $idEmployee = null)
    {
        $log = new PrestaShopLogger();
        $log->severity = (int) $severity;
        $log->error_code = (int) $errorCode;
        $log->message = pSQL($message);
        $log->date_add = date('Y-m-d H:i:s');
        $log->date_upd = date('Y-m-d H:i:s');

        if ($idEmployee === null && isset(Context::getContext()->employee) && Validate::isLoadedObject(Context::getContext()->employee)) {
            $idEmployee = Context::getContext()->employee->id;
        }

        if ($idEmployee !== null) {
            $log->id_employee = (int) $idEmployee;
        }

        if (!empty($objectType) && !empty($objectId)) {
            $log->object_type = pSQL($objectType);
            $log->object_id = (int) $objectId;
        }

        if ($objectType != 'Swift_Message') {
            PrestaShopLogger::sendByMail($log);
        }

        if ($allowDuplicate || !$log->_isPresent()) {
            $res = $log->add();
            if ($res) {
                self::$is_present[$log->getHash()] = isset(self::$is_present[$log->getHash()]) ? self::$is_present[$log->getHash()] + 1 : 1;

                return true;
            }
        }

        return false;
    }

    /**
     * this function md5($this->message.$this->severity.$this->error_code.$this->object_type.$this->object_id).
     *
     * @return string hash
     */
    public function getHash()
    {
        if (empty($this->hash)) {
            $this->hash = md5($this->message . $this->severity . $this->error_code . $this->object_type . $this->object_id);
        }

        return $this->hash;
    }

    public static function eraseAllLogs()
    {
        return Db::getInstance()->execute('TRUNCATE TABLE ' . _DB_PREFIX_ . 'log');
    }

    /**
     * @deprecated 1.7.0
     */
    protected function _isPresent()
    {
        return $this->isPresent();
    }

    /**
     * check if this log message already exists in database.
     *
     * @return true if exists
     *
     * @since 1.7.0
     */
    protected function isPresent()
    {
        if (!isset(self::$is_present[md5($this->message)])) {
            self::$is_present[$this->getHash()] = Db::getInstance()->getValue('SELECT COUNT(*)
				FROM `' . _DB_PREFIX_ . 'log`
				WHERE
					`message` = \'' . $this->message . '\'
					AND `severity` = \'' . $this->severity . '\'
					AND `error_code` = \'' . $this->error_code . '\'
					AND `object_type` = \'' . $this->object_type . '\'
					AND `object_id` = \'' . $this->object_id . '\'
				');
        }

        return self::$is_present[$this->getHash()];
    }
}
