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

/**
 * Class PageCore.
 */
class PageCore extends ObjectModel
{
    public $id_page_type;
    public $id_object;

    public $name;

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = [
        'table' => 'page',
        'primary' => 'id_page',
        'fields' => [
            'id_page_type' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true],
            'id_object' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedId'],
        ],
    ];

    /**
     * @return int Current page ID
     */
    public static function getCurrentId()
    {
        $controller = Dispatcher::getInstance()->getController();
        $pageTypeId = Page::getPageTypeByName($controller);

        /**
         * Some pages must be distinguished in order to record exactly what is being seen
         *
         * @todo dispatcher module
         */
        $specialArray = [
            'product' => 'id_product',
            'category' => 'id_category',
            'order' => 'step',
            'manufacturer' => 'id_manufacturer',
        ];

        $where = '';
        $insertData = [
            'id_page_type' => $pageTypeId,
        ];

        if (array_key_exists($controller, $specialArray)) {
            $objectId = Tools::getValue($specialArray[$controller], null);
            $where = ' AND `id_object` = ' . (int) $objectId;
            $insertData['id_object'] = (int) $objectId;
        }

        $sql = 'SELECT `id_page`
				FROM `' . _DB_PREFIX_ . 'page`
				WHERE `id_page_type` = ' . (int) $pageTypeId . $where;
        $result = Db::getInstance()->getRow($sql);
        if ($result['id_page']) {
            return $result['id_page'];
        }

        Db::getInstance()->insert('page', $insertData, true);

        return Db::getInstance()->Insert_ID();
    }

    /**
     * Return page type ID from page name.
     *
     * @param string $name Page name (E.g. product.php)
     */
    public static function getPageTypeByName($name)
    {
        if ($value = Db::getInstance()->getValue(
            '
				SELECT id_page_type
				FROM ' . _DB_PREFIX_ . 'page_type
				WHERE name = \'' . pSQL($name) . '\''
                )
            ) {
            return $value;
        }

        Db::getInstance()->insert('page_type', ['name' => pSQL($name)]);

        return Db::getInstance()->Insert_ID();
    }

    /**
     * Increase page viewed number by one.
     *
     * @param int $idPage Page ID
     */
    public static function setPageViewed($idPage)
    {
        $idDateRange = DateRange::getCurrentRange();
        $context = Context::getContext();

        // Try to increment the visits counter
        $sql = 'UPDATE `' . _DB_PREFIX_ . 'page_viewed`
				SET `counter` = `counter` + 1
				WHERE `id_date_range` = ' . (int) $idDateRange . '
					AND `id_page` = ' . (int) $idPage . '
					AND `id_shop` = ' . (int) $context->shop->id;
        Db::getInstance()->execute($sql);

        // If no one has seen the page in this date range, it is added
        if (Db::getInstance()->Affected_Rows() == 0) {
            Db::getInstance()->insert(
                'page_viewed',
                [
                    'id_date_range' => (int) $idDateRange,
                    'id_page' => (int) $idPage,
                    'counter' => 1,
                    'id_shop' => (int) $context->shop->id,
                    'id_shop_group' => (int) $context->shop->id_shop_group,
                ]
            );
        }
    }
}
