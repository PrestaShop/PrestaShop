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
 *  International Registered Trademark & Property of PrestaShop SA
 */
namespace PrestaShop\PrestaShop\Adapter\Product;

/**
 * Admin controller wrapper for new Architecture, about Product admin controller.
 */
class AdminProductWrapper
{
    private $errors = [];
    private $translator;
    private $legacyContext;

    /**
     * Constructor : Inject Symfony\Component\Translation Translator
     *
     * @param object $translator
     */
    public function __construct($translator, $legacyContext)
    {
        $this->transDomain = 'AdminProducts';
        $this->translator = $translator;
        $this->legacyContext = $legacyContext->getContext();
    }

    /**
     * getInstance
     * Get the legacy AdminProductsControllerCore instance
     *
     * @return \AdminProductsControllerCore instance
     */
    public function getInstance()
    {
        return new \AdminProductsControllerCore();
    }

    /**
     * processProductAttribute
     * Update a combination
     *
     * @param object $product
     * @param array $combinationValues the posted values
     *
     * @return \AdminProductsControllerCore instance
     */
    public function processProductAttribute($product, $combinationValues)
    {
        $id_product_attribute = (int)$combinationValues['id_product_attribute'];

        if (!\Combination::isFeatureActive() || $id_product_attribute == 0) {
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
        if ($combinationValues['attribute_default']) {
            $product->deleteDefaultAttributes();
        }

        $product->updateAttribute(
            $id_product_attribute,
            $combinationValues['attribute_wholesale_price'],
            $combinationValues['attribute_price'] * $combinationValues['attribute_price_impact'],
            $combinationValues['attribute_weight'] * $combinationValues['attribute_weight_impact'],
            $combinationValues['attribute_unity'] * $combinationValues['attribute_unit_impact'],
            $combinationValues['attribute_ecotax'],
            null,
            $combinationValues['attribute_reference'],
            $combinationValues['attribute_ean13'],
            $combinationValues['attribute_default'],
            isset($combinationValues['attribute_location']) ? $combinationValues['attribute_location'] : null,
            $combinationValues['attribute_upc'],
            $combinationValues['attribute_minimal_quantity'],
            $combinationValues['available_date_attribute'],
            false,
            array(),
            $combinationValues['attribute_isbn']
        );

        \StockAvailable::setProductDependsOnStock((int)$product->id, $product->depends_on_stock, null, $id_product_attribute);
        \StockAvailable::setProductOutOfStock((int)$product->id, $product->out_of_stock, null, $id_product_attribute);

        $product->checkDefaultAttributes();

        if ($combinationValues['attribute_default']) {
            \Product::updateDefaultAttribute((int)$product->id);
            if (isset($id_product_attribute)) {
                $product->cache_default_attribute = (int)$id_product_attribute;
            }

            if ($available_date = $combinationValues['available_date_attribute']) {
                $product->setAvailableDate($available_date);
            } else {
                $product->setAvailableDate();
            }
        }

        $this->processQuantityUpdate($product, $combinationValues['attribute_quantity'], $id_product_attribute);
    }

    /**
     * Update a quantity for a product or a combination.
     *
     * Does not work in Advanced stock management.
     *
     * @param \Product $product
     * @param integer $quantity
     * @param integer $forAttributeId
     */
    public function processQuantityUpdate(\Product $product, $quantity, $forAttributeId = 0)
    {
        // Hook triggered by legacy code below: actionUpdateQuantity('id_product', 'id_product_attribute', 'quantity')
        \StockAvailable::setQuantity((int)$product->id, $forAttributeId, $quantity);
        \Hook::exec('actionProductUpdate', array('id_product' => (int)$product->id, 'product' => $product));
    }

    /**
     * Update the out of stock strategy
     *
     * @param \Product $product
     * @param integer $out_of_stock
     */
    public function processProductOutOfStock(\Product $product, $out_of_stock)
    {
        \StockAvailable::setProductOutOfStock((int)$product->id, (int)$out_of_stock);
    }

    /**
     * Set if a product depends on stock (ASM). For a product or a combination.
     *
     * Does work only in Advanced stock management.
     *
     * @param \Product $product
     * @param boolean $dependsOnStock
     * @param integer $forAttributeId
     */
    public function processDependsOnStock(\Product $product, $dependsOnStock, $forAttributeId = 0)
    {
        \StockAvailable::setProductDependsOnStock((int)$product->id, $dependsOnStock, null, $forAttributeId);
    }

    /**
     * processProductSpecificPrice
     * Add/Update specific price
     *
     * @param int $id_product
     * @param array $specificPriceValues the posted values
     *
     * @return \AdminProductsControllerCore instance
     */
    public function processProductSpecificPrice($id_product, $specificPriceValues)
    {
        $id_product_attribute = $specificPriceValues['sp_id_product_attribute'];
        $id_shop = $specificPriceValues['sp_id_shop'] ? $specificPriceValues['sp_id_shop'] : 0;
        $id_currency = $specificPriceValues['sp_id_currency'] ? $specificPriceValues['sp_id_currency'] : 0;
        $id_country = $specificPriceValues['sp_id_country'] ? $specificPriceValues['sp_id_country'] : 0;
        $id_group = $specificPriceValues['sp_id_group'] ? $specificPriceValues['sp_id_group'] : 0;
        $id_customer = !empty($specificPriceValues['sp_id_customer']) ? $specificPriceValues['sp_id_customer'] : 0;
        $price = $specificPriceValues['leave_bprice'] ? '-1' : $specificPriceValues['sp_price'];
        $from_quantity = $specificPriceValues['sp_from_quantity'];
        $reduction = (float)$specificPriceValues['sp_reduction'];
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

        if (($price == '-1') && ((float)$reduction == '0')) {
            $this->errors[] = 'No reduction value has been submitted';
        } elseif ($to != '0000-00-00 00:00:00' && strtotime($to) < strtotime($from)) {
            $this->errors[] = 'Invalid date range';
        } elseif ($reduction_type == 'percentage' && ((float)$reduction <= 0 || (float)$reduction > 100)) {
            $this->errors[] = 'Submitted reduction value (0-100) is out-of-range';
        } elseif ($this->validateSpecificPrice($id_product, $id_shop, $id_currency, $id_country, $id_group, $id_customer, $price, $from_quantity, $reduction, $reduction_type, $from, $to, $id_product_attribute)) {
            $specificPrice = new \SpecificPrice();
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

            if (!$specificPrice->add()) {
                $this->errors[] ='An error occurred while updating the specific price.';
            }
        }

        return $this->errors;
    }

    /**
     * Validate a specific price
     */
    private function validateSpecificPrice($id_product, $id_shop, $id_currency, $id_country, $id_group, $id_customer, $price, $from_quantity, $reduction, $reduction_type, $from, $to, $id_combination = 0)
    {
        if (!\Validate::isUnsignedId($id_shop) || !\Validate::isUnsignedId($id_currency) || !\Validate::isUnsignedId($id_country) || !\Validate::isUnsignedId($id_group) || !\Validate::isUnsignedId($id_customer)) {
            $this->errors[] = 'Wrong IDs';
        } elseif ((!isset($price) && !isset($reduction)) || (isset($price) && !\Validate::isNegativePrice($price)) || (isset($reduction) && !\Validate::isPrice($reduction))) {
            $this->errors[] = 'Invalid price/discount amount';
        } elseif (!\Validate::isUnsignedInt($from_quantity)) {
            $this->errors[] = 'Invalid quantity';
        } elseif ($reduction && !\Validate::isReductionType($reduction_type)) {
            $this->errors[] = 'Please select a discount type (amount or percentage).';
        } elseif ($from && $to && (!\Validate::isDateFormat($from) || !\Validate::isDateFormat($to))) {
            $this->errors[] = 'The from/to date is invalid.';
        } elseif (\SpecificPrice::exists((int)$id_product, $id_combination, $id_shop, $id_group, $id_country, $id_currency, $id_customer, $from_quantity, $from, $to, false)) {
            $this->errors[] = 'A specific price already exists for these parameters.';
        } else {
            return true;
        }
        return false;
    }

    /**
     * Get specific prices list for a product
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
        $content = [];
        $specific_prices = \SpecificPrice::getByProductId((int)$product->id);

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
                    $impact = '- ' . \Tools::displayPrice(\Tools::ps_round($specific_price['reduction'], 2), $current_specific_currency) . ' ';
                    if ($specific_price['reduction_tax']) {
                        $impact .= '(' . $this->translator->trans('Tax incl.', [], $this->transDomain) . ')';
                    } else {
                        $impact .= '(' . $this->translator->trans('Tax excl.', [], $this->transDomain) . ')';
                    }
                } else {
                    $impact = '--';
                }

                if ($specific_price['from'] == '0000-00-00 00:00:00' && $specific_price['to'] == '0000-00-00 00:00:00') {
                    $period = $this->translator->trans('Unlimited', [], $this->transDomain);
                } else {
                    $period = $this->translator->trans('From', [], $this->transDomain) . ' ' . ($specific_price['from'] != '0000-00-00 00:00:00' ? $specific_price['from'] : '0000-00-00 00:00:00') . '<br />' . $this->translator->trans('To', [], $this->transDomain) . ' ' . ($specific_price['to'] != '0000-00-00 00:00:00' ? $specific_price['to'] : '0000-00-00 00:00:00');
                }
                if ($specific_price['id_product_attribute']) {
                    $combination = new \Combination((int)$specific_price['id_product_attribute']);
                    $attributes = $combination->getAttributesName(1);
                    $attributes_name = '';
                    foreach ($attributes as $attribute) {
                        $attributes_name .= $attribute['name'] . ' - ';
                    }
                    $attributes_name = rtrim($attributes_name, ' - ');
                } else {
                    $attributes_name = $this->translator->trans('All combinations', [], $this->transDomain);
                }

                $rule = new \SpecificPriceRule((int)$specific_price['id_specific_price_rule']);
                $rule_name = ($rule->id ? $rule->name : '--');

                if ($specific_price['id_customer']) {
                    $customer = new \Customer((int)$specific_price['id_customer']);
                    if (\Validate::isLoadedObject($customer)) {
                        $customer_full_name = $customer->firstname . ' ' . $customer->lastname;
                    }
                    unset($customer);
                }

                if (!$specific_price['id_shop'] || in_array($specific_price['id_shop'], \Shop::getContextListShopID())) {
                    $can_delete_specific_prices = true;
                    if (\Shop::isFeatureActive()) {
                        $can_delete_specific_prices = (count($this->legacyContext->employee->getAssociatedShops()) > 1 && !$specific_price['id_shop']) || $specific_price['id_shop'];
                    }

                    $price = \Tools::ps_round($specific_price['price'], 2);
                    $fixed_price = ($price == \Tools::ps_round($product->price, 2) || $specific_price['price'] == -1) ? '--' : \Tools::displayPrice($price, $current_specific_currency);

                    $content[] = [
                        'id_specific_price' => $specific_price['id_specific_price'],
                        'id_product' => $product->id,
                        'rule_name' => $rule_name,
                        'attributes_name' => $attributes_name,
                        'shop' => ($specific_price['id_shop'] ? $shops[$specific_price['id_shop']]['name'] : $this->translator->trans('All shops', [], $this->transDomain)),
                        'currency' => ($specific_price['id_currency'] ? $currencies[$specific_price['id_currency']]['name'] : $this->translator->trans('All currencies', [], $this->transDomain)),
                        'country' => ($specific_price['id_country'] ? $countries[$specific_price['id_country']]['name'] : $this->translator->trans('All countries', [], $this->transDomain)),
                        'group' => ($specific_price['id_group'] ? $groups[$specific_price['id_group']]['name'] : $this->translator->trans('All groups', [], $this->transDomain)),
                        'customer' => (isset($customer_full_name) ? $customer_full_name : $this->translator->trans('All customers', [], $this->transDomain)),
                        'fixed_price' => $fixed_price,
                        'impact' => $impact,
                        'period' => $period,
                        'from_quantity' => $specific_price['from_quantity'],
                        'can_delete' => (!$rule->id && $can_delete_specific_prices) ? true : false
                    ];

                    unset($customer_full_name);
                }
            }
        }

        return $content;
    }

    /**
     * Delete a specific price
     *
     * @param int $id_specific_price
     *
     * @return array error & status
     */
    public function deleteSpecificPrice($id_specific_price)
    {
        if (!$id_specific_price || !\Validate::isUnsignedId($id_specific_price)) {
            $error = 'The specific price ID is invalid.';
        } else {
            $specificPrice = new \SpecificPrice((int)$id_specific_price);
            if (!$specificPrice->delete()) {
                $error = 'An error occurred while attempting to delete the specific price.';
            }
        }

        if (isset($error)) {
            return array(
                'status' => 'error',
                'message'=> $error
            );
        }

        return array(
            'status' => 'ok',
            'message'=> 'Successful deletion'
        );
    }

    /**
     * Get price priority
     *
     * @param null|int $idProduct
     *
     * @return array
     */
    public function getPricePriority($idProduct = null)
    {
        if (!$idProduct) {
            return [
                0 => "id_shop",
                1 => "id_currency",
                2 => "id_country",
                3 => "id_group"
            ];
        }

        $specific_price_priorities = \SpecificPrice::getPriority((int)$idProduct);

        // Not use id_customer
        if ($specific_price_priorities[0] == 'id_customer') {
            unset($specific_price_priorities[0]);
        }

        return array_values($specific_price_priorities);
    }
}
