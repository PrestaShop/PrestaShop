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
class StoresControllerCore extends FrontController
{
    /** @var string */
    public $php_self = 'stores';

    /**
     * Get formatted string address.
     *
     * @param array $store
     *
     * @return string
     */
    protected function processStoreAddress($store)
    {
        $ignore_field = [
            'firstname',
            'lastname',
        ];

        $out_datas = [];

        $address_datas = AddressFormat::getOrderedAddressFields($store['id_country'], false, true);
        $state = (isset($store['id_state'])) ? new State($store['id_state']) : null;

        foreach ($address_datas as $data_line) {
            $data_fields = explode(' ', $data_line);
            $addr_out = [];

            $data_fields_mod = false;
            foreach ($data_fields as $field_item) {
                $field_item = trim($field_item);
                if (!in_array($field_item, $ignore_field) && !empty($store[$field_item])) {
                    $addr_out[] = ($field_item == 'city' && $state && !empty($state->iso_code)) ?
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
        $distance_unit = Configuration::get('PS_DISTANCE_UNIT');
        if (!in_array($distance_unit, ['km', 'mi'])) {
            $distance_unit = 'km';
        }

        $distance = (int) Tools::getValue('radius', 100);
        $multiplicator = ($distance_unit == 'km' ? 6371 : 3959);

        $langId = (int) Tools::getValue('lang', Configuration::get('PS_LANG_DEFAULT'));

        $stores = Db::getInstance()->executeS('
        SELECT s.*, sl.*, cl.name country, st.iso_code state,
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
        LEFT JOIN ' . _DB_PREFIX_ . 'country_lang cl ON (cl.id_country = s.id_country AND cl.id_lang = ' . (int) $langId . ')
        LEFT JOIN ' . _DB_PREFIX_ . 'store_lang sl ON (sl.id_store = s.id_store AND sl.id_lang = ' . (int) $langId . ')
        LEFT JOIN ' . _DB_PREFIX_ . 'state st ON (st.id_state = s.id_state)
        WHERE s.active = 1
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
        $stores = $this->getStoresForXml();
        $parnode = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><markers></markers>');

        foreach ($stores as $store) {
            $newnode = $parnode->addChild('marker');
            $newnode->addAttribute('name', $store['name']);
            $address = $this->processStoreAddress($store);

            $newnode->addAttribute('addressNoHtml', strip_tags(str_replace('<br />', ' ', $address)));
            $newnode->addAttribute('address', $address);
            $newnode->addAttribute('hours', trim($store['hours']));
            $newnode->addAttribute('phone', $store['phone']);
            $newnode->addAttribute('fax', $store['fax']);
            $newnode->addAttribute('note', $store['note']);
            $newnode->addAttribute('id_store', (string) (int) $store['id_store']);
            $newnode->addAttribute('has_store_picture', (string) file_exists(_PS_STORE_IMG_DIR_ . (int) $store['id_store'] . '.jpg'));
            $newnode->addAttribute('lat', (string) (float) $store['latitude']);
            $newnode->addAttribute('lng', (string) (float) $store['longitude']);
            if (isset($store['distance'])) {
                $newnode->addAttribute('distance', (string) (int) $store['distance']);
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
        if (!in_array($distance_unit, ['km', 'mi'])) {
            $distance_unit = 'km';
        }

        $this->context->smarty->assign([
            'mediumSize' => Image::getSize(ImageType::getFormattedName('medium')),
            'searchUrl' => $this->context->link->getPageLink('stores'),
            'distance_unit' => $distance_unit,
            'stores' => $this->getTemplateVarStores(),
        ]);

        parent::initContent();
        $this->setTemplate('cms/stores');
    }

    public function getTemplateVarStores()
    {
        $stores = Store::getStores($this->context->language->id);

        $imageRetriever = new \PrestaShop\PrestaShop\Adapter\Image\ImageRetriever($this->context->link);
        $attr = ['address1', 'address2', 'postcode', 'city', 'id_state', 'id_country'];

        foreach ($stores as &$store) {
            unset($store['active']);
            // Prepare $store.address
            $address = new Address();
            $store['address'] = [];

            foreach ($attr as $a) {
                $address->{$a} = $store[$a];
                $store['address'][$a] = $store[$a];
                unset($store[$a]);
            }
            $store['address']['formatted'] = AddressFormat::generateAddress($address, [], '<br />');

            // Prepare $store.business_hours
            // Required for trad
            $temp = json_decode($store['hours'], true);
            unset($store['hours']);
            $store['business_hours'] = [
                [
                    'day' => $this->trans('Monday', [], 'Shop.Theme.Global'),
                    'hours' => $temp[0],
                ], [
                    'day' => $this->trans('Tuesday', [], 'Shop.Theme.Global'),
                    'hours' => $temp[1],
                ], [
                    'day' => $this->trans('Wednesday', [], 'Shop.Theme.Global'),
                    'hours' => $temp[2],
                ], [
                    'day' => $this->trans('Thursday', [], 'Shop.Theme.Global'),
                    'hours' => $temp[3],
                ], [
                    'day' => $this->trans('Friday', [], 'Shop.Theme.Global'),
                    'hours' => $temp[4],
                ], [
                    'day' => $this->trans('Saturday', [], 'Shop.Theme.Global'),
                    'hours' => $temp[5],
                ], [
                    'day' => $this->trans('Sunday', [], 'Shop.Theme.Global'),
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

    public function getBreadcrumbLinks()
    {
        $breadcrumb = parent::getBreadcrumbLinks();

        $breadcrumb['links'][] = [
            'title' => $this->trans('Our stores', [], 'Shop.Theme.Global'),
            'url' => $this->context->link->getPageLink('stores', true),
        ];

        return $breadcrumb;
    }
}
