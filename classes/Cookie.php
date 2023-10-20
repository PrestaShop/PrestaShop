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
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */
use Defuse\Crypto\Key;
use PrestaShop\PrestaShop\Core\Exception\CoreException;
use PrestaShop\PrestaShop\Core\Session\SessionInterface;

/**
 * @property bool $detect_language
 * @property int $id_customer
 * @property int $id_employee
 * @property int $id_lang
 * @property int $id_guest
 * @property int|null $id_connections
 * @property bool $is_guest
 * @property bool $logged
 * @property string $passwd
 * @property int $session_id
 * @property string $session_token
 * @property string $shopContext
 * @property int $last_activity
 */
class CookieCore
{
    public const SAMESITE_NONE = 'None';
    public const SAMESITE_LAX = 'Lax';
    public const SAMESITE_STRICT = 'Strict';

    public const SAMESITE_AVAILABLE_VALUES = [
        self::SAMESITE_NONE => self::SAMESITE_NONE,
        self::SAMESITE_LAX => self::SAMESITE_LAX,
        self::SAMESITE_STRICT => self::SAMESITE_STRICT,
    ];

    /** @var array Contain cookie content in a key => value format */
    protected $_content = [];

    /** @var string Crypted cookie name for setcookie() */
    protected $_name;

    /** @var int expiration date for setcookie() */
    protected $_expire;

    /** @var bool|string Website domain for setcookie() */
    protected $_domain;

    /** @var string|bool SameSite for setcookie() */
    protected $_sameSite;

    /** @var string Path for setcookie() */
    protected $_path;

    /** @var PhpEncryption cipher tool instance */
    protected $cipherTool;

    protected $_modified = false;

    protected $_allow_writing;

    protected $_salt;

    protected $_standalone;

    /** @var bool */
    protected $_secure = false;

    /** @var SessionInterface|null */
    protected $session = null;

    /**
     * Get data if the cookie exists and else initialize an new one.
     *
     * @param string $name Cookie name before encrypting
     * @param string $path
     */
    public function __construct($name, $path = '', $expire = null, $shared_urls = null, $standalone = false, $secure = false)
    {
        $this->_content = [];
        $this->_standalone = $standalone;
        $this->_expire = null === $expire ? time() + 1728000 : (int) $expire;
        $this->_path = trim(($this->_standalone ? '' : Context::getContext()->shop->physical_uri) . $path, '/\\') . '/';
        if ($this->_path[0] != '/') {
            $this->_path = '/' . $this->_path;
        }
        $this->_path = rawurlencode($this->_path);
        $this->_path = str_replace(['%2F', '%7E', '%2B', '%26'], ['/', '~', '+', '&'], $this->_path);
        $this->_domain = $this->getDomain($shared_urls);
        $this->_sameSite = Configuration::get('PS_COOKIE_SAMESITE');
        $this->_name = 'PrestaShop-' . md5(($this->_standalone ? '' : _PS_VERSION_) . $name . $this->_domain);
        $this->_allow_writing = true;
        $this->_salt = $this->_standalone ? str_pad('', 32, md5('ps' . __FILE__)) : _COOKIE_IV_;

        if ($this->_standalone) {
            $asciiSafeString = \Defuse\Crypto\Encoding::saveBytesToChecksummedAsciiSafeString(Key::KEY_CURRENT_VERSION, str_pad($name, Key::KEY_BYTE_SIZE, md5(__FILE__)));
            $this->cipherTool = new PhpEncryption($asciiSafeString);
        } else {
            $this->cipherTool = new PhpEncryption(_NEW_COOKIE_KEY_);
        }

        $this->_secure = (bool) $secure;

        $this->update();
    }

    public function disallowWriting()
    {
        $this->_allow_writing = false;
    }

    /**
     * @param array|null $shared_urls
     *
     * @return bool|string
     */
    protected function getDomain($shared_urls = null)
    {
        $httpHost = Tools::getHttpHost(false, false);
        if (!$httpHost) {
            return false;
        }

        $r = '!(?:(\w+)://)?(?:(\w+)\:(\w+)@)?([^/:]+)?(?:\:(\d*))?([^#?]+)?(?:\?([^#]+))?(?:#(.+$))?!i';
        if (!preg_match($r, $httpHost, $out)) {
            return false;
        }

        if (preg_match('/^(((25[0-5]|2[0-4][0-9]|1[0-9]{2}|[1-9]{1}[0-9]|[1-9]).)' .
            '{1}((25[0-5]|2[0-4][0-9]|[1]{1}[0-9]{2}|[1-9]{1}[0-9]|[0-9]).)' .
            '{2}((25[0-5]|2[0-4][0-9]|[1]{1}[0-9]{2}|[1-9]{1}[0-9]|[0-9]){1}))$/', $out[4])) {
            return false;
        }
        if (!strstr($httpHost, '.')) {
            return false;
        }

        $domain = false;
        if ($shared_urls !== null) {
            foreach ($shared_urls as $shared_url) {
                if ($shared_url != $out[4]) {
                    continue;
                }
                if (preg_match('/^(?:.*\.)?([^.]*(?:.{2,4})?\..{2,3})$/Ui', $shared_url, $res)) {
                    $domain = '.' . $res[1];

                    break;
                }
            }
        }
        if (!$domain) {
            $domain = $out[4];
        }

        return $domain;
    }

    /**
     * Set expiration date.
     *
     * @param int $expire Expiration time from now
     */
    public function setExpire($expire)
    {
        $this->_expire = (int) ($expire);
    }

    /**
     * Magic method wich return cookie data from _content array.
     *
     * @param string $key key wanted
     *
     * @return string value corresponding to the key
     */
    public function __get($key)
    {
        return isset($this->_content[$key]) ? $this->_content[$key] : false;
    }

    /**
     * Magic method which check if key exists in the cookie.
     *
     * @param string $key key wanted
     *
     * @return bool key existence
     */
    public function __isset($key)
    {
        return isset($this->_content[$key]);
    }

    /**
     * Magic method which adds data into _content array.
     *
     * @param string $key Access key for the value
     * @param mixed $value Value corresponding to the key
     *
     * @throws Exception
     */
    public function __set($key, $value)
    {
        if (is_array($value)) {
            die(Tools::displayError());
        }
        if (preg_match('/¤|\|/', $key . $value)) {
            throw new Exception('Forbidden chars in cookie');
        }
        if (!$this->_modified && (!array_key_exists($key, $this->_content) || $this->_content[$key] != $value)) {
            $this->_modified = true;
        }
        $this->_content[$key] = $value;
    }

    /**
     * Magic method which delete data into _content array.
     *
     * @param string $key key wanted
     */
    public function __unset($key)
    {
        if (isset($this->_content[$key])) {
            $this->_modified = true;
        }
        unset($this->_content[$key]);
    }

    /**
     * Delete cookie
     * As of version 1.5 don't call this function, use Customer::logout() or Employee::logout() instead;.
     */
    public function logout()
    {
        $this->deleteSession();
        $this->_content = [];
        $this->encryptAndSetCookie();
        unset($_COOKIE[$this->_name]);
        $this->_modified = true;
    }

    /**
     * Soft logout, delete everything links to the customer
     * but leave there affiliate's informations.
     * As of version 1.5 don't call this function, use Customer::mylogout() instead;.
     */
    public function mylogout()
    {
        $this->deleteSession();
        unset(
            $this->_content['id_customer'],
            $this->_content['id_guest'],
            $this->_content['is_guest'],
            $this->_content['id_connections'],
            $this->_content['customer_lastname'],
            $this->_content['customer_firstname'],
            $this->_content['passwd'],
            $this->_content['logged'],
            $this->_content['email'],
            $this->_content['id_cart'],
            $this->_content['id_address_invoice'],
            $this->_content['id_address_delivery']
        );
        $this->_modified = true;
    }

    public function makeNewLog()
    {
        unset(
            $this->_content['id_customer'],
            $this->_content['id_guest']
        );
        Guest::setNewGuest($this);
        $this->_modified = true;
    }

    /**
     * Get cookie content.
     */
    public function update($nullValues = false)
    {
        if (isset($_COOKIE[$this->_name])) {
            /* Decrypt cookie content */
            $content = $this->cipherTool->decrypt($_COOKIE[$this->_name]);
            //printf("\$content = %s<br />", $content);

            /* Get cookie checksum */
            $tmpTab = explode('¤', $content);
            // remove the checksum which is the last element
            array_pop($tmpTab);
            $content_for_checksum = implode('¤', $tmpTab) . '¤';
            $checksum = hash('sha256', $this->_salt . $content_for_checksum);
            //printf("\$checksum = %s<br />", $checksum);

            /* Unserialize cookie content */
            $tmpTab = explode('¤', $content);
            foreach ($tmpTab as $keyAndValue) {
                $tmpTab2 = explode('|', $keyAndValue);
                if (count($tmpTab2) == 2) {
                    $this->_content[$tmpTab2[0]] = $tmpTab2[1];
                }
            }
            /* Check if cookie has not been modified */
            if (!isset($this->_content['checksum']) || $this->_content['checksum'] != $checksum) {
                $this->logout();
            }

            if (!isset($this->_content['date_add'])) {
                $this->_content['date_add'] = date('Y-m-d H:i:s');
            }
        } else {
            $this->_content['date_add'] = date('Y-m-d H:i:s');
        }

        //checks if the language exists, if not choose the default language
        if (!$this->_standalone && !Language::getLanguage((int) $this->id_lang)) {
            $this->id_lang = (int) Configuration::get('PS_LANG_DEFAULT');
            // set detect_language to force going through Tools::setCookieLanguage to figure out browser lang
            $this->detect_language = true;
        }
    }

    /**
     * Encrypt and set the Cookie.
     *
     * @param string|null $cookie Cookie content
     *
     * @return bool Indicates whether the Cookie was successfully set
     *
     * @since 1.7.0
     */
    protected function encryptAndSetCookie($cookie = null)
    {
        // Check if the content fits in the Cookie
        $length = null === $cookie
            ? 0
            : ((ini_get('mbstring.func_overload') & 2)
                ? mb_strlen($cookie, ini_get('default_charset'))
                : strlen($cookie));

        if ($length >= 1048576) {
            return false;
        }
        if ($cookie) {
            $content = $this->cipherTool->encrypt($cookie);
            $time = $this->_expire;
        } else {
            $content = 0;
            $time = 1;
        }

        /*
         * The alternative signature supporting an options array is only available since
         * PHP 7.3.0, before there is no support for SameSite attribute.
         */
        if (PHP_VERSION_ID < 70300) {
            return setcookie(
                $this->_name,
                $content,
                $time,
                $this->_path,
                $this->_domain . '; SameSite=' . $this->_sameSite,
                $this->_secure,
                true
            );
        }

        return setcookie(
            $this->_name,
            $content,
            [
                'expires' => $time,
                'path' => $this->_path,
                'domain' => (string) $this->_domain,
                'secure' => $this->_secure,
                'httponly' => true,
                'samesite' => in_array((string) $this->_sameSite, static::SAMESITE_AVAILABLE_VALUES) ? (string) $this->_sameSite : static::SAMESITE_NONE,
            ]
        );
    }

    public function __destruct()
    {
        $this->write();
    }

    /**
     * Save cookie with setcookie().
     */
    public function write()
    {
        if (!$this->_modified || headers_sent() || !$this->_allow_writing) {
            return;
        }

        $previousChecksum = $cookie = '';

        /* Serialize cookie content */
        if (isset($this->_content['checksum'])) {
            $previousChecksum = $this->_content['checksum'];
            unset($this->_content['checksum']);
        }
        foreach ($this->_content as $key => $value) {
            $cookie .= $key . '|' . $value . '¤';
        }

        /* Add checksum to cookie */
        $newChecksum = hash('sha256', $this->_salt . $cookie);
        // do not set cookie if the checksum is the same: it means the content has not changed!
        if ($previousChecksum === $newChecksum) {
            return;
        }
        $cookie .= 'checksum|' . $newChecksum;
        $this->_modified = false;
        /* Cookies are encrypted for evident security reasons */
        return $this->encryptAndSetCookie($cookie);
    }

    /**
     * Get a family of variables (e.g. "filter_").
     */
    public function getFamily($origin)
    {
        $result = [];
        if (count($this->_content) == 0) {
            return $result;
        }
        foreach ($this->_content as $key => $value) {
            if (strncmp($key, $origin, strlen($origin)) == 0) {
                $result[$key] = $value;
            }
        }

        return $result;
    }

    public function unsetFamily($origin)
    {
        $family = $this->getFamily($origin);
        foreach (array_keys($family) as $member) {
            unset($this->$member);
        }
    }

    public function getAll()
    {
        return $this->_content;
    }

    /**
     * @return string name of cookie
     */
    public function getName()
    {
        return $this->_name;
    }

    /**
     * Check if the cookie exists.
     *
     * @since 1.5.0
     *
     * @return bool
     */
    public function exists()
    {
        return isset($_COOKIE[$this->_name]);
    }

    /**
     * Register a new session
     *
     * @param SessionInterface $session
     */
    public function registerSession(SessionInterface $session)
    {
        if (isset($this->id_employee)) {
            $session->setUserId((int) $this->id_employee);
        } elseif (isset($this->id_customer)) {
            $session->setUserId((int) $this->id_customer);
        } else {
            throw new CoreException('Invalid user id');
        }

        $session->setToken(sha1(time() . uniqid()));
        $session->add();

        $this->session_id = $session->getId();
        $this->session_token = $session->getToken();
    }

    /**
     * Delete session
     *
     * @return bool
     */
    public function deleteSession()
    {
        if (!isset($this->session_id)) {
            return false;
        }

        $session = $this->getSession($this->session_id);
        if ($session !== null) {
            $session->delete();

            return true;
        }

        return false;
    }

    /**
     * Check if this session is still alive
     *
     * @return bool
     */
    public function isSessionAlive()
    {
        if (!isset($this->session_id) || !isset($this->session_token)) {
            return false;
        }

        $session = $this->getSession($this->session_id);

        return
            $session !== null
            && $session->getToken() === $this->session_token
            && (
                (int) $this->id_employee === $session->getUserId()
                || (int) $this->id_customer === $session->getUserId()
            )
        ;
    }

    /**
     * Retrieve session based on a session id and the employee or
     * customer id
     *
     * @return SessionInterface|null
     */
    public function getSession($sessionId)
    {
        if ($this->session !== null) {
            return $this->session;
        }

        if (isset($this->id_employee)) {
            $this->session = new EmployeeSession($sessionId);
        } elseif (isset($this->id_customer)) {
            $this->session = new CustomerSession($sessionId);
        }

        if (isset($this->session) && Validate::isLoadedObject($this->session)) {
            // Update session date_upd
            $this->session->save();
        }

        return $this->session;
    }
}
