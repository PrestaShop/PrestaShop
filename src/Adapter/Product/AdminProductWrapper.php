<?php
/**
 * 2007-2018 PrestaShop.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
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
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Adapter\Product;

use Attachment;
use PrestaShop\PrestaShop\Adapter\Entity\Customization;
use PrestaShop\PrestaShop\Core\Foundation\Database\EntityNotFoundException;
use SpecificPrice;
use Customer;
use Combination;
use Image;
use SpecificPriceRule;
use Product;
use ProductDownload;
use AdminProductsController;
use Symfony\Component\Translation\TranslatorInterface;
use Tools;
use StockAvailable;
use Hook;
use Validate;
use Db;
use Shop;
use Language;
use ObjectModel;
use Configuration;
use Context;
use ShopUrl;
use Category;

/**
 * Admin controller wrapper for new Architecture, about Product admin controller.
 */
class AdminProductWrapper
{
    /**
     * @var array
     */
    private $errors = array();

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var Context
     */
    private $legacyContext;

    /**
     * Constructor : Inject Symfony\Component\Translation Translator.
     *
     * @param object $translator
     */
    public function __construct($translator, $legacyContext)
    {
        $this->translator = $translator;
        $this->legacyContext = $legacyContext->getContext();
    }

    /**
     * getInstance
     * Get the legacy AdminProductsControllerCore instance.
     *
     * @return AdminProductsController instance
     */
    public function getInstance()
    {
        return new AdminProductsController();
    }

    /**
     * processProductAttribute
     * Update a combination.
     *
     * @param object $product
     * @param array $combinationValues the posted values
     *
     * @return AdminProductsController instance
     */
    public function processProductAttribute($product, $combinationValues)
    {
        $id_product_attribute = (int) $combinationValues['id_product_attribute'];
        $images = array();

        if (!Combination::isFeatureActive() || $id_product_attribute == 0) {
            return;
        }

        if (!isset($combinationValues['attribute_wholesale_price'])) {
            $combinationValues['attribute_wholesale_price'] = 0;
        }
        if (!isset($combinationValues['attribute_price_impact'])) {
            $combinationValues['attribute_price_impact'] = 0;
        }
        if (!isset($combinationValues['attribute_weight_impact'])) {
            $combinationValues['attribute_weight_impact'] = 0;
        }
        if (!isset($combinationValues['attribute_ecotax'])) {
            $combinationValues['attribute_ecotax'] = 0;
        }
        if ((isset($combinationValues['attribute_default']) && $combinationValues['attribute_default'] == 1)) {
            $product->deleteDefaultAttributes();
        }
        if (!empty($combinationValues['id_image_attr'])) {
            $images = $combinationValues['id_image_attr'];
        } else {
            $combination = new Combination($id_product_attribute);
            $combination->setImages(array());
        }
        if (!isset($combinationValues['attribute_low_stock_threshold'])) {
            $combinationValues['attribute_low_stock_threshold'] = null;
        }
        if (!isset($combinationValues['attribute_low_stock_alert'])) {
            $combinationValues['attribute_low_stock_alert'] = false;
        }

        $product->updateAttribute(
            $id_product_attribute,
            $combinationValues['attribute_wholesale_price'],
            $combinationValues['attribute_price'] * $combinationValues['attribute_price_impact'],
            $combinationValues['attribute_weight'] * $combinationValues['attribute_weight_impact'],
            $combinationValues['attribute_unity'] * $combinationValues['attribute_unit_impact'],
            $combinationValues['attribute_ecotax'],
            $images,
            $combinationValues['attribute_reference'],
            $combinationValues['attribute_ean13'],
            (isset($combinationValues['attribute_default']) && $combinationValues['attribute_default'] == 1),
            isset($combinationValues['attribute_location']) ? $combinationValues['attribute_location'] : null,
            $combinationValues['attribute_upc'],
            $combinationValues['attribute_minimal_quantity'],
            $combinationValues['available_date_attribute'],
            false,
            array(),
            $combinationValues['attribute_isbn'],
            $combinationValues['attribute_low_stock_threshold'],
            $combinationValues['attribute_low_stock_alert']
        );

        StockAvailable::setProductDependsOnStock((int) $product->id, $product->depends_on_stock, null, $id_product_attribute);
        StockAvailable::setProductOutOfStock((int) $product->id, $product->out_of_stock, null, $id_product_attribute);

        $product->checkDefaultAttributes();

        if ((isset($combinationValues['attribute_default']) && $combinationValues['attribute_default'] == 1)) {
            Product::updateDefaultAttribute((int) $product->id);
            if (isset($id_product_attribute)) {
                $product->cache_default_attribute = (int) $id_product_attribute;
            }

            // We need to reload the product because some other calls have modified the database
            // It's done just for the setAvailableDate to avoid side effects
            $consistentProduct = new Product($product->id);
            if ($available_date = $combinationValues['available_date_attribute']) {
                $consistentProduct->setAvailableDate($available_date);
            } else {
                $consistentProduct->setAvailableDate();
            }
        }

        if (isset($combinationValues['attribute_quantity'])) {
            $this->processQuantityUpdate($product, $combinationValues['attribute_quantity'], $id_product_attribute);
        }
    }

    /**
     * Update a quantity for a product or a combination.
     *
     * Does not work in Advanced stock management.
     *
     * @param Product $product
     * @param int $quantity
     * @param int $forAttributeId
     */
    public function processQuantityUpdate(Product $product, $quantity, $forAttributeId = 0)
    {
        // Hook triggered by legacy code below: actionUpdateQuantity('id_product', 'id_product_attribute', 'quantity')
        StockAvailable::setQuantity((int) $product->id, $forAttributeId, $quantity);
        Hook::exec('actionProductUpdate', array('id_product' => (int) $product->id, 'product' => $product));
    }

    /**
     * Update the out of stock strategy.
     *
     * @param Product $product
     * @param int $out_of_stock
     */
    public function processProductOutOfStock(Product $product, $out_of_stock)
    {
        StockAvailable::setProductOutOfStock((int) $product->id, (int) $out_of_stock);
    }

    /**
     * Set if a product depends on stock (ASM). For a product or a combination.
     *
     * Does work only in Advanced stock management.
     *
     * @param Product $product
     * @param bool $dependsOnStock
     * @param int $forAttributeId
     */
    public function processDependsOnStock(Product $product, $dependsOnStock, $forAttributeId = 0)
    {
        StockAvailable::setProductDependsOnStock((int) $product->id, $dependsOnStock, null, $forAttributeId);
    }

    /**
     * Add/Update a SpecificPrice object.
     *
     * @param int $id_product
     * @param array $specificPriceValues the posted values
     * @param int (optional) $id_specific_price if this is an update of an existing specific price, null else
     *
     * @return AdminProductsController instance
     */
    public function processProductSpecificPrice($id_product, $specificPriceValues, $idSpecificPrice = null)
    {
        // ---- data formatting ----
        $id_product_attribute = $specificPriceValues['sp_id_product_attribute'];
        $id_shop = $specificPriceValues['sp_id_shop'] ? $specificPriceValues['sp_id_shop'] : 0;
        $id_currency = $specificPriceValues['sp_id_currency'] ? $specificPriceValues['sp_id_currency'] : 0;
        $id_country = $specificPriceValues['sp_id_country'] ? $specificPriceValues['sp_id_country'] : 0;
        $id_group = $specificPriceValues['sp_id_group'] ? $specificPriceValues['sp_id_group'] : 0;
        $id_customer = !empty($specificPriceValues['sp_id_customer']['data']) ? $specificPriceValues['sp_id_customer']['data'][0] : 0;
        $price = isset($specificPriceValues['leave_bprice']) ? '-1' : $specificPriceValues['sp_price'];
        $from_quantity = $specificPriceValues['sp_from_quantity'];
        $reduction = (float) $specificPriceValues['sp_reduction'];
        $reduction_tax = $specificPriceValues['sp_reduction_tax'];
        $reduction_type = !$reduction ? 'amount' : $specificPriceValues['sp_reduction_type'];
        $reduction_type = $reduction_type == '-' ? 'amount' : $reduction_type;
        $from = $specificPriceValues['sp_from'];
        if (!$from) {
            $from = '0000-00-00 00:00:00';
        }
        $to = $specificPriceValues['sp_to'];
        if (!$to) {
            $to = '0000-00-00 00:00:00';
        }
        $isThisAnUpdate = (null !== $idSpecificPrice);

        // ---- validation ----
        if (($price == '-1') && ((float) $reduction == '0')) {
            $this->errors[] = $this->translator->trans('No reduction value has been submitted', array(), 'Admin.Catalog.Notification');
        } elseif ($to != '0000-00-00 00:00:00' && strtotime($to) < strtotime($from)) {
            $this->errors[] = $this->translator->trans('Invalid date range', array(), 'Admin.Catalog.Notification');
        } elseif ($reduction_type == 'percentage' && ((float) $reduction <= 0 || (float) $reduction > 100)) {
            $this->errors[] = $this->translator->trans('Submitted reduction value (0-100) is out-of-range', array(), 'Admin.Catalog.Notification');
        }
        $validationResult = $this->validateSpecificPrice(
            $id_product,
            $id_shop,
            $id_currency,
            $id_country,
            $id_group,
            $id_customer,
            $price,
            $from_quantity,
            $reduction,
            $reduction_type,
            $from,
            $to,
            $id_product_attribute,
            $isThisAnUpdate
        );

        if (false === $validationResult) {
            return $this->errors;
        }

        // ---- data modification ----
        if ($isThisAnUpdate) {
            $specificPrice = new SpecificPrice($idSpecificPrice);
        } else {
            $specificPrice = new SpecificPrice();
        }

        $specificPrice->id_product = (int)$id_product;
        $specificPrice->id_product_attribute = (int)$id_product_attribute;
        $specificPrice->id_shop = (int)$id_shop;
        $specificPrice->id_currency = (int)($id_currency);
        $specificPrice->id_country = (int)($id_country);
        $specificPrice->id_group = (int)($id_group);
        $specificPrice->id_customer = (int)$id_customer;
        $specificPrice->price = (float)($price);
        $specificPrice->from_quantity = (int)($from_quantity);
        $specificPrice->reduction = (float)($reduction_type == 'percentage' ? $reduction / 100 : $reduction);
        $specificPrice->reduction_tax = $reduction_tax;
        $specificPrice->reduction_type = $reduction_type;
        $specificPrice->from = $from;
        $specificPrice->to = $to;

        if ($isThisAnUpdate) {
            $dataSavingResult = $specificPrice->save();
        } else {
            $dataSavingResult = $specificPrice->add();
        }

        if (false === $dataSavingResult) {
            $this->errors[] = $this->translator->trans('An error occurred while updating the specific price.', array(), 'Admin.Catalog.Notification');
        }


        return $this->errors;
    }

    /**
     * Validate a specific price.
     */
    private function validateSpecificPrice(
        $id_product,
        $id_shop,
        $id_currency,
        $id_country,
        $id_group,
        $id_customer,
        $price,
        $from_quantity,
        $reduction,
        $reduction_type,
        $from,
        $to,
        $id_combination = 0,
        $isThisAnUpdate = false
    )
    {
        if (!Validate::isUnsignedId($id_shop) || !Validate::isUnsignedId($id_currency) || !Validate::isUnsignedId($id_country) || !Validate::isUnsignedId($id_group) || !Validate::isUnsignedId($id_customer)) {
            $this->errors[] = 'Wrong IDs';
        } elseif ((!isset($price) && !isset($reduction)) || (isset($price) && !Validate::isNegativePrice($price)) || (isset($reduction) && !Validate::isPrice($reduction))) {
            $this->errors[] = 'Invalid price/discount amount';
        } elseif (!Validate::isUnsignedInt($from_quantity)) {
            $this->errors[] = 'Invalid quantity';
        } elseif ($reduction && !Validate::isReductionType($reduction_type)) {
            $this->errors[] = 'Please select a discount type (amount or percentage).';
        } elseif ($from && $to && (!Validate::isDateFormat($from) || !Validate::isDateFormat($to))) {
            $this->errors[] = 'The from/to date is invalid.';
        } elseif (!$isThisAnUpdate && SpecificPrice::exists((int)$id_product, $id_combination, $id_shop, $id_group, $id_country, $id_currency, $id_customer, $from_quantity, $from, $to, false)) {
            $this->errors[] = 'A specific price already exists for these parameters.';
        } else {
            return true;
        }

        return false;
    }

    /**
     * Get specific prices list for a product.
     *
     * @param object $product
     * @param object $defaultCurrency
     * @param array $shops Available shops
     * @param array $currencies Available currencies
     * @param array $countries Available countries
     * @param array $groups Available users groups
     *
     * @return array
     */
    public function getSpecificPricesList($product, $defaultCurrency, $shops, $currencies, $countries, $groups)
    {
        $content = array();
        $specific_prices = SpecificPrice::getByProductId((int) $product->id);

        $tmp = array();
        foreach ($shops as $shop) {
            $tmp[$shop['id_shop']] = $shop;
        }
        $shops = $tmp;
        $tmp = array();
        foreach ($currencies as $currency) {
            $tmp[$currency['id_currency']] = $currency;
        }
        $currencies = $tmp;

        $tmp = array();
        foreach ($countries as $country) {
            $tmp[$country['id_country']] = $country;
        }
        $countries = $tmp;

        $tmp = array();
        foreach ($groups as $group) {
            $tmp[$group['id_group']] = $group;
        }
        $groups = $tmp;

        if (is_array($specific_prices) && count($specific_prices)) {
            foreach ($specific_prices as $specific_price) {
                $id_currency = $specific_price['id_currency'] ? $specific_price['id_currency'] : $defaultCurrency->id;
                if (!isset($currencies[$id_currency])) {
                    continue;
                }

                $current_specific_currency = $currencies[$id_currency];
                if ($specific_price['reduction_type'] == 'percentage') {
                    $impact = '- ' . ($specific_price['reduction'] * 100) . ' %';
                } elseif ($specific_price['reduction'] > 0) {
                    $impact = '- ' . Tools::displayPrice(Tools::ps_round($specific_price['reduction'], 2), $current_specific_currency) . ' ';
                    if ($specific_price['reduction_tax']) {
                        $impact .= '(' . $this->translator->trans('Tax incl.', array(), 'Admin.Global') . ')';
                    } else {
                        $impact .= '(' . $this->translator->trans('Tax excl.', array(), 'Admin.Global') . ')';
                    }
                } else {
                    $impact = '--';
                }

                if ($specific_price['from'] == '0000-00-00 00:00:00' && $specific_price['to'] == '0000-00-00 00:00:00') {
                    $period = $this->translator->trans('Unlimited', array(), 'Admin.Global');
                } else {
                    $period = $this->translator->trans('From', array(), 'Admin.Global') . ' ' . ($specific_price['from'] != '0000-00-00 00:00:00' ? $specific_price['from'] : '0000-00-00 00:00:00') . '<br />' . $this->translator->trans('to', array(), 'Admin.Global') . ' ' . ($specific_price['to'] != '0000-00-00 00:00:00' ? $specific_price['to'] : '0000-00-00 00:00:00');
                }
                if ($specific_price['id_product_attribute']) {
                    $combination = new Combination((int) $specific_price['id_product_attribute']);
                    $attributes = $combination->getAttributesName(1);
                    $attributes_name = '';
                    foreach ($attributes as $attribute) {
                        $attributes_name .= $attribute['name'] . ' - ';
                    }
                    $attributes_name = rtrim($attributes_name, ' - ');
                } else {
                    $attributes_name = $this->translator->trans('All combinations', array(), 'Admin.Catalog.Feature');
                }

                $rule = new SpecificPriceRule((int) $specific_price['id_specific_price_rule']);
                $rule_name = ($rule->id ? $rule->name : '--');

                if ($specific_price['id_customer']) {
                    $customer = new Customer((int) $specific_price['id_customer']);
                    if (Validate::isLoadedObject($customer)) {
                        $customer_full_name = $customer->firstname . ' ' . $customer->lastname;
                    }
                    unset($customer);
                }

                if (!$specific_price['id_shop'] || in_array($specific_price['id_shop'], Shop::getContextListShopID())) {
                    $can_delete_specific_prices = true;
                    if (Shop::isFeatureActive()) {
                        $can_delete_specific_prices = (count($this->legacyContext->employee->getAssociatedShops()) > 1 && !$specific_price['id_shop']) || $specific_price['id_shop'];
                    }

                    $price = Tools::ps_round($specific_price['price'], 2);
                    $fixed_price = ($price == Tools::ps_round($product->price, 2) || $specific_price['price'] == -1) ? '--' : Tools::displayPrice($price, $current_specific_currency);

                    $content[] = [
                        'id_specific_price' => $specific_price['id_specific_price'],
                        'id_product' => $product->id,
                        'rule_name' => $rule_name,
                        'attributes_name' => $attributes_name,
                        'shop' => ($specific_price['id_shop'] ? $shops[$specific_price['id_shop']]['name'] : $this->translator->trans('All shops', array(), 'Admin.Global')),
                        'currency' => ($specific_price['id_currency'] ? $currencies[$specific_price['id_currency']]['name'] : $this->translator->trans('All currencies', array(), 'Admin.Global')),
                        'country' => ($specific_price['id_country'] ? $countries[$specific_price['id_country']]['name'] : $this->translator->trans('All countries', array(), 'Admin.Global')),
                        'group' => ($specific_price['id_group'] ? $groups[$specific_price['id_group']]['name'] : $this->translator->trans('All groups', array(), 'Admin.Global')),
                        'customer' => (isset($customer_full_name) ? $customer_full_name : $this->translator->trans('All customers', array(), 'Admin.Global')),
                        'fixed_price' => $fixed_price,
                        'impact' => $impact,
                        'period' => $period,
                        'from_quantity' => $specific_price['from_quantity'],
                        'can_delete' => (!$rule->id && $can_delete_specific_prices) ? true : false,
                        'can_edit' => (!$rule->id && $can_delete_specific_prices) ? true : false,
                    ];

                    unset($customer_full_name);
                }
            }
        }

        return $content;
    }

    /**
     * @param int $id
     *
     * @return SpecificPrice
     *
     * @throws PrestaShopObjectNotFoundException
     */
    public function getSpecificPriceDataById($id)
    {
        $price = new SpecificPrice($id);
        if (null === $price->id) {
            throw new EntityNotFoundException(sprintf('Cannot find specific price with id %d', $id));
        }
        return $price;
    }

    /**
     * Delete a specific price.
     *
     * @param int $id_specific_price
     *
     * @return array error & status
     */
    public function deleteSpecificPrice($id_specific_price)
    {
        if (!$id_specific_price || !Validate::isUnsignedId($id_specific_price)) {
            $error = $this->translator->trans('The specific price ID is invalid.', array(), 'Admin.Catalog.Notification');
        } else {
            $specificPrice = new SpecificPrice((int) $id_specific_price);
            if (!$specificPrice->delete()) {
                $error = $this->translator->trans('An error occurred while attempting to delete the specific price.', array(), 'Admin.Catalog.Notification');
            }
        }

        if (isset($error)) {
            return array(
                'status' => 'error',
                'message' => $error,
            );
        }

        return array(
            'status' => 'ok',
            'message' => $this->translator->trans('Successful deletion', array(), 'Admin.Notifications.Success'),
        );
    }

    /**
     * Get price priority.
     *
     * @param null|int $idProduct
     *
     * @return array
     */
    public function getPricePriority($idProduct = null)
    {
        if (!$idProduct) {
            return [
                0 => 'id_shop',
                1 => 'id_currency',
                2 => 'id_country',
                3 => 'id_group',
            ];
        }

        $specific_price_priorities = SpecificPrice::getPriority((int) $idProduct);

        // Not use id_customer
        if ($specific_price_priorities[0] == 'id_customer') {
            unset($specific_price_priorities[0]);
        }

        return array_values($specific_price_priorities);
    }

    /**
     * Process customization collection.
     *
     * @param object $product
     * @param array $data
     *
     * @return bool
     */
    public function processProductCustomization($product, $data)
    {
        $customization_ids = array();
        if ($data) {
            foreach ($data as $customization) {
                $customization_ids[] = (int) $customization['id_customization_field'];
            }
        }

        $shopList = Shop::getContextListShopID();

        /* Update the customization fields to be deleted in the next step if not used */
        $product->softDeleteCustomizationFields($customization_ids);

        $usedCustomizationIds = $product->getUsedCustomizationFieldsIds();
        $usedCustomizationIds = array_column($usedCustomizationIds, 'index');
        $usedCustomizationIds = array_map('intval', $usedCustomizationIds);
        $usedCustomizationIds = array_unique(array_merge($usedCustomizationIds, $customization_ids), SORT_REGULAR);

        //remove customization field langs for current context shops
        $productCustomization = $product->getCustomizationFieldIds();
        $toDeleteCustomizationIds = array();
        foreach ($productCustomization as $customizationFiled) {
            if (!in_array((int) $customizationFiled['id_customization_field'], $usedCustomizationIds)) {
                $toDeleteCustomizationIds[] = (int) $customizationFiled['id_customization_field'];
            }
            //if the customization_field is still in use, only delete the current context shops langs,
            if (in_array((int) $customizationFiled['id_customization_field'], $customization_ids)) {
                Customization::deleteCustomizationFieldLangByShop($customizationFiled['id_customization_field'], $shopList);
            }
        }

        //remove unused customization for the product
        $product->deleteUnusedCustomizationFields($toDeleteCustomizationIds);

        //create new customizations
        $countFieldText = 0;
        $countFieldFile = 0;
        $productCustomizableValue = 0;
        $hasRequiredField = false;

        $new_customization_fields_ids = array();

        if ($data) {
            foreach ($data as $key => $customization) {
                if ($customization['require']) {
                    $hasRequiredField = true;
                }

                //create label
                if (isset($customization['id_customization_field'])) {
                    $id_customization_field = (int) $customization['id_customization_field'];
                    Db::getInstance()->execute('UPDATE `' . _DB_PREFIX_ . 'customization_field`
					SET `required` = ' . ($customization['require'] ? 1 : 0) . ', `type` = ' . (int) $customization['type'] . '
					WHERE `id_customization_field` = ' . $id_customization_field);
                } else {
                    Db::getInstance()->execute(
                        'INSERT INTO `' . _DB_PREFIX_ . 'customization_field` (`id_product`, `type`, `required`)
                    	VALUES ('
                            . (int) $product->id . ', '
                            . (int) $customization['type'] . ', '
                            . ($customization['require'] ? 1 : 0)
                        . ')'
                    );
                    $id_customization_field = (int) Db::getInstance()->Insert_ID();
                }

                $new_customization_fields_ids[$key] = $id_customization_field;

                // Create multilingual label name
                $langValues = '';
                foreach (Language::getLanguages() as $language) {
                    $name = $customization['label'][$language['id_lang']];
                    foreach ($shopList as $id_shop) {
                        $langValues .= '('
                            . (int) $id_customization_field . ', '
                            . (int) $language['id_lang'] . ', '
                            . (int) $id_shop . ',\''
                            . pSQL($name)
                            . '\'), ';
                    }
                }
                Db::getInstance()->execute(
                    'INSERT INTO `' . _DB_PREFIX_ . 'customization_field_lang` (`id_customization_field`, `id_lang`, `id_shop`, `name`) VALUES '
                    . rtrim(
                        $langValues,
                        ', '
                    )
                );

                if ($customization['type'] == 0) {
                    ++$countFieldFile;
                } else {
                    ++$countFieldText;
                }
            }

            $productCustomizableValue = $hasRequiredField ? 2 : 1;
        }

        //update product count fields labels
        Db::getInstance()->execute('UPDATE `' . _DB_PREFIX_ . 'product` SET `customizable` = ' . $productCustomizableValue . ', `uploadable_files` = ' . (int) $countFieldFile . ', `text_fields` = ' . (int) $countFieldText . ' WHERE `id_product` = ' . (int) $product->id);

        //update product_shop count fields labels
        ObjectModel::updateMultishopTable('product', array(
            'customizable' => $productCustomizableValue,
            'uploadable_files' => (int) $countFieldFile,
            'text_fields' => (int) $countFieldText,
        ), 'a.id_product = ' . (int) $product->id);

        Configuration::updateGlobalValue('PS_CUSTOMIZATION_FEATURE_ACTIVE', '1');

        return $new_customization_fields_ids;
    }

    /**
     * Update product download.
     *
     * @param object $product
     * @param array $data
     *
     * @return bool
     */
    public function updateDownloadProduct($product, $data)
    {
        $id_product_download = ProductDownload::getIdFromIdProduct((int) $product->id, false);
        $download = new ProductDownload($id_product_download ? $id_product_download : null);

        if ((int) $data['is_virtual_file'] == 1) {
            $fileName = null;
            $file = $data['file'];

            if (!empty($file)) {
                $fileName = ProductDownload::getNewFilename();
                $file->move(_PS_DOWNLOAD_DIR_, $fileName);
            }

            $product->setDefaultAttribute(0); //reset cache_default_attribute

            $download->id_product = (int) $product->id;
            $download->display_filename = $data['name'];
            $download->filename = $fileName ? $fileName : $download->filename;
            $download->date_add = date('Y-m-d H:i:s');
            $download->date_expiration = $data['expiration_date'] ? $data['expiration_date'] . ' 23:59:59' : '';
            $download->nb_days_accessible = (int) $data['nb_days'];
            $download->nb_downloadable = (int) $data['nb_downloadable'];
            $download->active = 1;
            $download->is_shareable = 0;

            if (!$id_product_download) {
                $download->save();
            } else {
                $download->update();
            }
        } else {
            if (!empty($id_product_download)) {
                $download->date_expiration = date('Y-m-d H:i:s', time() - 1);
                $download->active = 0;
                $download->update();
            }
        }

        return $download;
    }

    /**
     * Delete file from a virtual product.
     *
     * @param object $product
     */
    public function processDeleteVirtualProductFile($product)
    {
        $id_product_download = ProductDownload::getIdFromIdProduct((int) $product->id, false);
        $download = new ProductDownload($id_product_download ? $id_product_download : null);

        if ($download && !empty($download->filename)) {
            unlink(_PS_DOWNLOAD_DIR_ . $download->filename);
            Db::getInstance()->execute('UPDATE `' . _DB_PREFIX_ . 'product_download` SET filename = "" WHERE `id_product_download` = ' . (int) $download->id);
        }
    }

    /**
     * Delete a virtual product.
     *
     * @param object $product
     */
    public function processDeleteVirtualProduct($product)
    {
        $id_product_download = ProductDownload::getIdFromIdProduct((int) $product->id, false);
        $download = new ProductDownload($id_product_download ? $id_product_download : null);

        if ($download) {
            $download->delete(true);
        }
    }

    /**
     * Add attachement file.
     *
     * @param object $product
     * @param array $data
     * @param array $locales
     *
     * @return object|null Attachement
     */
    public function processAddAttachment($product, $data, $locales)
    {
        $attachment = null;
        $file = $data['file'];
        if (!empty($file)) {
            $fileName = sha1(microtime());
            $attachment = new Attachment();

            foreach ($locales as $locale) {
                $attachment->name[(int) $locale['id_lang']] = $data['name'];
                $attachment->description[(int) $locale['id_lang']] = $data['description'];
            }

            $attachment->file = $fileName;
            $attachment->mime = $file->getMimeType();
            $attachment->file_name = $file->getClientOriginalName();

            $file->move(_PS_DOWNLOAD_DIR_, $fileName);

            if ($attachment->add()) {
                $attachment->attachProduct($product->id);
            }
        }

        return $attachment;
    }

    /**
     * Process product attachments.
     *
     * @param object $product
     * @param array $data
     */
    public function processAttachments($product, $data)
    {
        Attachment::attachToProduct($product->id, $data);
    }

    /**
     * Update images positions.
     *
     * @param array $data Indexed array with id product/position
     */
    public function ajaxProcessUpdateImagePosition($data)
    {
        foreach ($data as $id => $position) {
            $img = new Image((int) $id);
            $img->position = (int) $position;
            $img->update();
        }
    }

    /**
     * Update image legend and cover.
     *
     * @param int $idImage
     * @param array $data
     *
     * @return object image
     */
    public function ajaxProcessUpdateImage($idImage, $data)
    {
        $img = new Image((int) $idImage);
        if ($data['cover']) {
            Image::deleteCover((int) $img->id_product);
            $img->cover = 1;
        }
        $img->legend = $data['legend'];
        $img->update();

        return $img;
    }

    /**
     * Generate preview URL.
     *
     * @param object $product
     * @param bool $preview
     *
     * @return string preview url
     */
    public function getPreviewUrl($product, $preview = true)
    {
        $context = Context::getContext();
        $id_lang = Configuration::get('PS_LANG_DEFAULT', null, null, $context->shop->id);

        if (!ShopUrl::getMainShopDomain()) {
            return false;
        }

        $is_rewrite_active = (bool) Configuration::get('PS_REWRITING_SETTINGS');
        $preview_url = $context->link->getProductLink(
            $product,
            $product->link_rewrite[$context->language->id],
            Category::getLinkRewrite($product->id_category_default, $context->language->id),
            null,
            $id_lang,
            (int) $context->shop->id,
            0,
            $is_rewrite_active
        );

        if (!$product->active && $preview) {
            $preview_url = $this->getPreviewUrlDeactivate($preview_url);
        }

        return $preview_url;
    }

    /**
     * Generate preview URL deactivate.
     *
     * @param string $preview_url
     *
     * @return string preview url deactivate
     */
    public function getPreviewUrlDeactivate($preview_url)
    {
        $context = Context::getContext();
        $token = Tools::getAdminTokenLite('AdminProducts');

        $admin_dir = dirname($_SERVER['PHP_SELF']);
        $admin_dir = substr($admin_dir, strrpos($admin_dir, '/') + 1);
        $preview_url_deactivate = $preview_url . ((strpos($preview_url, '?') === false) ? '?' : '&') . 'adtoken=' . $token . '&ad=' . $admin_dir . '&id_employee=' . (int) $context->employee->id . '&preview=1';

        return $preview_url_deactivate;
    }

    /**
     * Generate preview URL.
     *
     * @param int $productId
     *
     * @return string preview url
     */
    public function getPreviewUrlFromId($productId)
    {
        $product = new Product($productId, false);

        return $this->getPreviewUrl($product);
    }
}
