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
class ApiNode
{
    public const TYPE_VALUE = "value";
    public const TYPE_LANGUAGE = "language";
    public const TYPE_PARENT = "parent";
    public const TYPE_LIST = "list";

    public static $languages;
    private $type;
    private $name;
    private $value;
    private $values;
    private $attributes;
    private $nodes;

    private function __construct($type, $name = null, $value = null, $attributes = [], $nodes = [])
    {
        $this->type = $type;
        $this->name = $name;
        $this->value = $value;
        $this->attributes = $attributes;
        $this->nodes = $nodes;
    }

    private static function value($name, $value = null)
    {
        return new ApiNode(self::TYPE_VALUE, $name, $value);
    }

    private static function lang($name, $values = null)
    {
        return new ApiNode(self::TYPE_LANGUAGE, $name, $values);
    }

    public static function parent($name = null, $attributes = [])
    {
        return new ApiNode(self::TYPE_PARENT, $name, null, $attributes);
    }

    public static function list($name = null, $attributes = [])
    {
        return new ApiNode(self::TYPE_LIST, $name, null, $attributes);
    }

    public function getType()
    {
        return $this->type;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getValue()
    {
        return $this->value;
    }

    public function getAttributes()
    {
        return $this->attributes;
    }

    public function getNodes()
    {
        return $this->nodes;
    }

    public function setType($type)
    {
        $this->type = $type;
        return $this;
    }

    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    public function setValue($value)
    {
        $this->value = $value;
        return $this;
    }

    public function setAttributes($attributes)
    {
        $this->attributes = $attributes;
        return $this;
    }

    public function setNodes($nodes)
    {
        $this->nodes = $nodes;
        return $this;
    }

    public function addAttribute($name, $value)
    {
        $this->attributes[$name] = $value;
    }

    /**
     * @param ApiNode $node
     * @return ApiNode self
     */
    public function addApiNode($node)
    {
        $this->nodes[] = $node;
        return $this;
    }

    /**
     * @param string $name
     * @param mixed $value
     * @return ApiNode Created child node
     */
    public function addNode($name, $value = null)
    {
        $newNode = self::value($name, $value);
        $this->nodes[] = $newNode;
        return $newNode;
    }

    /**
     * @param string $name
     * @param array $values
     * @return ApiNode Created child node
     */
    public function addLanguageNode($name, $values)
    {
        $newNode = self::lang($name, $values);
        $this->nodes[] = $newNode;
        return $newNode;
    }

    /**
     * @param string $name
     * @param array|null $attributes
     * 
     * @return ApiNode Created child node
     */
    public function addParentNode($name = null, $attributes = [])
    {
        $newNode = self::parent($name, $attributes);
        $this->nodes[] = $newNode;
        return $newNode;
    }

    /**
     * @param string $name
     * @param array|null $attributes
     * 
     * @return ApiNode Created child node
     */
    public function addListNode($name = null, $attributes = [])
    {
        $newNode = self::list($name, $attributes);
        $this->nodes[] = $newNode;
        return $newNode;
    }

    public function addField($field)
    {
        $newNode = self::value($field['sqlId']);

        if (isset($field['encode'])) {
            $newNode->addAttribute("encode", $field['encode']);
        }

        if (!empty($field['synopsis_details']) && $this->schemaToDisplay !== 'blank') {
            foreach ($field['synopsis_details'] as $name => $detail) {
                $newNode->addAttribute($name, is_array($detail) ? implode(' ', $detail) : $detail);
            }
        }


        // display i18n fields
        if (isset($field['i18n']) && $field['i18n']) {
            foreach (self::$languages as $language) {
                $langAttributes = ["id" => $language];

                if (isset($field['synopsis_details']) || (isset($field['value']) && is_array($field['value']))) {
                    $langAttributes["xlink:href"] = WebserviceOutputBuilderCore::$wsUrl . 'languages/' . $language;
                    if (isset($field['synopsis_details']) && $this->schemaToDisplay != 'blank') {
                        $langAttributes["format"] = "isUnsignedId";
                    }
                }

                $newNode->setType(self::TYPE_LANGUAGE);
                $newNode->addNode("language", $field['value'][$language] ?? '')
                        ->setAttributes($langAttributes);
            }
        } else {

            // display not i18n fields value
            if (array_key_exists('xlink_resource', $field) && $this->schemaToDisplay != 'blank') {
                if (!is_array($field['xlink_resource'])) {
                    $xlink = WebserviceOutputBuilderCore::$wsUrl . $field['xlink_resource'] . '/' . $field['value'];
                } else {
                    $xlink = WebserviceOutputBuilderCore::$wsUrl . $field['xlink_resource']['resourceName'] . '/';

                    if (isset($field['xlink_resource']['subResourceName'])) {
                        $xlink .= $field['xlink_resource']['subResourceName'] . '/' . $field['object_id'] . '/';
                    }

                    $xlink .= $field['value'];
                }
                $newNode->addAttribute("xlink:href", $xlink);
            }

            if (isset($field['getter']) && $this->schemaToDisplay != 'blank') {
                $newNode->addAttribute("notFilterable", "true");
            }

            if (isset($field['setter']) && $field['setter'] == false && $this->schemaToDisplay == 'synopsis') {
                $newNode->addAttribute("read_only", "true");
            }

            if (array_key_exists('value', $field)) {
                $newNode->setValue($field['value']);
            }
        }

        $this->nodes[] = $newNode;
        return $newNode;
    }
}