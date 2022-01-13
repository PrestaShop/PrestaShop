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

    public const TYPE_VALUE = 'value';
    public const TYPE_LANGUAGE = 'language';
    public const TYPE_PARENT = 'parent';
    public const TYPE_LIST = 'list';

    public static $languages;

    /** @var string */
    private $type;

    /** @var string|null */
    private $name = null;

    /** @var string|null */
    private $value = null;

    /** @var array */
    private $attributes = [];

    /** @var array */
    private $nodes = [];

    private function __construct(string $type, ?string $name = null, ?string $value = null, array $attributes = [], array $nodes = [])
    {
        $this->type = $type;
        $this->name = $name;
        $this->value = $value;
        $this->attributes = $attributes;
        $this->nodes = $nodes;
    }

    /**
     * Create new ApiNode instance of type "value"
     *
     * @param string $name
     * @param string|null $value
     *
     * @return \ApiNode
     */
    private static function value(string $name, ?string $value = null): self
    {
        return new ApiNode(self::TYPE_VALUE, $name, $value);
    }

    /**
     * Create new ApiNode instance of type "lang"
     * Lang ApiNode serves as ApiNode with name. All the translated values are supposed to be children of that Node.
     *
     * @param string $name
     *
     * @return \ApiNode
     */
    private static function lang(string $name): self
    {
        return new ApiNode(self::TYPE_LANGUAGE, $name);
    }

    /**
     * Create new ApiNode instance of type "parent"
     * Parent ApiNode serves as ApiNode with name and array of child nodes (and potentionally array of attributes)
     * Its children nodes are meant to be rendered as associative arrays
     *
     * @param string $name
     * @param array $attributes
     *
     * @return \ApiNode
     */
    public static function parent(?string $name = null, array $attributes = []): self
    {
        return new ApiNode(self::TYPE_PARENT, $name, null, $attributes);
    }

    /**
     * Create new ApiNode instance of type "list"
     * List ApiNode serves as ApiNode with name and array of child nodes.
     * Its children nodes are meant to be rendered as non-associative arrays
     *
     * @param string $name
     * @param array $attributes
     *
     * @return \ApiNode
     */
    public static function list(?string $name = null, array $attributes = []): self
    {
        return new ApiNode(self::TYPE_LIST, $name, null, $attributes);
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @return string|null
     */
    public function getValue(): ?string
    {
        return $this->value;
    }

    /**
     * @return array
     */
    public function getAttributes(): array
    {
        return $this->attributes;
    }

    /**
     * @return array
     */
    public function getNodes(): array
    {
        return $this->nodes;
    }

    /**
     * @param string $type
     *
     * @return self
     */
    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @param string|null $name
     *
     * @return self
     */
    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @param string|null $value
     *
     * @return self
     */
    public function setValue(?string $value): self
    {
        $this->value = $value;

        return $this;
    }

    /**
     * @param array $attributes
     *
     * @return self
     */
    public function setAttributes(array $attributes): self
    {
        $this->attributes = $attributes;

        return $this;
    }

    /**
     * @param array $nodes
     *
     * @return self
     */
    public function setNodes(array $nodes): self
    {
        $this->nodes = $nodes;

        return $this;
    }

    /**
     * @param string $name
     * @param string|null $value
     *
     * @return self
     */
    public function addAttribute(string $name, ?string $value = null): self
    {
        $this->attributes[$name] = $value;

        return $this;
    }

    /**
     * Appends $node as child to current ApiNode
     *
     * @param ApiNode $node
     *
     * @return ApiNode self
     */
    public function addApiNode(ApiNode $node): self
    {
        $this->nodes[] = $node;

        return $this;
    }

    /**
     * Create new ApiNode of type "value" and appends it as child to current ApiNode
     *
     * @param string $name
     * @param mixed $value
     *
     * @return ApiNode Created child node
     */
    public function addNode(string $name, ?string $value = null): self
    {
        $newNode = self::value($name, $value);
        $this->nodes[] = $newNode;

        return $newNode;
    }

    /**
     * Create new ApiNode of type "lang" and appends it as child to current ApiNode
     *
     * @param string $name
     * @param array $values
     *
     * @return ApiNode Created child node
     */
    public function addLanguageNode(string $name): self
    {
        $newNode = self::lang($name);
        $this->nodes[] = $newNode;

        return $newNode;
    }

    /**
     * Create new ApiNode of type "parent" and appends it as child to current ApiNode
     *
     * @param string $name
     * @param array $attributes
     *
     * @return ApiNode Created child node
     */
    public function addParentNode(string $name = null, array $attributes = []): self
    {
        $newNode = self::parent($name, $attributes);
        $this->nodes[] = $newNode;

        return $newNode;
    }

    /**
     * Create new ApiNode of type "list" and appends it as child to current ApiNode
     *
     * @param string $name
     * @param array $attributes
     *
     * @return ApiNode Created child node
     */
    public function addListNode(string $name = null, array $attributes = [])
    {
        $newNode = self::list($name, $attributes);
        $this->nodes[] = $newNode;

        return $newNode;
    }

    /**
     * Transform $field array into ApiNode and appends it as child to current node
     *
     * @param array $field
     * @param string $schemaToDisplay
     *
     * @return self
     */
    public function addField(array $field, string $schemaToDisplay = ''): self
    {
        $newNode = self::value($field['sqlId']);

        if (isset($field['encode'])) {
            $newNode->addAttribute('encode', $field['encode']);
        }

        if (!empty($field['synopsis_details']) && $schemaToDisplay !== 'blank') {
            foreach ($field['synopsis_details'] as $name => $detail) {
                $newNode->addAttribute($name, is_array($detail) ? implode(' ', $detail) : $detail);
            }
        }

        // display i18n fields
        if (!empty($field['i18n'])) {
            foreach (self::$languages as $language) {
                $langAttributes = ['id' => $language];

                if (isset($field['synopsis_details']) || (isset($field['value']) && is_array($field['value']))) {
                    $langAttributes['xlink:href'] = WebserviceOutputBuilderCore::$wsUrl . 'languages/' . $language;
                    if (isset($field['synopsis_details']) && $schemaToDisplay != 'blank') {
                        $langAttributes['format'] = 'isUnsignedId';
                    }
                }

                $newNode->setType(self::TYPE_LANGUAGE);
                $newNode->addNode('language', $field['value'][$language] ?? '')
                    ->setAttributes($langAttributes);
            }
        } else {
            // display not i18n fields value
            if (array_key_exists('xlink_resource', $field) && $schemaToDisplay != 'blank') {
                if (!is_array($field['xlink_resource'])) {
                    $xlink = WebserviceOutputBuilderCore::$wsUrl . $field['xlink_resource'] . '/' . $field['value'];
                } else {
                    $xlink = WebserviceOutputBuilderCore::$wsUrl . $field['xlink_resource']['resourceName'] . '/';

                    if (isset($field['xlink_resource']['subResourceName'])) {
                        $xlink .= $field['xlink_resource']['subResourceName'] . '/' . $field['object_id'] . '/';
                    }

                    $xlink .= $field['value'];
                }
                $newNode->addAttribute('xlink:href', $xlink);
            }

            if (isset($field['getter']) && $schemaToDisplay != 'blank') {
                $newNode->addAttribute('notFilterable', 'true');
            }

            if (isset($field['setter']) && $field['setter'] == false && $schemaToDisplay == 'synopsis') {
                $newNode->addAttribute('read_only', 'true');
            }

            if (array_key_exists('value', $field)) {
                $newNode->setValue($field['value']);
            }
        }

        $this->nodes[] = $newNode;

        return $newNode;
    }
}
