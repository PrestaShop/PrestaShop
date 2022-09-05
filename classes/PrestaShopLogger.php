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
    /**
     * List of log level types.
     */
    public const LOG_SEVERITY_LEVEL_INFORMATIVE = 1;
    public const LOG_SEVERITY_LEVEL_WARNING = 2;
    public const LOG_SEVERITY_LEVEL_ERROR = 3;
    public const LOG_SEVERITY_LEVEL_MAJOR = 4;

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

    /** @var int Employee ID */
    public $id_employee;

    /** @var string Object creation date */
    public $date_add;

    /** @var string Object last modification date */
    public $date_upd;

    /** @var int|null Shop ID */
    public $id_shop;

    /** @var int|null Shop group ID */
    public $id_shop_group;

    /** @var int|null Language ID */
    public $id_lang;

    /** @var bool In all shops */
    public $in_all_shops;

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
            'id_shop' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedInt', 'allow_null' => true],
            'id_shop_group' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedInt', 'allow_null' => true],
            'id_lang' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedInt', 'allow_null' => true],
            'in_all_shops' => ['type' => self::TYPE_BOOL, 'validate' => 'isBool'],
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
     * @param PrestaShopLogger $log
     */
    public static function sendByMail($log)
    {
        $config_severity = (int) Configuration::get('PS_LOGS_BY_EMAIL');
        if (!empty($config_severity) && $config_severity <= (int) $log->severity) {
            $to = array_map('trim', explode(',', Configuration::get('PS_LOGS_EMAIL_RECEIVERS')));
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
                $to
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
        $log->message = $message;
        $log->date_add = date('Y-m-d H:i:s');
        $log->date_upd = date('Y-m-d H:i:s');

        $context = Context::getContext();

        if ($idEmployee === null && isset($context->employee->id)) {
            $idEmployee = $context->employee->id;
        }

        if ($idEmployee !== null) {
            $log->id_employee = (int) $idEmployee;
        }

        if (!empty($objectType)) {
            $log->object_type = $objectType;
            if (!empty($objectId)) {
                $log->object_id = (int) $objectId;
            }
        }

        $log->id_lang = $context->language ? (int) $context->language->id : null;
        $log->in_all_shops = Shop::getContext() == Shop::CONTEXT_ALL;
        $log->id_shop = Shop::getContext() == Shop::CONTEXT_SHOP ? (int) $context->shop->getContextualShopId() : null;
        $log->id_shop_group = Shop::getContext() == Shop::CONTEXT_GROUP ? (int) $context->shop->getContextShopGroupID() : null;

        if ($objectType != 'SwiftMessage') {
            PrestaShopLogger::sendByMail($log);
        }

        if ($allowDuplicate || !$log->isPresent()) {
            $res = $log->add();
            if ($res) {
                self::$is_present[$log->getHash()] = isset(self::$is_present[$log->getHash()]) ? self::$is_present[$log->getHash()] + 1 : 1;

                return true;
            }
        }

        return false;
    }

    /**
     * @return string hash
     */
    public function getHash()
    {
        if (empty($this->hash)) {
            $this->hash = md5(
                $this->message .
                $this->severity .
                $this->error_code .
                $this->object_type .
                $this->object_id .
                $this->id_shop .
                $this->id_shop_group .
                $this->id_lang .
                $this->in_all_shops
            );
        }

        return $this->hash;
    }

    public static function eraseAllLogs()
    {
        return Db::getInstance()->execute('TRUNCATE TABLE ' . _DB_PREFIX_ . 'log');
    }

    /**
     * check if this log message already exists in database.
     *
     * @return bool true if exists
     *
     * @since 1.7.0
     */
    protected function isPresent()
    {
        if (!isset(self::$is_present[md5($this->message)])) {
            self::$is_present[$this->getHash()] = Db::getInstance()->getValue(
                (new DbQuery())
                    ->select('COUNT(*)')
                    ->from('log', 'l')
                    ->where('message = "' . pSQL($this->message) . '"')
                    ->where('severity = ' . (int) $this->severity)
                    ->where('error_code = ' . (int) $this->error_code)
                    ->where('object_type = "' . pSQL($this->object_type) . '"')
                    ->where('object_id = ' . (int) $this->object_id)
                    ->where('id_shop = ' . (int) $this->id_shop)
                    ->where('id_shop_group = ' . (int) $this->id_shop_group)
                    ->where('id_lang = ' . (int) $this->id_lang)
                    ->where('in_all_shops = ' . (int) $this->in_all_shops)
            );
        }

        return self::$is_present[$this->getHash()];
    }
}
