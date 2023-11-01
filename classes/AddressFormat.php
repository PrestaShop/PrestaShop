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
use PrestaShop\PrestaShop\Core\Domain\Address\Exception\AddressException;

/**
 * Class AddressFormatCore.
 */
class AddressFormatCore extends ObjectModel
{
    public const FORMAT_NEW_LINE = "\n";

    /** @var int Address format */
    public $id_address_format;

    /** @var int Country ID */
    public $id_country;

    /** @var string Format */
    public $format;

    protected $_errorFormatList = [];

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = [
        'table' => 'address_format',
        'primary' => 'id_country',
        'fields' => [
            'format' => ['type' => self::TYPE_HTML, 'validate' => 'isGenericName', 'required' => true],
            'id_country' => ['type' => self::TYPE_INT],
        ],
    ];

    /** @var array Default required form fields list */
    public static $requireFormFieldsList = [
        'firstname',
        'lastname',
        'address1',
        'city',
        'Country:name',
    ];

    /** @var array Default forbidden property list */
    public static $forbiddenPropertyList = [
        'deleted',
        'date_add',
        'alias',
        'secure_key',
        'note',
        'newsletter',
        'ip_registration_newsletter',
        'newsletter_date_add',
        'optin',
        'passwd',
        'last_passwd_gen',
        'active',
        'is_guest',
        'date_upd',
        'country',
        'years',
        'days',
        'months',
        'description',
        'meta_description',
        'short_description',
        'link_rewrite',
        'meta_title',
        'meta_keywords',
        'display_tax_label',
        'need_zip_code',
        'contains_states',
        'call_prefixes',
        'show_public_prices',
        'max_payment',
        'max_payment_days',
        'geoloc_postcode',
        'logged',
        'account_number',
        'groupBox',
        'ape',
        'max_payment',
        'outstanding_allow_amount',
        'call_prefix',
        'definition',
        'debug_list',
    ];

    /** @var array Default formbidden class list */
    public static $forbiddenClassList = [
        'Manufacturer',
        'Supplier',
    ];

    public const _CLEANING_REGEX_ = '#([^\w:_]+)#i';

    /**
     * Check if the the association of the field name and a class name
     * is valid.
     *
     * @param string $className The name class
     * @param string $fieldName The property name
     * @param bool $isIdField Do we have to allow a property name to be started with 'id_'
     *
     * @return bool Association of the field and class name is valid
     */
    protected function _checkValidateClassField($className, $fieldName, $isIdField)
    {
        $isValid = false;

        if (!class_exists($className)) {
            $this->_errorFormatList[] = $this->trans('This class name does not exist.', [], 'Admin.Notifications.Error') .
            ': ' . $className;
        } else {
            $obj = new $className();
            $reflect = new ReflectionObject($obj);

            // Check if the property is accessible
            $publicProperties = $reflect->getProperties(ReflectionProperty::IS_PUBLIC);
            foreach ($publicProperties as $property) {
                $propertyName = $property->getName();
                if (($propertyName == $fieldName) && ($isIdField ||
                        (!preg_match('/\bid\b|id_\w+|\bid[A-Z]\w+/', $propertyName)))) {
                    $isValid = true;
                }
            }

            if (!$isValid) {
                $this->_errorFormatList[] = $this->trans('This property does not exist in the class or is forbidden.', [], 'Admin.Notifications.Error') .
                ': ' . $className . ': ' . $fieldName;
            }

            unset(
                $obj,
                $reflect
            );
        }

        return $isValid;
    }

    /**
     * Verify the existence of a field name and check the availability
     * of an association between a field name and a class (ClassName:fieldName)
     * if the separator is overview.
     *
     * @param string $patternName The composition of the class and field name
     */
    protected function _checkLiableAssociation($patternName)
    {
        $patternName = trim($patternName);

        $associationName = explode(':', $patternName);
        $totalNameUsed = count($associationName);
        if ($totalNameUsed > 2) {
            $this->_errorFormatList[] = $this->trans('This association has too many elements.', [], 'Admin.Notifications.Error');
        } elseif ($totalNameUsed == 1) {
            $associationName[0] = strtolower($associationName[0]);
            if (in_array($associationName[0], self::$forbiddenPropertyList) ||
                !$this->_checkValidateClassField('Address', $associationName[0], false)) {
                $this->_errorFormatList[] = $this->trans('This name is not allowed.', [], 'Admin.Notifications.Error') . ': ' .
                $associationName[0];
            }
        } else {
            if (empty($associationName[0]) || empty($associationName[1])) {
                $this->_errorFormatList[] = $this->trans('Syntax error with this pattern.', [], 'Admin.Notifications.Error') . ': ' . $patternName;
            } else {
                $associationName[0] = ucfirst($associationName[0]);
                $associationName[1] = strtolower($associationName[1]);

                if (in_array($associationName[0], self::$forbiddenClassList)) {
                    $this->_errorFormatList[] = $this->trans('This name is not allowed.', [], 'Admin.Notifications.Error') . ': ' .
                    $associationName[0];
                } else {
                    // Check if the id field name exist in the Address class
                    // Don't check this attribute on Address (no sense)
                    if ($associationName[0] != 'Address') {
                        $this->_checkValidateClassField('Address', 'id_' . strtolower($associationName[0]), true);
                    }

                    // Check if the field name exist in the class write by the user
                    $this->_checkValidateClassField($associationName[0], $associationName[1], false);
                }
            }
        }
    }

    /**
     * Check if the set fields are valid.
     */
    public function checkFormatFields()
    {
        $this->_errorFormatList = [];
        $fieldsValidate = Address::getFieldsValidate();
        $usedKeyList = [];

        $multipleLineFields = explode(self::FORMAT_NEW_LINE, $this->format);
        foreach ($multipleLineFields as $lineField) {
            if (($patternsName = preg_split(self::_CLEANING_REGEX_, $lineField, -1, PREG_SPLIT_NO_EMPTY))) {
                if (is_array($patternsName)) {
                    foreach ($patternsName as $patternName) {
                        if (!in_array($patternName, $usedKeyList)) {
                            $this->_checkLiableAssociation($patternName);
                            $usedKeyList[] = $patternName;
                        } else {
                            $this->_errorFormatList[] = $this->trans('This key has already been used.', [], 'Admin.Notifications.Error') .
                                ': ' . $patternName;
                        }
                    }
                }
            }
        }
        $this->checkRequiredFields($usedKeyList);

        return !count($this->_errorFormatList);
    }

    /**
     * Checks that all required fields exist in a given fields list.
     * Fills _errorFormatList array in case of absence of a required field.
     *
     * @param array $fieldList
     */
    protected function checkRequiredFields($fieldList)
    {
        foreach (self::getFieldsRequired() as $requiredField) {
            if (!in_array($requiredField, $fieldList)) {
                $this->_errorFormatList[] = $this->trans(
                    'The %s field (in tab %s) is required.',
                    [htmlspecialchars($requiredField), htmlspecialchars($this->getFieldTabName($requiredField))],
                    'Admin.Notifications.Error');
            }
        }
    }

    /**
     * Given a field name, get the name of the tab in which the field name can be found.
     * For ex: Country:name => the tab is 'Country'.
     * There should be only one separator in the string, otherwise throw an exception.
     *
     * @param string $field
     *
     * @return string
     *
     * @throws AddressException
     */
    private function getFieldTabName($field)
    {
        if (strpos($field, ':') === false) {
            // When there is no ':' separator, the field is in the Address tab
            return 'Address';
        }

        $fieldTab = explode(':', $field);
        if (count($fieldTab) === 2) {
            // The part preceding the ':' separator is the name of the tab in which there is the required field
            return $fieldTab[0];
        }

        throw new AddressException('Address format field is not valid');
    }

    /**
     * Returns the error list.
     */
    public function getErrorList()
    {
        return $this->_errorFormatList;
    }

    /**
     * Set the layout key with the liable value
     * example : (firstname) => 'Presta' will result (Presta)
     *         : (firstname-lastname) => 'Presta' and 'Shop' result '(Presta-Shop)'.
     */
    protected static function _setOriginalDisplayFormat(&$formattedValueList, $currentLine, $currentKeyList)
    {
        if ($currentKeyList && is_array($currentKeyList)) {
            $originalFormattedPatternList = explode(' ', $currentLine);
            // Foreach the available pattern
            foreach ($originalFormattedPatternList as $patternNum => $pattern) {
                // Var allows to modify the good formatted key value when multiple key exist into the same pattern
                $mainFormattedKey = '';

                // Multiple key can be found in the same pattern
                foreach ($currentKeyList as $key) {
                    // Check if we need to use an older modified pattern if a key has already be matched before
                    $replacedValue = empty($mainFormattedKey) ? $pattern : $formattedValueList[$mainFormattedKey];

                    $chars = $start = $end = str_replace($key, '', $replacedValue);
                    if (preg_match(self::_CLEANING_REGEX_, $chars)) {
                        if (Tools::substr($replacedValue, 0, Tools::strlen($chars)) == $chars) {
                            $end = '';
                        } else {
                            $start = '';
                        }

                        if ($chars) {
                            $replacedValue = str_replace($chars, '', $replacedValue);
                        }
                    }

                    if (empty($formattedValueList[$key])) {
                        return;
                    }
                    $formattedValue = preg_replace('/^' . $key . '$/', $formattedValueList[$key], $replacedValue, -1, $count);
                    if ($formattedValue) {
                        if ($count) {
                            // Allow to check multiple key in the same pattern,
                            if (empty($mainFormattedKey)) {
                                $mainFormattedKey = $key;
                            }
                            // Set the pattern value to an empty string if an older key has already been matched before
                            if ($mainFormattedKey != $key) {
                                $formattedValueList[$key] = '';
                            }
                            // Store the new pattern value
                            $formattedValueList[$mainFormattedKey] = $start . $formattedValue . $end;
                            unset($originalFormattedPatternList[$patternNum]);
                        }
                    }
                }
            }
        }
    }

    /**
     * Cleaned the layout set by the user.
     */
    public static function cleanOrderedAddress(&$orderedAddressField)
    {
        foreach ($orderedAddressField as &$line) {
            $cleanedLine = '';
            if (($keyList = preg_split(self::_CLEANING_REGEX_, $line, -1, PREG_SPLIT_NO_EMPTY))) {
                foreach ($keyList as $key) {
                    $cleanedLine .= $key . ' ';
                }
                $cleanedLine = trim($cleanedLine);
                $line = $cleanedLine;
            }
        }
    }

    /**
     * Returns the formatted fields with associated values.
     *
     * @param Address $address Address object
     * @param array $addressFormat Address format fields by line
     * @param int|null $id_lang
     *
     * @return array
     */
    public static function getFormattedAddressFieldsValues($address, $addressFormat, $id_lang = null)
    {
        if (!$id_lang) {
            $id_lang = Context::getContext()->language->id;
        }
        $tab = [];
        $temporyObject = [];

        // Check if $address exist and it's an instanciate object of Address
        if ($address instanceof Address) {
            foreach ($addressFormat as $line) {
                if (($keyList = preg_split(self::_CLEANING_REGEX_, $line, -1, PREG_SPLIT_NO_EMPTY)) && is_array($keyList)) {
                    foreach ($keyList as $pattern) {
                        $associateName = explode(':', $pattern);

                        $totalName = count($associateName);
                        if ($totalName == 1 && isset($address->{$associateName[0]})) {
                            $tab[$associateName[0]] = $address->{$associateName[0]};
                        } else {
                            $tab[$pattern] = '';

                            // Check if the property exist in both classes
                            if (($totalName == 2) && class_exists($associateName[0]) &&
                                property_exists($associateName[0], $associateName[1]) &&
                                property_exists($address, 'id_' . strtolower($associateName[0]))) {
                                $idFieldName = 'id_' . strtolower($associateName[0]);

                                if (!isset($temporyObject[$associateName[0]])) {
                                    $temporyObject[$associateName[0]] = new $associateName[0]($address->{$idFieldName});
                                }
                                $tab[$pattern] = is_array($temporyObject[$associateName[0]]->{$associateName[1]}) ?
                                    (
                                        isset($temporyObject[$associateName[0]]->{$associateName[1]}[$id_lang]) ?
                                        $temporyObject[$associateName[0]]->{$associateName[1]}[$id_lang] :
                                        ''
                                    ) :
                                    $temporyObject[$associateName[0]]->{$associateName[1]};
                            }
                        }
                    }
                    AddressFormat::_setOriginalDisplayFormat($tab, $line, $keyList);
                }
            }
        }
        AddressFormat::cleanOrderedAddress($addressFormat);

        return $tab;
    }

    /**
     * Generates the full address text.
     *
     * @param Address $address
     * @param array $patternRules A defined rules array to avoid some pattern
     * @param string $newLine A string containing the newLine format
     * @param string $separator A string containing the separator format
     * @param array $style
     *
     * @return string
     */
    public static function generateAddress(Address $address, $patternRules = [], $newLine = self::FORMAT_NEW_LINE, $separator = ' ', $style = [])
    {
        $addressFields = AddressFormat::getOrderedAddressFields($address->id_country);
        $addressFormatedValues = AddressFormat::getFormattedAddressFieldsValues($address, $addressFields);

        $addressText = '';
        foreach ($addressFields as $line) {
            if (($patternsList = preg_split(self::_CLEANING_REGEX_, $line, -1, PREG_SPLIT_NO_EMPTY))) {
                $tmpText = '';
                foreach ($patternsList as $pattern) {
                    if (!array_key_exists('avoid', $patternRules) || !in_array($pattern, $patternRules['avoid'])) {
                        $tmpText .= (isset($addressFormatedValues[$pattern]) && !empty($addressFormatedValues[$pattern])) ?
                                (((isset($style[$pattern])) ?
                                    (sprintf($style[$pattern], $addressFormatedValues[$pattern])) :
                                    $addressFormatedValues[$pattern]) . $separator) : '';
                    }
                }
                $tmpText = trim($tmpText);
                $addressText .= (!empty($tmpText)) ? $tmpText . $newLine : '';
            }
        }

        $addressText = preg_replace('/' . preg_quote($newLine, '/') . '$/i', '', $addressText);
        $addressText = rtrim($addressText, $separator);

        return $addressText;
    }

    /**
     * Generate formatted Address string for display on Smarty templates.
     *
     * @param array $params Address parameters
     * @param Smarty $smarty Smarty instance
     *
     * @return string Formatted Address string
     */
    public static function generateAddressSmarty($params, &$smarty)
    {
        return AddressFormat::generateAddress(
            $params['address'],
            (isset($params['patternRules']) ? $params['patternRules'] : []),
            (isset($params['newLine']) ? $params['newLine'] : self::FORMAT_NEW_LINE),
            (isset($params['separator']) ? $params['separator'] : ' '),
            (isset($params['style']) ? $params['style'] : [])
        );
    }

    /**
     * Returns selected fields required for an address in an array according to a selection hash.
     *
     * @return array String values
     */
    public static function getValidateFields($className)
    {
        $propertyList = [];

        if (class_exists($className)) {
            $object = new $className();
            $reflect = new ReflectionObject($object);

            // Check if the property is accessible
            $publicProperties = $reflect->getProperties(ReflectionProperty::IS_PUBLIC);
            foreach ($publicProperties as $property) {
                $propertyName = $property->getName();
                if ((!in_array($propertyName, AddressFormat::$forbiddenPropertyList)) &&
                        (!preg_match('#id|id_\w#', $propertyName))) {
                    $propertyList[] = $propertyName;
                }
            }
            unset(
                $object,
                $reflect
            );
        }

        return $propertyList;
    }

    /**
     * Return a list of liable class of the className.
     *
     * @param string $className
     *
     * @return array
     */
    public static function getLiableClass($className)
    {
        $objectList = [];

        if (class_exists($className)) {
            $object = new $className();
            $reflect = new ReflectionObject($object);

            // Get all the name object liable to the Address class
            $publicProperties = $reflect->getProperties(ReflectionProperty::IS_PUBLIC);
            foreach ($publicProperties as $property) {
                $propertyName = $property->getName();
                if (preg_match('#id_\w#', $propertyName) && strlen($propertyName) > 3) {
                    $nameObject = ucfirst(substr($propertyName, 3));
                    if (!in_array($nameObject, self::$forbiddenClassList) &&
                            class_exists($nameObject)) {
                        $objectList[$nameObject] = new $nameObject();
                    }
                }
            }
            unset(
                $object,
                $reflect
            );
        }

        return $objectList;
    }

    /**
     * Returns address format fields in array by country.
     *
     * @param int $idCountry If null using PS_COUNTRY_DEFAULT
     * @param bool $splitAll
     * @param bool $cleaned
     *
     * @return array String field address format
     */
    public static function getOrderedAddressFields($idCountry = 0, $splitAll = false, $cleaned = false)
    {
        $out = [];
        $fieldSet = explode(AddressFormat::FORMAT_NEW_LINE, AddressFormat::getAddressCountryFormat($idCountry));
        foreach ($fieldSet as $fieldItem) {
            if ($splitAll) {
                $keyList = $cleaned ? preg_split(self::_CLEANING_REGEX_, $fieldItem, -1, PREG_SPLIT_NO_EMPTY) : explode(' ', $fieldItem);
                foreach ($keyList as $wordItem) {
                    $out[] = trim($wordItem);
                }
            } else {
                $out[] = ($cleaned) ? implode(' ', preg_split(self::_CLEANING_REGEX_, trim($fieldItem), -1, PREG_SPLIT_NO_EMPTY))
                    : trim($fieldItem);
            }
        }

        return $out;
    }

    /**
     * Return a data array containing ordered, formatedValue and object fields.
     */
    public static function getFormattedLayoutData($address)
    {
        $layoutData = [];

        if ($address && $address instanceof Address) {
            $layoutData['ordered'] = AddressFormat::getOrderedAddressFields((int) $address->id_country);
            $layoutData['formated'] = AddressFormat::getFormattedAddressFieldsValues($address, $layoutData['ordered']);
            $layoutData['object'] = [];

            $reflect = new ReflectionObject($address);
            $publicProperties = $reflect->getProperties(ReflectionProperty::IS_PUBLIC);
            foreach ($publicProperties as $property) {
                if (isset($address->{$property->getName()})) {
                    $layoutData['object'][$property->getName()] = $address->{$property->getName()};
                }
            }
        }

        return $layoutData;
    }

    /**
     * Returns address format by country if not defined using default country.
     *
     * @param int $idCountry Country ID
     *
     * @return string field address format
     */
    public static function getAddressCountryFormat($idCountry = 0)
    {
        $idCountry = (int) $idCountry;

        $tmpObj = new AddressFormat();
        $tmpObj->id_country = $idCountry;
        $out = $tmpObj->getFormat($tmpObj->id_country);
        unset($tmpObj);

        return $out;
    }

    /**
     * Returns address format by Country.
     *
     * @param int $idCountry Country ID
     *
     * @return string field Address format
     */
    public function getFormat($idCountry)
    {
        $out = $this->getFormatDB($idCountry);
        if (empty($out)) {
            $out = $this->getFormatDB((int) Configuration::get('PS_COUNTRY_DEFAULT'));
        }
        if (Country::isNeedDniByCountryId($idCountry) && false === strpos($out, 'dni')) {
            $out .= AddressFormat::FORMAT_NEW_LINE . 'dni';
        }

        return $out;
    }

    /**
     * Get Address format from DB.
     *
     * @param int $idCountry Country ID
     *
     * @return false|string|null Address format
     *
     * @since 1.7.0
     */
    protected function getFormatDB($idCountry)
    {
        if (!Cache::isStored('AddressFormat::getFormatDB' . $idCountry)) {
            $format = Db::getInstance()->getValue('
			SELECT format
			FROM `' . _DB_PREFIX_ . $this->def['table'] . '`
			WHERE `id_country` = ' . (int) $idCountry);
            $format = trim($format);
            Cache::store('AddressFormat::getFormatDB' . $idCountry, $format);

            return $format;
        }

        return Cache::retrieve('AddressFormat::getFormatDB' . $idCountry);
    }

    /**
     * @see ObjectModel::getFieldsRequired()
     */
    public static function getFieldsRequired()
    {
        $address = new CustomerAddress();

        return array_unique(array_merge($address->getFieldsRequiredDB(), AddressFormat::$requireFormFieldsList));
    }
}
