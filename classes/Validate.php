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
use Egulias\EmailValidator\EmailValidator;
use Egulias\EmailValidator\Validation\MultipleValidationWithAnd;
use Egulias\EmailValidator\Validation\RFCValidation;
use PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints\CustomerName;
use PrestaShop\PrestaShop\Core\ConstraintValidator\Factory\CustomerNameValidatorFactory;
use PrestaShop\PrestaShop\Core\Domain\Currency\ValueObject\NumericIsoCode;
use PrestaShop\PrestaShop\Core\Domain\Customer\ValueObject\ApeCode;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\Isbn;
use PrestaShop\PrestaShop\Core\Email\CyrillicCharactersInEmailValidation;
use PrestaShop\PrestaShop\Core\Security\PasswordPolicyConfiguration;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Validation;
use ZxcvbnPhp\Zxcvbn;

class ValidateCore
{
    public const ORDER_BY_REGEXP = '/^(?:(`?)[\w!_-]+\1\.)?(?:(`?)[\w!_-]+\2)$/';
    public const OBJECT_CLASS_NAME_REGEXP = '/^[a-zA-Z_\x80-\xff][a-zA-Z0-9_\x80-\xff]*(\\\\[a-zA-Z_\x80-\xff][a-zA-Z0-9_\x80-\xff]*)*$/';
    /**
     * Maximal 32 bits value: (2^32)-1
     *
     * @var int
     */
    public const MYSQL_UNSIGNED_INT_MAX = 4294967295;

    public static function isIp2Long($ip)
    {
        return preg_match('#^-?[0-9]+$#', (string) $ip);
    }

    /**
     * Check for e-mail validity.
     *
     * @param string $email e-mail address to validate
     *
     * @return bool Validity is ok or not
     */
    public static function isEmail($email)
    {
        // Check if the value is empty
        if (empty($email)) {
            return false;
        }

        $validator = Validation::createValidator();
        $errors = $validator->validate($email, new Email([
            'mode' => 'loose',
        ]));

        if (count($errors) > 0) {
            return false;
        }

        // Check if the value is correct according to both validators (RFC & CyrillicCharactersInEmailValidation)
        return (new EmailValidator())->isValid($email, new MultipleValidationWithAnd([
            new RFCValidation(),
            new CyrillicCharactersInEmailValidation(),
        ]));
    }

    /**
     * Check for module URL validity.
     *
     * @param string $url module URL to validate
     * @param array $errors Reference array for catching errors
     *
     * @return bool Validity is ok or not
     */
    public static function isModuleUrl($url, &$errors)
    {
        if (!$url || $url == 'http://') {
            $errors[] = Context::getContext()->getTranslator()->trans('Please specify module URL', [], 'Admin.Modules.Notification');
        } elseif (substr($url, -4) != '.tar' && substr($url, -4) != '.zip' && substr($url, -4) != '.tgz' && substr($url, -7) != '.tar.gz') {
            $errors[] = Context::getContext()->getTranslator()->trans('Unknown archive type.', [], 'Admin.Modules.Notification');
        } else {
            if ((strpos($url, 'http')) === false) {
                $url = 'http://' . $url;
            }
            if (!is_array(@get_headers($url))) {
                $errors[] = Context::getContext()->getTranslator()->trans('Invalid URL', [], 'Admin.Notifications.Error');
            }
        }
        if (!count($errors)) {
            return true;
        }

        return false;
    }

    /**
     * Check for MD5 string validity.
     *
     * @param string $md5 MD5 string to validate
     *
     * @return bool Validity is ok or not
     */
    public static function isMd5($md5)
    {
        return preg_match('/^[a-f0-9A-F]{32}$/', $md5);
    }

    /**
     * Check for SHA1 string validity.
     *
     * @param string $sha1 SHA1 string to validate
     *
     * @return bool Validity is ok or not
     */
    public static function isSha1($sha1)
    {
        return preg_match('/^[a-fA-F0-9]{40}$/', $sha1);
    }

    /**
     * Check for a float number validity.
     *
     * @param float $float Float number to validate
     *
     * @return bool Validity is ok or not
     */
    public static function isFloat($float)
    {
        return (string) ((float) $float) == (string) $float;
    }

    public static function isUnsignedFloat($float)
    {
        return (string) ((float) $float) == (string) $float && $float >= 0;
    }

    /**
     * Check for a float number validity.
     *
     * @param float $float Float number to validate
     *
     * @return bool Validity is ok or not
     */
    public static function isOptFloat($float)
    {
        return empty($float) || Validate::isFloat($float);
    }

    /**
     * Check for a carrier name validity.
     *
     * @param string $name Carrier name to validate
     *
     * @return bool Validity is ok or not
     */
    public static function isCarrierName($name)
    {
        return empty($name) || preg_match('/^[^<>;=#{}]*$/u', $name);
    }

    /**
     * Check for an image size validity.
     *
     * @param string $size Image size to validate
     *
     * @return bool Validity is ok or not
     */
    public static function isImageSize($size)
    {
        return preg_match('/^[0-9]{1,4}$/', $size);
    }

    /**
     * Check whether given customer name is valid
     *
     * @param string $name Name to validate
     *
     * @return bool
     */
    public static function isCustomerName($name)
    {
        $validatorBuilder = Validation::createValidatorBuilder();
        $validatorBuilder->setConstraintValidatorFactory(new CustomerNameValidatorFactory());
        $validator = $validatorBuilder->getValidator();
        $violations = $validator->validate($name, [
            new CustomerName(),
        ]);

        return count($violations) === 0;
    }

    /**
     * Check whether given name is valid
     *
     * @param string $name Name to validate
     *
     * @return bool
     */
    public static function isName($name)
    {
        return preg_match('/^[^0-9!<>,;?=+()@#"°{}_$%:¤|]*$/u', $name);
    }

    /**
     * Check for hook name validity.
     *
     * @param string $hook Hook name to validate
     *
     * @return bool Validity is ok or not
     */
    public static function isHookName($hook)
    {
        return preg_match('/^[a-zA-Z0-9_-]+$/', $hook);
    }

    /**
     * Check for sender name validity.
     *
     * @param string $mail_name Sender name to validate
     *
     * @return bool Validity is ok or not
     */
    public static function isMailName($mail_name)
    {
        return is_string($mail_name) && preg_match('/^[^<>;=#{}]*$/u', $mail_name);
    }

    /**
     * Check for e-mail subject validity.
     *
     * @param string $mail_subject e-mail subject to validate
     *
     * @return bool Validity is ok or not
     */
    public static function isMailSubject($mail_subject)
    {
        return preg_match('/^[^<>]*$/u', $mail_subject);
    }

    /**
     * Check for module name validity.
     *
     * @param string $module_name Module name to validate
     *
     * @return bool Validity is ok or not
     */
    public static function isModuleName($module_name)
    {
        return is_string($module_name) && preg_match('/^[a-zA-Z0-9_-]+$/', $module_name);
    }

    /**
     * Check for template name validity.
     *
     * @param string $tpl_name Template name to validate
     *
     * @return bool Validity is ok or not
     */
    public static function isTplName($tpl_name)
    {
        return preg_match('/^[a-zA-Z0-9_-]+$/', $tpl_name);
    }

    /**
     * Check for image type name validity.
     *
     * @param string $type Image type name to validate
     *
     * @return bool Validity is ok or not
     */
    public static function isImageTypeName($type)
    {
        return preg_match('/^[a-zA-Z0-9_ -]+$/', $type);
    }

    /**
     * Check for price validity.
     *
     * @param string $price Price to validate
     *
     * @return bool Validity is ok or not
     */
    public static function isPrice($price)
    {
        return preg_match('/^[0-9]{1,10}(\.[0-9]{1,9})?$/', $price);
    }

    /**
     * Check for price validity (including negative price).
     *
     * @param string $price Price to validate
     *
     * @return bool Validity is ok or not
     */
    public static function isNegativePrice($price)
    {
        return preg_match('/^[-]?[0-9]{1,10}(\.[0-9]{1,9})?$/', $price);
    }

    /**
     * Check for language code (ISO) validity.
     *
     * @param string $iso_code Language code (ISO) to validate
     *
     * @return bool Validity is ok or not
     */
    public static function isLanguageIsoCode($iso_code)
    {
        return preg_match('/^[a-zA-Z]{2,3}$/', $iso_code);
    }

    public static function isLanguageCode($s)
    {
        return preg_match('/^[a-zA-Z]{2}(-[a-zA-Z]{2})?$/', $s);
    }

    /**
     * @see https://en.wikipedia.org/wiki/IETF_language_tag#ISO_3166-1_and_UN_M.49
     *
     * @param string $s
     *
     * @return bool
     */
    public static function isLocale($s)
    {
        return preg_match('/^[a-z]{2}-[A-Z]{2}$/', $s);
    }

    public static function isStateIsoCode($iso_code)
    {
        return preg_match('/^[a-zA-Z0-9]{1,4}((-)[a-zA-Z0-9]{1,4})?$/', $iso_code);
    }

    public static function isNumericIsoCode($iso_code)
    {
        return preg_match(NumericIsoCode::PATTERN, $iso_code);
    }

    /**
     * Check for voucher name validity.
     *
     * @param string $voucher voucher to validate
     *
     * @return bool Validity is ok or not
     */
    public static function isDiscountName($voucher)
    {
        return preg_match('/^[^!<>,;?=+()@"°{}_$%:]{3,32}$/u', $voucher);
    }

    /**
     * Check for product or category name validity.
     *
     * @param string $name Product or category name to validate
     *
     * @return bool Validity is ok or not
     */
    public static function isCatalogName($name)
    {
        return preg_match('/^[^<>;=#{}]*$/u', $name);
    }

    /**
     * Check for a message validity.
     *
     * @param string $message Message to validate
     *
     * @return bool Validity is ok or not
     */
    public static function isMessage($message)
    {
        return !preg_match('/[<>{}]/i', $message);
    }

    /**
     * Check for a country name validity.
     *
     * @param string $name Country name to validate
     *
     * @return bool Validity is ok or not
     */
    public static function isCountryName($name)
    {
        return preg_match('/^[a-zA-Z -]+$/', $name);
    }

    /**
     * Check for a link (url-rewriting only) validity.
     *
     * @param string $link Link to validate
     *
     * @return bool Validity is ok or not
     */
    public static function isLinkRewrite($link)
    {
        if (Configuration::get('PS_ALLOW_ACCENTED_CHARS_URL')) {
            return preg_match('/^[_a-zA-Z0-9\x{0600}-\x{06FF}\pL\pS-]+$/u', $link);
        }

        return preg_match('/^[_a-zA-Z0-9\-]+$/', $link);
    }

    /**
     * Check for a route pattern validity.
     *
     * @param string $pattern to validate
     *
     * @return bool Validity is ok or not
     */
    public static function isRoutePattern($pattern)
    {
        if (Configuration::get('PS_ALLOW_ACCENTED_CHARS_URL')) {
            return preg_match('/^[_a-zA-Z0-9\x{0600}-\x{06FF}\(\)\.{}:\/\pL\pS-]+$/u', $pattern);
        }

        return preg_match('/^[_a-zA-Z0-9\(\)\.{}:\/\-]+$/', $pattern);
    }

    /**
     * Check for a postal address validity.
     *
     * @param string $address Address to validate
     *
     * @return bool Validity is ok or not
     */
    public static function isAddress($address)
    {
        return empty($address) || preg_match('/^[^!<>?=+@{}_$%]*$/u', $address);
    }

    /**
     * Check for city name validity.
     *
     * @param string $city City name to validate
     *
     * @return bool Validity is ok or not
     */
    public static function isCityName($city)
    {
        return preg_match('/^[^!<>;?=+@#"°{}_$%]*$/u', $city);
    }

    /**
     * Check for search query validity.
     *
     * @param string $search Query to validate
     *
     * @return bool Validity is ok or not
     */
    public static function isValidSearch($search)
    {
        return preg_match('/^[^<>;=#{}]{0,64}$/u', $search);
    }

    /**
     * Check for standard name validity.
     *
     * @param string $name Name to validate
     *
     * @return bool Validity is ok or not
     */
    public static function isGenericName($name)
    {
        return empty($name) || preg_match('/^[^<>={}]*$/u', $name);
    }

    /**
     * Check for HTML field validity (no XSS please !).
     *
     * @param string $html HTML field to validate
     *
     * @return bool Validity is ok or not
     */
    public static function isCleanHtml($html, $allow_iframe = false)
    {
        $events = 'onmousedown|onmousemove|onmmouseup|onmouseover|onmouseout|onload|onunload|onfocus|onblur|onchange';
        $events .= '|onsubmit|ondblclick|onclick|onkeydown|onkeyup|onkeypress|onmouseenter|onmouseleave|onerror|onselect|onreset|onabort|ondragdrop|onresize|onactivate|onafterprint|onmoveend';
        $events .= '|onafterupdate|onbeforeactivate|onbeforecopy|onbeforecut|onbeforedeactivate|onbeforeeditfocus|onbeforepaste|onbeforeprint|onbeforeunload|onbeforeupdate|onmove';
        $events .= '|onbounce|oncellchange|oncontextmenu|oncontrolselect|oncopy|oncut|ondataavailable|ondatasetchanged|ondatasetcomplete|ondeactivate|ondrag|ondragend|ondragenter|onmousewheel';
        $events .= '|ondragleave|ondragover|ondragstart|ondrop|onerrorupdate|onfilterchange|onfinish|onfocusin|onfocusout|onhashchange|onhelp|oninput|onlosecapture|onmessage|onmouseup|onmovestart';
        $events .= '|onoffline|ononline|onpaste|onpropertychange|onreadystatechange|onresizeend|onresizestart|onrowenter|onrowexit|onrowsdelete|onrowsinserted|onscroll|onsearch|onselectionchange';
        $events .= '|onselectstart|onstart|onstop|onanimationcancel|onanimationend|onanimationiteration|onanimationstart';
        $events .= '|onpointerover|onpointerenter|onpointerdown|onpointermove|onpointerup|onpointerout|onpointerleave|onpointercancel|ongotpointercapture|onlostpointercapture';

        if (preg_match('/<[\s]*script/ims', $html) || preg_match('/(' . $events . ')[\s]*=/ims', $html) || preg_match('/.*script\:/ims', $html)) {
            return false;
        }

        if (!$allow_iframe && preg_match('/<[\s]*(i?frame|form|input|embed|object)/ims', $html)) {
            return false;
        }

        return true;
    }

    /**
     * Check for product reference validity.
     *
     * @param string $reference Product reference to validate
     *
     * @return bool Validity is ok or not
     */
    public static function isReference($reference)
    {
        return preg_match('/^[^<>;={}]*$/u', $reference);
    }

    /**
     * Check if the password score is valid
     *
     * @param string $password Password to validate
     *
     * @return bool Indicates whether the given string is a valid password
     */
    public static function isAcceptablePasswordScore(string $password): bool
    {
        $zxcvbn = new Zxcvbn();
        $result = $zxcvbn->passwordStrength($password);
        $minScore = Configuration::hasKey(PasswordPolicyConfiguration::CONFIGURATION_MINIMUM_SCORE) ?
                  Configuration::get(PasswordPolicyConfiguration::CONFIGURATION_MINIMUM_SCORE) :
                  PasswordPolicyConfiguration::PASSWORD_SAFELY_UNGUESSABLE;

        return isset($result['score']) && $result['score'] >= $minScore;
    }

    /**
     * Check if password length is valid
     *
     * @param string $password Password to validate
     *
     * @return bool Indicates whether the given string is a valid password length
     */
    public static function isAcceptablePasswordLength(string $password): bool
    {
        $passwordLength = Tools::strlen($password);
        if (Configuration::hasKey(PasswordPolicyConfiguration::CONFIGURATION_MINIMUM_LENGTH)
            && Configuration::hasKey(PasswordPolicyConfiguration::CONFIGURATION_MAXIMUM_LENGTH)
        ) {
            return $passwordLength >= Configuration::get(PasswordPolicyConfiguration::CONFIGURATION_MINIMUM_LENGTH)
                && $passwordLength <= Configuration::get(PasswordPolicyConfiguration::CONFIGURATION_MAXIMUM_LENGTH);
        }

        // If value doesn't exist in database, use default behavior check
        return $passwordLength >= PasswordPolicyConfiguration::DEFAULT_MINIMUM_LENGTH && $passwordLength <= PasswordPolicyConfiguration::DEFAULT_MAXIMUM_LENGTH;
    }

    /**
     * Check if hashed password is valid
     * PrestaShop supports both MD5 and `PASSWORD_BCRYPT` (PHP API)
     * The lengths are 32 (MD5) or 60 (`PASSWORD_BCRYPT`)
     * Anything else is invalid.
     *
     * @param string $hashedPasswd Password to validate
     *
     * @return bool Indicates whether the given string is a valid hashed password
     *
     * @since 1.7.0
     */
    public static function isHashedPassword($hashedPasswd)
    {
        return Tools::strlen($hashedPasswd) == 32 || Tools::strlen($hashedPasswd) == 60;
    }

    /**
     * Check for configuration key validity.
     *
     * @param string $config_name Configuration key to validate
     *
     * @return bool Validity is ok or not
     */
    public static function isConfigName($config_name)
    {
        return preg_match('/^[a-zA-Z_0-9-]+$/', $config_name);
    }

    /**
     * Check date formats like http://php.net/manual/en/function.date.php.
     *
     * @param string $date_format date format to check
     *
     * @return bool Validity is ok or not
     */
    public static function isPhpDateFormat($date_format)
    {
        // We can't really check if this is valid or not, because this is a string and you can write whatever you want in it.
        // That's why only < et > are forbidden (HTML)
        return preg_match('/^[^<>]+$/', $date_format);
    }

    /**
     * Check for date format.
     *
     * @param string $date Date to validate
     *
     * @return bool Validity is ok or not
     */
    public static function isDateFormat($date)
    {
        return (bool) preg_match('/^([0-9]{4})-((0?[0-9])|(1[0-2]))-((0?[0-9])|([1-2][0-9])|(3[01]))( [0-9]{2}:[0-9]{2}:[0-9]{2})?$/', $date);
    }

    /**
     * Check for date validity.
     *
     * @param string $date Date to validate
     *
     * @return bool Validity is ok or not
     */
    public static function isDate($date)
    {
        if (!preg_match('/^([0-9]{4})-((?:0?[0-9])|(?:1[0-2]))-((?:0?[0-9])|(?:[1-2][0-9])|(?:3[01]))( [0-9]{2}:[0-9]{2}:[0-9]{2})?$/', $date, $matches)) {
            return false;
        }

        return checkdate((int) $matches[2], (int) $matches[3], (int) $matches[1]);
    }

    public static function isDateOrNull($date)
    {
        if (null === $date || $date === '0000-00-00 00:00:00' || $date === '0000-00-00') {
            return true;
        }

        return self::isDate($date);
    }

    /**
     * Check for birthDate validity. To avoid year in two digits, disallow date < 200 years ago
     *
     * @param string $date birthdate to validate
     * @param string $format optional format
     *
     * @return bool Validity is ok or not
     */
    public static function isBirthDate($date, $format = 'Y-m-d')
    {
        if (empty($date) || $date == '0000-00-00') {
            return true;
        }

        $d = DateTime::createFromFormat($format, $date);
        if (!empty(DateTime::getLastErrors()['warning_count']) || false === $d) {
            return false;
        }
        $twoHundredYearsAgo = new Datetime();
        $twoHundredYearsAgo->sub(new DateInterval('P200Y'));

        return $d->setTime(0, 0, 0) <= new Datetime() && $d->setTime(0, 0, 0) >= $twoHundredYearsAgo;
    }

    /**
     * Check for boolean validity.
     *
     * @param mixed $bool Value to validate as a boolean
     *
     * @return bool Validity is ok or not
     */
    public static function isBool($bool)
    {
        return $bool === null || is_bool($bool) || preg_match('/^(0|1)$/', $bool);
    }

    /**
     * Check for phone number validity.
     *
     * @param string $number Phone number to validate
     *
     * @return bool Validity is ok or not
     */
    public static function isPhoneNumber($number)
    {
        return preg_match('/^[+0-9. ()\/-]*$/', $number);
    }

    /**
     * Check for barcode validity (EAN-13).
     *
     * @param string $ean13 Barcode to validate
     *
     * @return bool Validity is ok or not
     */
    public static function isEan13($ean13)
    {
        return !$ean13 || preg_match('/^[0-9]{0,13}$/', $ean13);
    }

    /**
     * Check for ISBN.
     *
     * @param string $isbn validate
     *
     * @return bool Validity is ok or not
     */
    public static function isIsbn($isbn)
    {
        return !$isbn || preg_match(Isbn::VALID_PATTERN, $isbn);
    }

    /**
     * Check for barcode validity (UPC).
     *
     * @param string $upc Barcode to validate
     *
     * @return bool Validity is ok or not
     */
    public static function isUpc($upc)
    {
        return !$upc || preg_match('/^[0-9]{0,12}$/', $upc);
    }

    /**
     * Check for MPN validity.
     *
     * @param string $mpn to validate
     *
     * @return bool Validity is ok or not
     */
    public static function isMpn($mpn)
    {
        return Tools::strlen($mpn) <= 40;
    }

    /**
     * Check for postal code validity.
     *
     * @param string $postcode Postal code to validate
     *
     * @return bool Validity is ok or not
     */
    public static function isPostCode($postcode)
    {
        return empty($postcode) || preg_match('/^[a-zA-Z 0-9-]+$/', $postcode);
    }

    /**
     * Check for zip code format validity.
     *
     * @param string $zip_code zip code format to validate
     *
     * @return bool Validity is ok or not
     */
    public static function isZipCodeFormat($zip_code)
    {
        if (!empty($zip_code)) {
            return preg_match('/^[NLCnlc 0-9-]+$/', $zip_code);
        }

        return true;
    }

    /**
     * Check for table or identifier validity
     * Mostly used in database for ordering : ASC / DESC.
     *
     * @param string $way Keyword to validate
     *
     * @return bool Validity is ok or not
     */
    public static function isOrderWay($way)
    {
        return !empty($way) && in_array(strtolower($way), ['asc', 'desc', 'random']);
    }

    /**
     * Check for table or identifier validity
     * Mostly used in database for ordering : ORDER BY field.
     *
     * @param string $order Field to validate
     *
     * @return bool Validity is ok or not
     */
    public static function isOrderBy($order)
    {
        return !empty($order) && preg_match(static::ORDER_BY_REGEXP, $order);
    }

    /**
     * Check for table or identifier validity
     * Mostly used in database for table names and id_table.
     *
     * @param string $table Table/identifier to validate
     *
     * @return bool Validity is ok or not
     */
    public static function isTableOrIdentifier($table)
    {
        return preg_match('/^[a-zA-Z0-9_-]+$/', $table);
    }

    /**
     * Check for tags list validity.
     *
     * @param string $list List to validate
     *
     * @return bool Validity is ok or not
     */
    public static function isTagsList($list)
    {
        return preg_match('/^[^!<>;?=+#"°{}_$%]*$/u', $list);
    }

    /**
     * Check for product visibility.
     *
     * @param string $s visibility to check
     *
     * @return bool Validity is ok or not
     */
    public static function isProductVisibility($s)
    {
        return preg_match('/^both|catalog|search|none$/i', $s);
    }

    /**
     * Check for an integer validity.
     *
     * @param int|bool $value Integer to validate
     *
     * @return bool Validity is ok or not
     */
    public static function isInt($value)
    {
        return (string) (int) $value === (string) $value || $value === false;
    }

    /**
     * Check for an integer validity (unsigned).
     *
     * @param mixed $value Integer to validate
     *
     * @return bool Validity is ok or not
     */
    public static function isUnsignedInt($value)
    {
        return (is_numeric($value) || is_string($value))
            && (string) (int) $value === (string) $value
            && $value < (static::MYSQL_UNSIGNED_INT_MAX + 1)
            && $value >= 0;
    }

    /**
     * Check for a number (int) bigger than 0
     *
     * @param mixed $value Integer with value bigger than 0 to validate
     *
     * @return bool Validity is ok or not
     */
    public static function isPositiveInt($value)
    {
        return self::isUnsignedInt($value) && $value > 0;
    }

    /**
     * Check for an percentage validity (between 0 and 100).
     *
     * @param float $value Float to validate
     *
     * @return bool Validity is ok or not
     */
    public static function isPercentage($value)
    {
        return Validate::isFloat($value) && $value >= 0 && $value <= 100;
    }

    /**
     * Check for an integer validity (unsigned)
     * Mostly used in database for auto-increment.
     *
     * @param int $id Integer to validate
     *
     * @return bool Validity is ok or not
     */
    public static function isUnsignedId($id)
    {
        return Validate::isUnsignedInt($id); /* Because an id could be equal to zero when there is no association */
    }

    public static function isNullOrUnsignedId($id)
    {
        return $id === null || Validate::isUnsignedId($id);
    }

    /**
     * Check object validity.
     *
     * @param object $object Object to validate
     *
     * @return bool Validity is ok or not
     */
    public static function isLoadedObject($object)
    {
        return is_object($object) && $object->id;
    }

    /**
     * Check color validity.
     *
     * @param string $color Color to validate
     *
     * @return bool Validity is ok or not
     */
    public static function isColor($color)
    {
        return preg_match('/^(#[0-9a-fA-F]{6}|[a-zA-Z0-9-]*)$/', $color);
    }

    /**
     * Check url validity (disallowed empty string).
     *
     * @param string $url Url to validate
     *
     * @return bool Validity is ok or not
     */
    public static function isUrl($url)
    {
        return preg_match('/^[~:#,$%&_=\(\)\.\? \+\-@\/a-zA-Z0-9\pL\pS-]+$/u', $url);
    }

    /**
     * Check tracking number validity (disallowed empty string).
     *
     * @param string $tracking_number Tracking number to validate
     *
     * @return bool Validity is ok or not
     */
    public static function isTrackingNumber($tracking_number)
    {
        return preg_match('/^[~:#,%&_=\(\)\[\]\.\? \+\-@\/a-zA-Z0-9]+$/', $tracking_number);
    }

    /**
     * Check url validity (allowed empty string).
     *
     * @param string $url Url to validate
     *
     * @return bool Validity is ok or not
     */
    public static function isUrlOrEmpty($url)
    {
        return empty($url) || Validate::isUrl($url);
    }

    /**
     * Check if URL is absolute.
     *
     * @param string $url URL to validate
     *
     * @return bool Validity is ok or not
     */
    public static function isAbsoluteUrl($url)
    {
        if (!empty($url)) {
            return preg_match('/^(https?:)?\/\/[$~:;#,%&_=\(\)\[\]\.\? \+\-@\/a-zA-Z0-9]+$/', $url);
        }

        return true;
    }

    public static function isMySQLEngine($engine)
    {
        return in_array($engine, ['InnoDB', 'MyISAM']);
    }

    public static function isUnixName($data)
    {
        return preg_match('/^[a-z0-9\._-]+$/ui', $data);
    }

    public static function isTablePrefix($data)
    {
        // Even if "-" is theorically allowed, it will be considered a syntax error if you do not add backquotes (`) around the table name
        return preg_match('/^[a-z0-9_]+$/ui', $data);
    }

    /**
     * Check for standard name file validity.
     *
     * @param string $name Name to validate
     *
     * @return bool Validity is ok or not
     */
    public static function isFileName($name)
    {
        return preg_match('/^[a-zA-Z0-9_.-]+$/', $name);
    }

    /**
     * Check for standard name directory validity.
     *
     * @param string $dir Directory to validate
     *
     * @return bool Validity is ok or not
     */
    public static function isDirName($dir)
    {
        return (bool) preg_match('/^[a-zA-Z0-9_.-]*$/', $dir);
    }

    /**
     * Check for admin panel tab name validity.
     *
     * @param string $name Name to validate
     *
     * @return bool Validity is ok or not
     */
    public static function isTabName($name)
    {
        return preg_match('/^[^<>]+$/u', $name);
    }

    public static function isWeightUnit($unit)
    {
        return Validate::isGenericName($unit) & (Tools::strlen($unit) < 5);
    }

    public static function isDistanceUnit($unit)
    {
        return Validate::isGenericName($unit) & (Tools::strlen($unit) < 5);
    }

    public static function isSubDomainName($domain)
    {
        return preg_match('/^[a-zA-Z0-9-_]*$/', $domain);
    }

    public static function isVoucherDescription($text)
    {
        return preg_match('/^([^<>{}]|<br \/>)*$/i', $text);
    }

    /**
     * Check if the value is a sort direction value (DESC/ASC).
     *
     * @param string $value
     *
     * @return bool Validity is ok or not
     */
    public static function isSortDirection($value)
    {
        return $value === 'ASC' || $value === 'DESC';
    }

    /**
     * Customization fields' label validity.
     *
     * @param string $label
     *
     * @return bool Validity is ok or not
     */
    public static function isLabel($label)
    {
        return preg_match('/^[^{}<>]*$/u', $label);
    }

    /**
     * Price display method validity.
     *
     * @param int $data Data to validate
     *
     * @return bool Validity is ok or not
     */
    public static function isPriceDisplayMethod($data)
    {
        return $data == PS_TAX_EXC || $data == PS_TAX_INC;
    }

    /**
     * @param string $dni to validate
     *
     * @return bool
     */
    public static function isDniLite($dni)
    {
        return empty($dni) || (bool) preg_match('/^[0-9A-Za-z-.]{1,16}$/U', $dni);
    }

    /**
     * Check if $data is a PrestaShop cookie object.
     *
     * @param mixed $data to validate
     *
     * @return bool
     */
    public static function isCookie($data)
    {
        return is_object($data) && get_class($data) == 'Cookie';
    }

    /**
     * Check if $data is a string.
     *
     * @param string $data Data to validate
     *
     * @return bool Validity is ok or not
     */
    public static function isString($data)
    {
        return is_string($data);
    }

    /**
     * Check if the data is a reduction type (amout or percentage).
     *
     * @param string $data Data to validate
     *
     * @return bool Validity is ok or not
     */
    public static function isReductionType($data)
    {
        return $data === 'amount' || $data === 'percentage';
    }

    /**
     * Check for bool_id.
     *
     * @param string $ids
     *
     * @return bool Validity is ok or not
     */
    public static function isBoolId($ids)
    {
        return (bool) preg_match('#^[01]_[0-9]+$#', $ids);
    }

    /**
     * Check the localization pack part selected.
     *
     * @param string $data Localization pack to check
     *
     * @return bool Validity is ok or not
     */
    public static function isLocalizationPackSelection($data)
    {
        return in_array((string) $data, ['states', 'taxes', 'currencies', 'languages', 'units', 'groups']);
    }

    /**
     * Check for PHP serialized data.
     *
     * @param string|null $data Serialized data to validate
     *
     * @return bool Validity is ok or not
     */
    public static function isSerializedArray($data)
    {
        return $data === null || (is_string($data) && preg_match('/^a:[0-9]+:{.*;}$/s', $data));
    }

    /**
     * Check if $string is a valid JSON string.
     *
     * @param string $string JSON string to validate
     *
     * @return bool Validity is ok or not
     */
    public static function isJson($string)
    {
        json_decode($string);

        return json_last_error() == JSON_ERROR_NONE;
    }

    /**
     * Check for Latitude/Longitude.
     *
     * @param string|null $data Coordinate to validate
     *
     * @return bool Validity is ok or not
     */
    public static function isCoordinate($data)
    {
        return $data === null || preg_match('/^\-?[0-9]{1,8}\.[0-9]{1,8}$/s', $data);
    }

    /**
     * Check for Language Iso Code.
     *
     * @param string $iso_code
     *
     * @return bool Validity is ok or not
     */
    public static function isLangIsoCode($iso_code)
    {
        return (bool) preg_match('/^[a-zA-Z]{2,3}$/s', $iso_code);
    }

    /**
     * Check for Language File Name.
     *
     * @param string $file_name
     *
     * @return bool Validity is ok or not
     */
    public static function isLanguageFileName($file_name)
    {
        return (bool) preg_match('/^[a-zA-Z]{2,3}\.(?:gzip|tar\.gz)$/s', $file_name);
    }

    /**
     * @param array $ids
     *
     * @return bool return true if the array contain only unsigned int value and not empty
     */
    public static function isArrayWithIds($ids)
    {
        if (!is_array($ids) || count($ids) < 1) {
            return false;
        }

        foreach ($ids as $id) {
            if ($id == 0 || !Validate::isUnsignedInt($id)) {
                return false;
            }
        }

        return true;
    }

    /**
     * @param array $stock_management
     *
     * @return bool return true if is a valide stock management
     */
    public static function isStockManagement($stock_management)
    {
        if (!in_array($stock_management, ['WA', 'FIFO', 'LIFO'])) {
            return false;
        }

        return true;
    }

    /**
     * Validate SIRET Code.
     *
     * @param string $siret SIRET Code
     *
     * @return bool Return true if is valid
     */
    public static function isSiret($siret)
    {
        if (Tools::strlen($siret) != 14) {
            return false;
        }
        $sum = 0;
        for ($i = 0; $i != 14; ++$i) {
            $tmp = ((($i + 1) % 2) + 1) * (int) ($siret[$i]);
            if ($tmp >= 10) {
                $tmp -= 9;
            }
            $sum += $tmp;
        }

        return $sum % 10 === 0;
    }

    /**
     * Validate APE Code.
     *
     * @param string $ape APE Code
     *
     * @return bool Return true if is valid
     */
    public static function isApe($ape)
    {
        return (bool) preg_match(ApeCode::PATTERN, $ape);
    }

    public static function isControllerName($name)
    {
        return (bool) (is_string($name) && preg_match('/^[0-9a-zA-Z-_]*$/u', $name));
    }

    public static function isPrestaShopVersion($version)
    {
        return preg_match('/^[0-1]\.[0-9]{1,2}(\.[0-9]{1,2}){0,2}$/', $version) && ip2long($version);
    }

    public static function isOrderInvoiceNumber($id)
    {
        return preg_match('/^(?:' . Configuration::get('PS_INVOICE_PREFIX', Context::getContext()->language->id) . ')\s*([0-9]+)$/i', $id);
    }

    public static function isThemeName($theme_name)
    {
        return (bool) preg_match('/^[\w-]{3,255}$/u', $theme_name);
    }

    /**
     * Check if enable_insecure_rsh exists in
     * this PHP version otherwise disable the
     * oProxyCommand option.
     *
     * @return bool
     */
    public static function isValidImapUrl($imapUrl)
    {
        if (false === ini_get('imap.enable_insecure_rsh')) {
            return preg_match('~^((?!oProxyCommand).)*$~i', $imapUrl);
        }

        return true;
    }

    /**
     * Check the given string is a valid PHP class name
     *
     * @param string $objectClassName object class name
     *
     * @return bool
     */
    public static function isValidObjectClassName(string $objectClassName): bool
    {
        return preg_match(static::OBJECT_CLASS_NAME_REGEXP, $objectClassName);
    }
}
