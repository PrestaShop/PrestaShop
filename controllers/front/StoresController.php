<?php
/**
 * 2007-2019 PrestaShop and Contributors
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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */
class StoresControllerCore extends FrontController
{
    public $php_self = 'stores';

    /**
     * Initialize stores controller.
     *
     * @see FrontController::init()
     */
    public function init()
    {
        parent::init();

        // StarterTheme: Remove check when google maps v3 is done
        if (!extension_loaded('Dom')) {
            $this->errors[] = $this->trans('PHP "Dom" extension has not been loaded.', array(), 'Shop.Notifications.Error');
            $this->context->smarty->assign('errors', $this->errors);
        }
    }

    /**
     * Get formatted string address.
     *
     * @param array $store
     *
     * @return string
     */
    protected function processStoreAddress($store)
    {
        // StarterTheme: Remove method when google maps v3 is done
        $ignore_field = array(
            'firstname',
            'lastname',
        );

        $out_datas = array();

        $address_datas = AddressFormat::getOrderedAddressFields($store['id_country'], false, true);
        $state = (isset($store['id_state'])) ? new State($store['id_state']) : null;

        foreach ($address_datas as $data_line) {
            $data_fields = explode(' ', $data_line);
            $addr_out = array();

            $data_fields_mod = false;
            foreach ($data_fields as $field_item) {
                $field_item = trim($field_item);
                if (!in_array($field_item, $ignore_field) && !empty($store[$field_item])) {
                    $addr_out[] = ($field_item == 'city' && $state && isset($state->iso_code) && strlen($state->iso_code)) ?
                        $store[$field_item] . ', ' . $state->iso_code : $store[$field_item];
                    $data_fields_mod = true;
                }
            }
            if ($data_fields_mod) {
                $out_datas[] = implode(' ', $addr_out);
            }
        }

        $out = implode('<br />', $out_datas);

        return $out;
    }

    public function getStoresForXml()
    {
        // StarterTheme: Remove method when google maps v3 is done
        $distance_unit = Configuration::get('PS_DISTANCE_UNIT');
        if (!in_array($distance_unit, array('km', 'mi'))) {
            $distance_unit = 'km';
        }

        $distance = (int) Tools::getValue('radius', 100);
        $multiplicator = ($distance_unit == 'km' ? 6371 : 3959);

        $stores = Db::getInstance()->executeS('
        SELECT s.*, cl.name country, st.iso_code state,
        (' . (int) $multiplicator . '
            * acos(
                cos(radians(' . (float) Tools::getValue('latitude') . '))
                * cos(radians(latitude))
                * cos(radians(longitude) - radians(' . (float) Tools::getValue('longitude') . '))
                + sin(radians(' . (float) Tools::getValue('latitude') . '))
                * sin(radians(latitude))
            )
        ) distance,
        cl.id_country id_country
        FROM ' . _DB_PREFIX_ . 'store s
        ' . Shop::addSqlAssociation('store', 's') . '
        LEFT JOIN ' . _DB_PREFIX_ . 'country_lang cl ON (cl.id_country = s.id_country)
        LEFT JOIN ' . _DB_PREFIX_ . 'state st ON (st.id_state = s.id_state)
        WHERE s.active = 1 AND cl.id_lang = ' . (int) $this->context->language->id . '
        HAVING distance < ' . (int) $distance . '
        ORDER BY distance ASC
        LIMIT 0,20');

        return $stores;
    }

    /**
     * Display the Xml for showing the nodes in the google map.
     */
    protected function displayAjax()
    {
        // StarterTheme: Remove method when google maps v3 is done
        $stores = $this->getStoresForXml();
        $parnode = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><markers></markers>');

        foreach ($stores as $store) {
            $other = '';
            $newnode = $parnode->addChild('marker');
            $newnode->addAttribute('name', $store['name']);
            $address = $this->processStoreAddress($store);

            //$other .= $this->renderStoreWorkingHours($store);
            $newnode->addAttribute('addressNoHtml', strip_tags(str_replace('<br />', ' ', $address)));
            $newnode->addAttribute('address', $address);
            $newnode->addAttribute('other', $other);
            $newnode->addAttribute('phone', $store['phone']);
            $newnode->addAttribute('id_store', (int) $store['id_store']);
            $newnode->addAttribute('has_store_picture', file_exists(_PS_STORE_IMG_DIR_ . (int) $store['id_store'] . '.jpg'));
            $newnode->addAttribute('lat', (float) $store['latitude']);
            $newnode->addAttribute('lng', (float) $store['longitude']);
            if (isset($store['distance'])) {
                $newnode->addAttribute('distance', (int) $store['distance']);
            }
        }

        header('Content-type: text/xml');

        $this->ajaxRender($parnode->asXML());
    }

    /**
     * Assign template vars related to page content.
     *
     * @see FrontController::initContent()
     */
    public function initContent()
    {
        $distance_unit = Configuration::get('PS_DISTANCE_UNIT');
        if (!in_array($distance_unit, array('km', 'mi'))) {
            $distance_unit = 'km';
        }

        $this->context->smarty->assign(array(
            'mediumSize' => Image::getSize(ImageType::getFormattedName('medium')),
            'searchUrl' => $this->context->link->getPageLink('stores'),
            'distance_unit' => $distance_unit,
            'stores' => $this->getTemplateVarStores(),
        ));

        parent::initContent();
        $this->setTemplate('cms/stores');
    }

    public function getTemplateVarStores()
    {
        $stores = Store::getStores($this->context->language->id);

        $imageRetriever = new \PrestaShop\PrestaShop\Adapter\Image\ImageRetriever($this->context->link);

        foreach ($stores as &$store) {
            unset($store['active']);
            // Prepare $store.address
            $address = new Address();
            $store['address'] = [];
            $attr = ['address1', 'address2', 'postcode', 'city', 'id_state', 'id_country'];
            foreach ($attr as $a) {
                $address->{$a} = $store[$a];
                $store['address'][$a] = $store[$a];
                unset($store[$a]);
            }
            $store['address']['formatted'] = AddressFormat::generateAddress($address, array(), '<br />');

            // Prepare $store.business_hours
            // Required for trad
            $temp = json_decode($store['hours'], true);
            unset($store['hours']);
            $store['business_hours'] = [
                [
                    'day' => $this->trans('Monday', array(), 'Shop.Theme.Global'),
                    'hours' => $temp[0],
                ], [
                    'day' => $this->trans('Tuesday', array(), 'Shop.Theme.Global'),
                    'hours' => $temp[1],
                ], [
                    'day' => $this->trans('Wednesday', array(), 'Shop.Theme.Global'),
                    'hours' => $temp[2],
                ], [
                    'day' => $this->trans('Thursday', array(), 'Shop.Theme.Global'),
                    'hours' => $temp[3],
                ], [
                    'day' => $this->trans('Friday', array(), 'Shop.Theme.Global'),
                    'hours' => $temp[4],
                ], [
                    'day' => $this->trans('Saturday', array(), 'Shop.Theme.Global'),
                    'hours' => $temp[5],
                ], [
                    'day' => $this->trans('Sunday', array(), 'Shop.Theme.Global'),
                    'hours' => $temp[6],
                ],
            ];
            $store['image'] = $imageRetriever->getImage(new Store($store['id_store']), $store['id_store']);
            if (is_array($store['image'])) {
                $store['image']['legend'] = $store['image']['legend'][$this->context->language->id];
            }
        }

        return $stores;
    }
}
