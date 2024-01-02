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

namespace PrestaShopBundle\Install;

use PrestaShop\PrestaShop\Adapter\Entity\Db;
use PrestaShop\PrestaShop\Adapter\Entity\DbQuery;
use PrestaShop\PrestaShop\Adapter\Entity\Image;
use PrestaShop\PrestaShop\Adapter\Entity\ImageManager;
use PrestaShop\PrestaShop\Adapter\Entity\ImageType;
use PrestaShop\PrestaShop\Adapter\Entity\Pack;
use PrestaShop\PrestaShop\Adapter\Entity\Shop;
use PrestaShop\PrestaShop\Adapter\Entity\StockAvailable;
use PrestaShop\PrestaShop\Adapter\Entity\Tag;
use PrestaShop\PrestaShop\Adapter\Entity\Tools;
use PrestaShop\PrestaShop\Core\Foundation\Filesystem\FileSystem;
use PrestaShopBundle\Install\EntityLoader\FileLoader;
use PrestaShopDatabaseException;
use PrestashopInstallerException;
use SimpleXMLElement;

class XmlLoader
{
    /**
     * @var LanguageList
     */
    protected $language;

    /**
     * @var \Symfony\Bundle\FrameworkBundle\Translation\Translator
     */
    protected $translator;

    /**
     * @var array List of languages stored as array(id_lang => iso)
     */
    protected $languages = [];

    /**
     * @var array List of errors
     */
    protected $errors = [];

    protected $data_path;
    protected $lang_path;
    protected $img_path;
    public $path_type;

    protected $ids = [];

    protected $primaries = [];

    protected $delayed_inserts = [];

    /**
     * @var FileLoader
     */
    private $fileLoader;

    public function __construct()
    {
        $this->language = LanguageList::getInstance();
        $this->setDefaultPath();
    }

    /**
     * Set list of installed languages.
     *
     * @param array $languages array(id_lang => iso)
     */
    public function setLanguages(array $languages)
    {
        $this->languages = $languages;
    }

    public function setTranslator($translator)
    {
        $this->translator = $translator;

        return $translator;
    }

    public function setDefaultPath()
    {
        $this->path_type = 'common';
        $this->data_path = _PS_INSTALL_DATA_PATH_ . 'xml/';
        $this->lang_path = _PS_INSTALL_LANGS_PATH_;
        $this->img_path = _PS_INSTALL_DATA_PATH_ . 'img/';
        $this->fileLoader = new FileLoader($this->data_path, $this->lang_path);
    }

    public function setFixturesPath($path = null)
    {
        if ($path === null) {
            $path = _PS_INSTALL_FIXTURES_PATH_ . 'fashion/';
        }

        $this->path_type = 'fixture';
        $this->data_path = $path . 'data/';
        $this->lang_path = $path . 'langs/';
        $this->img_path = $path . 'img/';
        $this->fileLoader = new FileLoader($this->data_path, $this->lang_path);
    }

    /**
     * Get list of errors.
     *
     * @return array
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * Add an error.
     *
     * @param string $error
     */
    public function setError($error)
    {
        $this->errors[] = $error;
    }

    /**
     * Store an ID related to an entity and its identifier (E.g. we want to save that product with ID "ipod_nano" has the ID 1).
     *
     * @param string $entity
     * @param string $identifier
     * @param int $id
     */
    public function storeId($entity, $identifier, $id)
    {
        $this->ids[$entity . ':' . $identifier] = $id;
    }

    /**
     * Retrieve an ID related to an entity and its identifier.
     *
     * @param string $entity
     * @param string $identifier
     */
    public function retrieveId($entity, $identifier)
    {
        return isset($this->ids[$entity . ':' . $identifier]) ? $this->ids[$entity . ':' . $identifier] : 0;
    }

    public function getIds()
    {
        return $this->ids;
    }

    public function setIds($ids)
    {
        $this->ids = $ids;
    }

    /**
     * @return string[] Entity names
     *
     * @throws PrestashopInstallerException
     */
    public function getSortedEntities()
    {
        // Browse all XML files from data/xml directory
        $entities = [];
        $dependencies = [];
        foreach (scandir($this->data_path) as $file) {
            if (preg_match('#^(.+)\.xml$#', $file, $m)) {
                $entity = $m[1];
                $xml = $this->fileLoader->load($entity);

                // Store entities dependencies (with field type="relation")
                if ($xml instanceof \SimpleXMLElement && isset($xml->fields, $xml->fields->field)) {
                    foreach ($xml->fields->field as $field) {
                        if ($field['relation'] && $field['relation'] != $entity) {
                            if (!isset($dependencies[(string) $field['relation']])) {
                                $dependencies[(string) $field['relation']] = [];
                            }
                            $dependencies[(string) $field['relation']][] = $entity;
                        }
                    }
                }
                $entities[] = $entity;
            }
        }

        // Sort entities to populate database in good order (E.g. zones before countries)
        do {
            $current = (isset($sort_entities)) ? $sort_entities : [];
            $sort_entities = [];
            foreach ($entities as $key => $entity) {
                if (isset($dependencies[$entity])) {
                    $min = count($entities) - 1;
                    foreach ($dependencies[$entity] as $item) {
                        if (($key = array_search($item, $sort_entities)) !== false) {
                            $min = min($min, $key);
                        }
                    }
                    if ($min == 0) {
                        array_unshift($sort_entities, $entity);
                    } else {
                        array_splice($sort_entities, $min, 0, [$entity]);
                    }
                } else {
                    $sort_entities[] = $entity;
                }
            }
            $entities = $sort_entities;
        } while ($current != $sort_entities);

        return $sort_entities;
    }

    /**
     * Read all XML files from data folder and populate tables.
     *
     * @throws PrestashopInstallerException
     */
    public function populateFromXmlFiles()
    {
        $entities = $this->getSortedEntities();

        // Populate entities
        foreach ($entities as $entity) {
            $this->populateEntity($entity);
        }
    }

    /**
     * Populate an entity.
     *
     * @param string $entity Entity name to populate
     *
     * @throws PrestashopInstallerException
     */
    public function populateEntity($entity)
    {
        $populateEntityMethod = 'populateEntity' . Tools::toCamelCase($entity);
        if (method_exists($this, $populateEntityMethod)) {
            $this->$populateEntityMethod();

            return;
        }

        if (str_starts_with($entity, '.') || str_starts_with($entity, '_')) {
            return;
        }

        $xml = $this->fileLoader->load($entity);

        // Read list of fields
        if (!$xml instanceof \SimpleXMLElement && !empty($xml->fields)) {
            throw new PrestashopInstallerException('List of fields not found for entity ' . $entity);
        }

        $is_multi_lang_entity = $this->isMultilang($entity);
        $xml_langs = $multilang_columns = [];
        $default_lang = null;

        if ($is_multi_lang_entity) {
            $multilang_columns = $this->getColumns($entity, true);
            foreach ($this->languages as $id_lang => $iso) {
                if ($iso == $this->language->getLanguageIso()) {
                    $default_lang = $id_lang;
                }

                try {
                    $xml_langs[$id_lang] = $this->fileLoader->load($entity, $iso);
                } catch (PrestashopInstallerException $e) {
                    $xml_langs[$id_lang] = null;
                }
            }
        }

        // Load all row for current entity and prepare data to be populated
        $i = 0;
        foreach ($xml->entities->$entity as $node) {
            $data = [];
            $identifier = (string) $node['id'];

            // Read attributes
            foreach ($node->attributes() as $k => $v) {
                if ($k != 'id') {
                    $data[$k] = (string) $v;
                }
            }

            // Read cdatas
            foreach ($node->children() as $child) {
                $data[$child->getName()] = (string) $child;
            }

            // Load multilang data
            $data_lang = [];
            if ($is_multi_lang_entity) {
                $xpath_query = $entity . '[@id="' . $identifier . '"]';
                foreach ($xml_langs as $id_lang => $xml_lang) {
                    if (!$xml_lang) {
                        continue;
                    }

                    if (($node_lang = $xml_lang->xpath($xpath_query)) || ($node_lang = $xml_langs[$default_lang]->xpath($xpath_query))) {
                        $node_lang = $node_lang[0];
                        foreach ($multilang_columns as $column => $is_text) {
                            $value = '';
                            if ($node_lang[$column]) {
                                $value = (string) $node_lang[$column];
                            }

                            if ($node_lang->$column) {
                                $value = (string) $node_lang->$column;
                            }
                            $data_lang[$column][$id_lang] = $value;
                        }
                    }
                }
            }

            $data = $this->rewriteRelationedData($entity, $data);
            $createEntityMethod = 'createEntity' . Tools::toCamelCase($entity);
            if (method_exists($this, $createEntityMethod)) {
                // Create entity with custom method in current class
                $this->$createEntityMethod($identifier, $data, $data_lang);
            } else {
                $this->createEntity($entity, $identifier, (string) $xml->fields['class'], $data, $data_lang);
            }

            if ($xml->fields['image']) {
                $copyImagesMethod = 'copyImages' . Tools::toCamelCase($entity);
                if (method_exists($this, $copyImagesMethod)) {
                    $this->{$copyImagesMethod}($identifier, $data);
                } else {
                    $this->copyImages($entity, $identifier, (string) $xml->fields['image'], $data);
                }
            }
            ++$i;

            if ($i >= 100) {
                $this->flushDelayedInserts();
                $i = 0;
            }
        }

        $this->flushDelayedInserts();
        $this->fileLoader->flushCache($entity);
    }

    protected function getFallBackToDefaultLanguage($iso)
    {
        return file_exists($this->lang_path . $iso . '/data/') ? $iso : 'en';
    }

    protected function getFallBackToDefaultEntityLanguage($iso, $entity)
    {
        if ($this->getFallBackToDefaultLanguage($iso) === 'en') {
            return 'en';
        }

        return file_exists($this->lang_path . $this->getFallBackToDefaultLanguage($iso) . '/data/' . $entity . '.xml') ? $iso : 'en';
    }

    /**
     * Special case for "country" entity.
     */
    public function populateEntityCountry()
    {
        $xml = $this->fileLoader->load('country');

        // Read list of fields
        if (empty($xml->fields)) {
            throw new PrestashopInstallerException('List of fields not found for entity country');
        }
        $langs = [];
        $languageList = LanguageList::getInstance();
        foreach ($this->languages as $id_lang => $iso) {
            $langs[$id_lang] = $languageList->getCountriesByLanguage($iso);
        }

        // Load all row for current entity and prepare data to be populated
        $i = 0;
        if ($xml->entities->country instanceof SimpleXMLElement) {
            foreach ($xml->entities->country as $node) {
                $data = [];

                // Read attributes
                $identifier = '';
                foreach ($node->attributes() as $k => $v) {
                    if ($k == 'id') {
                        $identifier = (string) $v;
                        continue;
                    }
                    $data[$k] = (string) $v;
                }

                // Load multilang data
                $data_lang = [];
                foreach ($langs as $id_lang => $countries) {
                    $data_lang['name'][$id_lang] = $countries[strtolower($identifier)] ?? '';
                }

                $data = $this->rewriteRelationedData('country', $data);
                $this->createEntity('country', $identifier, (string) $xml->fields['class'], $data, $data_lang);
                ++$i;

                if ($i >= 100) {
                    $this->flushDelayedInserts();
                    $i = 0;
                }
            }
        }

        $this->flushDelayedInserts();
    }

    /**
     * Special case for "tag" entity.
     */
    public function populateEntityTag()
    {
        foreach ($this->languages as $id_lang => $iso) {
            if (!file_exists($this->lang_path . $this->getFallBackToDefaultLanguage($iso) . '/data/tag.xml')) {
                continue;
            }

            $xml = $this->fileLoader->load('tag', $this->getFallBackToDefaultLanguage($iso));
            $tags = [];
            foreach ($xml->tag as $tag_node) {
                $products = trim((string) $tag_node['products']);
                if (!$products) {
                    continue;
                }

                foreach (explode(',', $products) as $product) {
                    $product = trim($product);
                    $product_id = $this->retrieveId('product', $product);
                    if (!isset($tags[$product_id])) {
                        $tags[$product_id] = [];
                    }
                    $tags[$product_id][] = trim((string) $tag_node['name']);
                }
            }

            foreach ($tags as $id_product => $tag_list) {
                Tag::addTags($id_lang, $id_product, $tag_list);
            }
        }
    }

    /**
     * Check fields related to an other entity, and replace their values by the ID created by the other entity.
     *
     * @param string $entity
     * @param array $data
     */
    protected function rewriteRelationedData($entity, array $data)
    {
        $xml = $this->fileLoader->load($entity);
        foreach ($xml->fields->field as $field) {
            if ($field['relation']) {
                $id = $this->retrieveId((string) $field['relation'], $data[(string) $field['name']]);
                if (!$id && $data[(string) $field['name']] && is_numeric($data[(string) $field['name']])) {
                    $id = $data[(string) $field['name']];
                }
                $data[(string) $field['name']] = $id;
            }
        }

        return $data;
    }

    public function flushDelayedInserts()
    {
        foreach ($this->delayed_inserts as $entity => $queries) {
            $type = Db::INSERT_IGNORE;
            if ($entity == 'access') {
                $type = Db::REPLACE;
            }

            if (!Db::getInstance()->insert($entity, $queries, false, true, $type)) {
                $this->setError($this->translator->trans('An SQL error occurred for entity <i>%entity%</i>: <i>%message%</i>', ['%entity%' => $entity, '%message%' => Db::getInstance()->getMsgError()], 'Install'));
            }
            unset($this->delayed_inserts[$entity]);
        }
    }

    /**
     * Create a simple entity with all its data and lang data
     * If a methode createEntity$entity exists, use it. Else if $classname is given, use it. Else do a simple insert in database.
     *
     * @param string $entity
     * @param string $identifier
     * @param string $classname
     * @param array $data
     * @param array $data_lang
     */
    public function createEntity($entity, $identifier, $classname, array $data, array $data_lang = [])
    {
        $xml = $this->fileLoader->load($entity);
        if ($classname) {
            $classname = '\\' . $classname;
            // Create entity with ObjectModel class
            $object = new $classname();
            $object->hydrate($data);
            if ($data_lang) {
                $object->hydrate($data_lang);
            }
            $object->add(true, isset($xml->fields['null']));
            $entity_id = $object->id;
            unset($object);
        } else {
            // Generate primary key manually
            $primary = '';
            $entity_id = 0;
            if (!$xml->fields['primary']) {
                $primary = 'id_' . $entity;
            } elseif (!str_contains((string) $xml->fields['primary'], ',')) {
                $primary = (string) $xml->fields['primary'];
            }
            unset($xml);

            if ($primary) {
                $entity_id = $this->generatePrimary($entity, $primary);
                $data[$primary] = $entity_id;
            }

            // Store INSERT queries in order to optimize install with grouped inserts
            $this->delayed_inserts[$entity][] = array_map('pSQL', $data);
            if ($data_lang) {
                $real_data_lang = [];
                foreach ($data_lang as $field => $list) {
                    foreach ($list as $id_lang => $value) {
                        $real_data_lang[$id_lang][$field] = $value;
                    }
                }

                foreach ($real_data_lang as $id_lang => $insert_data_lang) {
                    $insert_data_lang['id_' . $entity] = $entity_id;
                    $insert_data_lang['id_lang'] = $id_lang;
                    $this->delayed_inserts[$entity . '_lang'][] = array_map('pSQL', $insert_data_lang);
                }

                // Store INSERT queries for _shop associations
                $entity_asso = Shop::getAssoTable($entity);
                if ($entity_asso !== false && $entity_asso['type'] == 'shop') {
                    $this->delayed_inserts[$entity . '_shop'][] = [
                        'id_shop' => 1,
                        'id_' . $entity => $entity_id,
                    ];
                }
            }
        }

        $this->storeId($entity, $identifier, $entity_id);
    }

    public function createEntityAttribute($identifier, array $data, array $data_lang = [])
    {
        $this->createEntity('attribute', $identifier, 'ProductAttribute', $data, $data_lang);
    }

    public function createEntityConfiguration($identifier, array $data, array $data_lang)
    {
        if (Db::getInstance()->getValue('SELECT id_configuration FROM ' . _DB_PREFIX_ . 'configuration WHERE name = \'' . pSQL($data['name']) . '\'')) {
            return;
        }

        $entity = 'configuration';
        $entity_id = $this->generatePrimary($entity, 'id_configuration');
        $data['id_configuration'] = $entity_id;

        // Store INSERT queries in order to optimize install with grouped inserts
        $this->delayed_inserts[$entity][] = array_map('pSQL', $data);
        if ($data_lang) {
            $real_data_lang = [];
            foreach ($data_lang as $field => $list) {
                foreach ($list as $id_lang => $value) {
                    $real_data_lang[$id_lang][$field] = $value;
                }
            }

            foreach ($real_data_lang as $id_lang => $insert_data_lang) {
                $insert_data_lang['id_' . $entity] = $entity_id;
                $insert_data_lang['id_lang'] = $id_lang;
                $this->delayed_inserts[$entity . '_lang'][] = array_map('pSQL', $insert_data_lang);
            }
        }

        $this->storeId($entity, $identifier, $entity_id);
    }

    /**
     * @param string $identifier
     * @param array $data
     * @param array $data_lang
     *
     * @return $this
     *
     * @throws PrestaShopDatabaseException
     */
    public function createEntityPack($identifier, array $data, array $data_lang)
    {
        Pack::addItem($data['id_product_pack'], $data['id_product_item'], $data['quantity']);

        return $this;
    }

    public function createEntityStockAvailable($identifier, array $data, array $data_lang)
    {
        $stock_available = new StockAvailable();
        $stock_available->updateQuantity($data['id_product'], $data['id_product_attribute'], $data['quantity'], $data['id_shop']);
    }

    /**
     * Called from self::populateEntity
     *
     * @param string $identifier Tab id
     * @param array $data Attributes + children of tab element
     * @param array $data_lang Translated attributes
     *
     * @throws PrestashopInstallerException
     */
    public function createEntityTab($identifier, array $data, array $data_lang)
    {
        static $position = [];

        $entity = 'tab';
        $xml = $this->fileLoader->load($entity);

        if (!isset($position[$data['id_parent']])) {
            $position[$data['id_parent']] = 0;
        }
        $data['position'] = $position[$data['id_parent']]++;
        $data['icon'] = $data['icon'] ?? '';
        $data['wording'] = $data['wording'] ?? '';
        $data['wording_domain'] = $data['wording_domain'] ?? '';
        $data['route_name'] = $data['route_name'] ?? '';

        // Generate primary key manually
        if (!$xml->fields['primary']) {
            $primary = 'id_' . $entity;
        } elseif (!str_contains((string) $xml->fields['primary'], ',')) {
            $primary = (string) $xml->fields['primary'];
        } else {
            $primary = '';
        }

        if ($primary) {
            $entity_id = $this->generatePrimary($entity, $primary);
            $data[$primary] = $entity_id;
        } else {
            $entity_id = 0;
        }

        // Make sure data are correctly ordered because some attributes are optional
        // and Db::insert needs to have all data keys in the same order when using multiple insert
        ksort($data);

        // Store INSERT queries in order to optimize install with grouped inserts
        $this->delayed_inserts[$entity][] = array_map('pSQL', $data);
        if ($data_lang) {
            $real_data_lang = [];
            foreach ($data_lang as $field => $list) {
                foreach ($list as $id_lang => $value) {
                    $real_data_lang[$id_lang][$field] = $value;
                }
            }

            foreach ($real_data_lang as $id_lang => $insert_data_lang) {
                $insert_data_lang['id_' . $entity] = $entity_id;
                $insert_data_lang['id_lang'] = $id_lang;
                $this->delayed_inserts[$entity . '_lang'][] = array_map('pSQL', $insert_data_lang);
            }
        }

        $this->storeId($entity, $identifier, $entity_id);
    }

    public function generatePrimary($entity, $primary)
    {
        if (!isset($this->primaries[$entity])) {
            $entity = Db::getInstance()->escape($entity, false, true);
            $primary = Db::getInstance()->escape($primary, false, true);
            $this->primaries[$entity] = (int) Db::getInstance()->getValue(
                'SELECT ' . $primary . ' FROM `' . _DB_PREFIX_ . $entity . '` ORDER BY `' . $primary . '` DESC'
            );
        }

        return ++$this->primaries[$entity];
    }

    public function copyImages($entity, $identifier, $path, array $data, $extension = 'jpg')
    {
        // Get list of image types
        $reference = [
            'product' => 'products',
            'category' => 'categories',
            'manufacturer' => 'manufacturers',
            'supplier' => 'suppliers',
            'store' => 'stores',
        ];

        $types = [];
        if (isset($reference[$entity])) {
            $types = ImageType::getImagesTypes($reference[$entity]);
        }

        // For each path copy images
        $path = array_map('trim', explode(',', $path));
        foreach ($path as $p) {
            $from_path = $this->img_path . $p . '/';
            $dst_path = _PS_IMG_DIR_ . $p . '/';
            $entity_id = $this->retrieveId($entity, $identifier);

            if (!@copy($from_path . $identifier . '.' . $extension, $dst_path . $entity_id . '.' . $extension)) {
                $this->setError(
                    $this->translator->trans(
                        'Cannot create image "%identifier%" for entity "%entity%"',
                        ['%entity%' => $entity, '%identifier%' => $identifier],
                        'Install'
                    )
                );

                return;
            }

            foreach ($types as $type) {
                $origin_file = $from_path . $identifier . '-' . $type['name'] . '.' . $extension;
                $target_file = $dst_path . $entity_id . '-' . $type['name'] . '.' . $extension;

                // Test if dest folder is writable
                if (!is_writable(dirname($target_file))) {
                    $this->setError(
                        $this->translator->trans(
                            'Cannot create image "%identifier%" (bad permissions on folder "%folder%")',
                            ['%identifier%' => $identifier . '-' . $type['name'], '%folder%' => dirname($target_file)],
                            'Install'
                        )
                    );
                } elseif (file_exists($origin_file)) {
                    // If a file named folder/entity-type.extension exists just copy it
                    // this is an optimisation in order to prevent to much resize
                    if (!@copy($origin_file, $target_file)) {
                        $this->setError(
                            $this->translator->trans(
                                'Cannot create image "%identifier%"',
                                ['%identifier%' => $identifier . '-' . $type['name']],
                                'Install'
                            )
                        );
                    }
                    @chmod($target_file, FileSystem::DEFAULT_MODE_FILE);
                } elseif (!ImageManager::resize(
                    $from_path . $identifier . '.' . $extension,
                    $target_file,
                    $type['width'],
                    $type['height']
                )) {
                    // Resize the image if no cache was prepared in fixtures
                    $this->setError(
                        $this->translator->trans(
                            'Cannot create image "%identifier%" for entity "%entity%"',
                            ['%identifier%' => $identifier . '-' . $type['name'], '%entity%' => $entity],
                            'Install'
                        )
                    );
                }
            }
        }
        Image::moveToNewFileSystem();
    }

    public function copyImagesOrderState($identifier, array $data)
    {
        $this->copyImages('order_state', $identifier, 'os', $data, 'gif');
    }

    public function copyImagesTab($identifier, array $data)
    {
        $from_path = $this->img_path . 't/';
        $dst_path = _PS_IMG_DIR_ . 't/';
        if (file_exists($from_path . $data['class_name'] . '.gif') && !file_exists($dst_path . $data['class_name'] . '.gif')) {
            //test if file exist in install dir and if do not exist in dest folder.
            if (!@copy($from_path . $data['class_name'] . '.gif', $dst_path . $data['class_name'] . '.gif')) {
                $this->setError($this->translator->trans('Cannot create image "%identifier%" for entity "%entity%"', ['%identifier%' => $identifier, '%tab%' => 'tab'], 'Install'));

                return;
            }
        }
    }

    public function copyImagesImage($identifier)
    {
        $path = $this->img_path . 'p/';
        $image = new Image($this->retrieveId('image', $identifier));
        $dst_path = $image->getPathForCreation();
        if (!@copy($path . $identifier . '.jpg', $dst_path . '.' . $image->image_format)) {
            $this->setError(
                $this->translator->trans(
                    'Cannot create image "%identifier%" for entity "%entity%"',
                    ['%identifier%' => $identifier, '%entity%' => 'product'],
                    'Install'
                )
            );

            return;
        }
        @chmod($dst_path . '.' . $image->image_format, FileSystem::DEFAULT_MODE_FILE);

        $types = ImageType::getImagesTypes('products');
        foreach ($types as $type) {
            $origin_file = $path . $identifier . '-' . $type['name'] . '.jpg';
            $target_file = $dst_path . '-' . $type['name'] . '.' . $image->image_format;

            // Test if dest folder is writable
            if (!is_writable(dirname($target_file))) {
                $this->setError(
                    $this->translator->trans(
                        'Cannot create image "%identifier%" (bad permissions on folder "%folder%")',
                        ['%identifier%' => $identifier . '-' . $type['name'], '%folder%' => dirname($target_file)],
                        'Install'
                    )
                );
            } elseif (file_exists($origin_file)) {
                // If a file named folder/entity-type.jpg exists just copy it
                // this is an optimisation in order to prevent to much resize
                if (!@copy($origin_file, $target_file)) {
                    $this->setError(
                        $this->translator->trans(
                            'Cannot create image "%1$s" for entity "%2$s"',
                            ['%identifier%' => $identifier . '-' . $type['name'], '%entity%' => 'product'],
                            'Install'
                        )
                    );
                }
                @chmod($target_file, FileSystem::DEFAULT_MODE_FILE);
            } elseif (!ImageManager::resize($path . $identifier . '.jpg', $target_file, $type['width'], $type['height'])) {
                // Resize the image if no cache was prepared in fixtures
                $this->setError(
                    $this->translator->trans(
                        'Cannot create image "%identifier%" for entity "%entity%"',
                        ['%identifier%' => $identifier . '-' . $type['name'], '%entity%' => 'product'],
                        'Install'
                    )
                );
            }
        }
    }

    public function getTables()
    {
        static $tables = null;

        if (null === $tables) {
            $tables = [];
            foreach (Db::getInstance()->executeS('SHOW TABLES') as $row) {
                $table = current($row);
                if (preg_match('#^' . _DB_PREFIX_ . '(.+?)(_lang)?$#i', $table, $m)) {
                    $tables[$m[1]] = (isset($m[2]) && $m[2]) ? true : false;
                }
            }
        }

        return $tables;
    }

    public function hasElements($table)
    {
        $table = Db::getInstance()->escape($table, false, true);

        return (bool) Db::getInstance()->getValue('SELECT COUNT(*) FROM `' . _DB_PREFIX_ . $table . '`');
    }

    public function getColumns($table, $multilang = false, array $exclude = [])
    {
        static $columns = [];

        if ($multilang) {
            return ($this->isMultilang($table)) ? $this->getColumns($table . '_lang', false, ['id_' . $table]) : [];
        }

        if (!isset($columns[$table])) {
            $columns[$table] = [];
            $sql = 'SHOW COLUMNS FROM `' . _DB_PREFIX_ . bqSQL($table) . '`';
            foreach (Db::getInstance()->executeS($sql) as $row) {
                $columns[$table][$row['Field']] = $this->checkIfTypeIsText($row['Type']);
            }
        }

        $exclude = array_merge(['id_' . $table, 'date_add', 'date_upd', 'deleted', 'id_lang'], $exclude);

        $list = [];
        foreach ($columns[$table] as $k => $v) {
            if (!in_array($k, $exclude)) {
                $list[$k] = $v;
            }
        }

        return $list;
    }

    public function getClasses($path = null)
    {
        static $cache = null;

        if (null !== $cache) {
            return $cache;
        }

        $dir = $path;
        if (null === $dir) {
            $dir = _PS_CLASS_DIR_;
        }

        $classes = [];
        foreach (scandir($dir, SCANDIR_SORT_NONE) as $file) {
            if ($file[0] != '.' && $file != 'index.php') {
                if (is_dir($dir . $file)) {
                    $classes = array_merge($classes, $this->getClasses($dir . $file . '/'));
                } elseif (preg_match('#^(.+)\.php$#', $file, $m)) {
                    $classes[] = $m[1];
                }
            }
        }

        sort($classes);
        if (null === $path) {
            $cache = $classes;
        }

        return $classes;
    }

    public function checkIfTypeIsText($type)
    {
        if (preg_match('#^(longtext|text|tinytext)#i', $type)) {
            return true;
        }

        if (preg_match('#^varchar\(([0-9]+)\)$#i', $type, $m)) {
            return (int) ($m[1]) >= 64 ? true : false;
        }

        return false;
    }

    public function isMultilang($entity)
    {
        $tables = $this->getTables();

        return isset($tables[$entity]) && $tables[$entity];
    }

    public function entityExists($entity)
    {
        return file_exists($this->data_path . $entity . '.xml');
    }

    public function getEntitiesList()
    {
        $entities = [];
        foreach (scandir($this->data_path, SCANDIR_SORT_NONE) as $file) {
            if ($file[0] != '.' && preg_match('#^(.+)\.xml$#', $file, $m)) {
                $entities[] = $m[1];
            }
        }

        return $entities;
    }

    public function getEntityInfo($entity)
    {
        $info = [
            'config' => [
                'id' => '',
                'primary' => '',
                'class' => '',
                'sql' => '',
                'ordersql' => '',
                'image' => '',
                'null' => '',
            ],
            'fields' => [],
        ];

        if (!$this->entityExists($entity)) {
            return $info;
        }

        $xml = @simplexml_load_file($this->data_path . $entity . '.xml', 'SimplexmlElement');
        if (!$xml) {
            return $info;
        }

        if ($xml->fields['id']) {
            $info['config']['id'] = (string) $xml->fields['id'];
        }

        if ($xml->fields['primary']) {
            $info['config']['primary'] = (string) $xml->fields['primary'];
        }

        if ($xml->fields['class']) {
            $info['config']['class'] = (string) $xml->fields['class'];
        }

        if ($xml->fields['sql']) {
            $info['config']['sql'] = (string) $xml->fields['sql'];
        }

        if ($xml->fields['ordersql']) {
            $info['config']['ordersql'] = (string) $xml->fields['ordersql'];
        }

        if ($xml->fields['null']) {
            $info['config']['null'] = (string) $xml->fields['null'];
        }

        if ($xml->fields['image']) {
            $info['config']['image'] = (string) $xml->fields['image'];
        }

        foreach ($xml->fields->field as $field) {
            $column = (string) $field['name'];
            $info['fields'][$column] = [];
            if (isset($field['relation'])) {
                $info['fields'][$column]['relation'] = (string) $field['relation'];
            }
        }

        return $info;
    }

    public function getDependencies()
    {
        $entities = [];
        foreach ($this->getEntitiesList() as $entity) {
            $entities[$entity] = $this->getEntityInfo($entity);
        }

        $dependencies = [];
        foreach ($entities as $entity => $info) {
            foreach ($info['fields'] as $field => $info_field) {
                if (isset($info_field['relation']) && $info_field['relation'] != $entity) {
                    if (!isset($dependencies[$info_field['relation']])) {
                        $dependencies[$info_field['relation']] = [];
                    }
                    $dependencies[$info_field['relation']][] = $entity;
                }
            }
        }

        return $dependencies;
    }

    public function generateEntitySchema($entity, array $fields, array $config)
    {
        if ($this->entityExists($entity)) {
            $xml = $this->fileLoader->load($entity);
        } else {
            $xml = new SimplexmlElement('<entity_' . $entity . ' />');
        }
        unset($xml->fields);

        // Fill <fields> attributes (config)
        $xml_fields = $xml->addChild('fields');
        foreach ($config as $k => $v) {
            if ($v) {
                $xml_fields[$k] = $v;
            }
        }

        // Create list of fields
        foreach ($fields as $column => $info) {
            $field = $xml_fields->addChild('field');
            $field->name = $column;
            if (isset($info['relation'])) {
                $field->relation = $info['relation'];
            }
        }

        // Recreate entities nodes, in order to have the <entities> node after the <fields> node
        $store_entities = clone $xml->entities;
        unset($xml->entities);
        $xml->addChild('entities', $store_entities);

        $xml->asXML($this->data_path . $entity . '.xml');
    }

    /**
     * ONLY FOR DEVELOPMENT PURPOSE.
     */
    public function generateAllEntityFiles()
    {
        $entities = [];
        foreach ($this->getEntitiesList() as $entity) {
            $entities[$entity] = $this->getEntityInfo($entity);
        }
        $this->generateEntityFiles($entities);
    }

    /**
     * ONLY FOR DEVELOPMENT PURPOSE.
     */
    public function generateEntityFiles($entities)
    {
        $dependencies = $this->getDependencies();

        // Sort entities to populate database in good order (E.g. zones before countries)
        do {
            $current = (isset($sort_entities)) ? $sort_entities : [];
            $sort_entities = [];
            foreach ($entities as $entity) {
                if (isset($dependencies[$entity])) {
                    $min = count($entities) - 1;
                    foreach ($dependencies[$entity] as $item) {
                        if (($key = array_search($item, $sort_entities)) !== false) {
                            $min = min($min, $key);
                        }
                    }
                    if ($min == 0) {
                        array_unshift($sort_entities, $entity);
                    } else {
                        array_splice($sort_entities, $min, 0, [$entity]);
                    }
                } else {
                    $sort_entities[] = $entity;
                }
            }
            $entities = $sort_entities;
        } while ($current != $sort_entities);

        foreach ($sort_entities as $entity) {
            $this->generateEntityContent($entity);
        }
    }

    public function generateEntityContent($entity)
    {
        $xml = $this->fileLoader->load($entity);
        if (method_exists($this, 'getEntityContents' . Tools::toCamelCase($entity))) {
            $content = $this->{'getEntityContents' . Tools::toCamelCase($entity)}($entity);
        } else {
            $content = $this->getEntityContents($entity);
        }

        unset($xml->entities);
        $entities = $xml->addChild('entities');
        $this->createXmlEntityNodes($entity, $content['nodes'], $entities);
        $xml->asXML($this->data_path . $entity . '.xml');

        // Generate multilang XML files
        if ($content['nodes_lang']) {
            foreach ($content['nodes_lang'] as $id_lang => $nodes) {
                if (!isset($this->languages[$id_lang])) {
                    continue;
                }

                $iso = $this->languages[$id_lang];
                if (!is_dir($this->lang_path . $this->getFallBackToDefaultLanguage($iso) . '/data')) {
                    mkdir($this->lang_path . $this->getFallBackToDefaultLanguage($iso) . '/data');
                }

                $xml_node = new SimplexmlElement('<entity_' . $entity . ' />');
                $this->createXmlEntityNodes($entity, $nodes, $xml_node);
                $xml_node->asXML($this->lang_path . $this->getFallBackToDefaultEntityLanguage($iso, $entity) . '/data/' . $entity . '.xml');
            }
        }

        if ($xml->fields['image']) {
            if (method_exists($this, 'backupImage' . Tools::toCamelCase($entity))) {
                $this->{'backupImage' . Tools::toCamelCase($entity)}((string) $xml->fields['image']);
            } else {
                $this->backupImage($entity, (string) $xml->fields['image']);
            }
        }
    }

    /**
     * ONLY FOR DEVELOPMENT PURPOSE.
     */
    public function getEntityContents($entity)
    {
        $xml = $this->fileLoader->load($entity);
        $primary = !empty($xml->fields['primary']) ? (string) $xml->fields['primary'] : 'id_' . $entity;
        $is_multilang = $this->isMultilang($entity);

        // Check if current table is an association table (if multiple primary keys)
        $is_association = false;
        if (str_contains($primary, ',')) {
            $is_association = true;
            $primary = array_map('trim', explode(',', $primary));
        }

        // Build query
        $sql = new DbQuery();
        $sql->select('a.*');
        $sql->from($entity, 'a');
        if ($is_multilang) {
            $sql->select('b.*');
            $sql->leftJoin($entity . '_lang', 'b', 'a.' . $primary . ' = b.' . $primary);
        }

        if (!empty($xml->fields['sql'])) {
            $sql->where((string) $xml->fields['sql']);
        }

        if (!$is_association) {
            $sql->select('a.' . $primary);
            if (empty($xml->fields['ordersql'])) {
                $sql->orderBy('a.' . $primary);
            }
        }

        if ($is_multilang && empty($xml->fields['ordersql'])) {
            $sql->orderBy('b.id_lang');
        }

        if (!empty($xml->fields['ordersql'])) {
            $sql->orderBy((string) $xml->fields['ordersql']);
        }

        // Get multilang columns
        $alias_multilang = $multilang_columns = [];
        if ($is_multilang) {
            $columns = $this->getColumns($entity);
            $multilang_columns = $this->getColumns($entity, true);

            // If some columns from _lang table have same name than original table, rename them (E.g. value in configuration)
            foreach ($multilang_columns as $c => $is_text) {
                if (isset($columns[$c])) {
                    $alias = $c . '_alias';
                    $alias_multilang[$c] = $alias;
                    $sql->select('a.' . $c . ' as ' . $c . ', b.' . $c . ' as ' . $alias);
                }
            }
        }

        // Get all results
        $nodes = $nodes_lang = [];
        $results = Db::getInstance()->executeS($sql);
        if (Db::getInstance()->getNumberError()) {
            $this->setError($this->translator->trans('SQL error on query <i>%query%</i>', ['%sql%' => $sql], 'Install'));
        } else {
            foreach ($results as $row) {
                // Store common columns
                if ($is_association) {
                    $id = $entity;
                    foreach ($primary as $key) {
                        $id .= '_' . $row[$key];
                    }
                } else {
                    $id = $this->generateId($entity, $row[$primary], $row, (!empty($xml->fields['id'])) ? (string) $xml->fields['id'] : null);
                }

                if (!isset($nodes[$id])) {
                    $node = [];
                    foreach ($xml->fields->field as $field) {
                        $column = (string) $field['name'];
                        if (isset($field['relation'])) {
                            $sql = 'SELECT `id_' . bqSQL($field['relation']) . '`
									FROM `' . bqSQL(_DB_PREFIX_ . $field['relation']) . '`
									WHERE `id_' . bqSQL($field['relation']) . '` = ' . (int) $row[$column];
                            $node[$column] = $this->generateId((string) $field['relation'], Db::getInstance()->getValue($sql));

                            // A little trick to allow storage of some hard values, like '-1' for tab.id_parent
                            if (!$node[$column] && $row[$column]) {
                                $node[$column] = $row[$column];
                            }
                        } else {
                            $node[$column] = $row[$column];
                        }
                    }
                    $nodes[$id] = $node;
                }

                // Store multilang columns
                if ($is_multilang && $row['id_lang']) {
                    $node = [];
                    foreach ($multilang_columns as $column => $is_text) {
                        $node[$column] = $row[isset($alias_multilang[$column]) ? $alias_multilang[$column] : $column];
                    }
                    $nodes_lang[$row['id_lang']][$id] = $node;
                }
            }
        }

        return [
            'nodes' => $nodes,
            'nodes_lang' => $nodes_lang,
        ];
    }

    public function getEntityContentsTag()
    {
        $nodes_lang = [];

        $sql = 'SELECT t.id_tag, t.id_lang, t.name, pt.id_product
				FROM ' . _DB_PREFIX_ . 'tag t
				LEFT JOIN ' . _DB_PREFIX_ . 'product_tag pt ON t.id_tag = pt.id_tag
				ORDER BY id_lang';
        foreach (Db::getInstance()->executeS($sql) as $row) {
            $identifier = $this->generateId('tag', $row['id_tag']);
            if (!isset($nodes_lang[$row['id_lang']])) {
                $nodes_lang[$row['id_lang']] = [];
            }

            if (!isset($nodes_lang[$row['id_lang']][$identifier])) {
                $nodes_lang[$row['id_lang']][$identifier] = [
                    'name' => $row['name'],
                    'products' => '',
                ];
            }

            $nodes_lang[$row['id_lang']][$identifier]['products'] .= (($nodes_lang[$row['id_lang']][$identifier]['products']) ? ',' : '') . $this->generateId('product', $row['id_product']);
        }

        return [
            'nodes' => [],
            'nodes_lang' => $nodes_lang,
        ];
    }

    /**
     * ONLY FOR DEVELOPMENT PURPOSE.
     */
    public function generateId($entity, $primary, array $row = [], $id_format = null)
    {
        static $ids = [];

        if (isset($ids[$entity][$primary])) {
            return $ids[$entity][$primary];
        }

        if (!isset($ids[$entity])) {
            $ids[$entity] = [];
        }

        if (!$primary) {
            return '';
        }

        if (!$id_format || !$row || !$row[$id_format]) {
            $ids[$entity][$primary] = $entity . '_' . $primary;
        } else {
            $value = $row[$id_format];
            $value = preg_replace('#[^a-z0-9_-]#i', '_', $value);
            $value = preg_replace('#_+#', '_', $value);
            $value = preg_replace('#^_+#', '', $value);
            $value = preg_replace('#_+$#', '', $value);

            $store_identifier = $value;
            $i = 1;
            while (in_array($store_identifier, $ids[$entity])) {
                $store_identifier = $value . '_' . $i++;
            }
            $ids[$entity][$primary] = $store_identifier;
        }

        return $ids[$entity][$primary];
    }

    /**
     * ONLY FOR DEVELOPMENT PURPOSE.
     */
    public function createXmlEntityNodes($entity, array $nodes, SimpleXMLElement $entities)
    {
        $types = array_merge($this->getColumns($entity), $this->getColumns($entity, true));
        foreach ($nodes as $id => $node) {
            $entity_node = $entities->addChild($entity);
            $entity_node->id = $id;
            foreach ($node as $k => $v) {
                if (!empty($types[$k])) {
                    $entity_node->addChild($k, $v);
                } else {
                    $entity_node->{$k} = $v;
                }
            }
        }
    }

    /**
     * ONLY FOR DEVELOPMENT PURPOSE.
     */
    public function backupImage($entity, $path)
    {
        $reference = [
            'product' => 'products',
            'category' => 'categories',
            'manufacturer' => 'manufacturers',
            'supplier' => 'suppliers',
            'store' => 'stores',
        ];

        $types = [];
        if (isset($reference[$entity])) {
            $types = [];
            foreach (ImageType::getImagesTypes($reference[$entity]) as $type) {
                $types[] = $type['name'];
            }
        }

        $path_list = array_map('trim', explode(',', $path));
        foreach ($path_list as $p) {
            $backup_path = $this->img_path . $p . '/';
            $from_path = _PS_IMG_DIR_ . $p . '/';

            if (!is_dir($backup_path) && !mkdir($backup_path)) {
                $this->setError(sprintf('Cannot create directory <i>%s</i>', $backup_path));
            }

            foreach (scandir($from_path, SCANDIR_SORT_NONE) as $file) {
                if ($file[0] != '.' && preg_match('#^(([0-9]+)(-(' . implode('|', $types) . '))?)\.(gif|jpg|jpeg|png)$#i', $file, $m)) {
                    $file_id = $m[2];
                    $file_type = $m[3];
                    $file_extension = $m[5];
                    copy($from_path . $file, $backup_path . $this->generateId($entity, $file_id) . $file_type . '.' . $file_extension);
                }
            }
        }
    }

    /**
     * ONLY FOR DEVELOPMENT PURPOSE.
     */
    public function backupImageImage()
    {
        $types = [];
        foreach (ImageType::getImagesTypes('products') as $type) {
            $types[] = $type['name'];
        }

        $backup_path = $this->img_path . 'p/';
        $from_path = _PS_PRODUCT_IMG_DIR_;
        if (!is_dir($backup_path) && !mkdir($backup_path)) {
            $this->setError(sprintf('Cannot create directory <i>%s</i>', $backup_path));
        }

        foreach (Image::getAllImages() as $image) {
            $image = new Image($image['id_image']);
            $image_path = $image->getExistingImgPath();
            if (file_exists($from_path . $image_path . '.' . $image->image_format)) {
                copy($from_path . $image_path . '.' . $image->image_format, $backup_path . $this->generateId('image', $image->id) . '.' . $image->image_format);
            }

            foreach ($types as $type) {
                if (file_exists($from_path . $image_path . '-' . $type . '.' . $image->image_format)) {
                    copy($from_path . $image_path . '-' . $type . '.' . $image->image_format, $backup_path . $this->generateId('image', $image->id) . '-' . $type . '.' . $image->image_format);
                }
            }
        }
    }

    /**
     * ONLY FOR DEVELOPMENT PURPOSE.
     */
    public function backupImageTab()
    {
        $backup_path = $this->img_path . 't/';
        $from_path = _PS_IMG_DIR_ . 't/';
        if (!is_dir($backup_path) && !mkdir($backup_path)) {
            $this->setError(sprintf('Cannot create directory <i>%s</i>', $backup_path));
        }

        $xml = $this->fileLoader->load('tab');
        foreach ($xml->entities->tab as $tab) {
            if (file_exists($from_path . $tab->class_name . '.gif')) {
                copy($from_path . $tab->class_name . '.gif', $backup_path . $tab->class_name . '.gif');
            }
        }
    }
}
