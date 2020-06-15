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
 * Class QuickAccessCore.
 */
class QuickAccessCore extends ObjectModel
{
    /** @var string Name */
    public $name;

    /** @var string Link */
    public $link;

    /** @var bool New windows or not */
    public $new_window;

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = [
        'table' => 'quick_access',
        'primary' => 'id_quick_access',
        'multilang' => true,
        'fields' => [
            'link' => ['type' => self::TYPE_STRING, 'validate' => 'isUrl', 'required' => true, 'size' => 255],
            'new_window' => ['type' => self::TYPE_BOOL, 'validate' => 'isBool', 'required' => true],

            /* Lang fields */
            'name' => ['type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isCleanHtml', 'required' => true, 'size' => 32],
        ],
    ];

    /**
     * Get all available quick_accesses.
     *
     * @return array QuickAccesses
     */
    public static function getQuickAccesses($idLang)
    {
        return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
		SELECT *
		FROM `' . _DB_PREFIX_ . 'quick_access` qa
		LEFT JOIN `' . _DB_PREFIX_ . 'quick_access_lang` qal ON (qa.`id_quick_access` = qal.`id_quick_access` AND qal.`id_lang` = ' . (int) $idLang . ')
		ORDER BY `name` ASC');
    }

    /**
     * Get all available quick_accesses with token.
     *
     * @return array QuickAccesses
     */
    public static function getQuickAccessesWithToken($idLang, $idEmployee)
    {
        $quickAccess = self::getQuickAccesses($idLang);

        if (empty($quickAccess)) {
            return false;
        }

        foreach ($quickAccess as $index => $quick) {
            // first, clean url to have a real quickLink
            $quick['link'] = Context::getContext()->link->getQuickLink($quick['link']);
            $tokenString = $idEmployee;

            if ('../' === $quick['link'] && Shop::getContext() == Shop::CONTEXT_SHOP) {
                $url = Context::getContext()->shop->getBaseURL();
                if (!$url) {
                    unset($quickAccess[$index]);

                    continue;
                }
                $quickAccess[$index]['link'] = $url;
            } else {
                preg_match('/controller=(.+)(&.+)?$/', $quick['link'], $admin_tab);
                if (isset($admin_tab[1])) {
                    if (strpos($admin_tab[1], '&')) {
                        $admin_tab[1] = substr($admin_tab[1], 0, strpos($admin_tab[1], '&'));
                    }
                    $quick_access[$index]['target'] = $admin_tab[1];

                    $tokenString = $admin_tab[1] . (int) Tab::getIdFromClassName($admin_tab[1]) . $idEmployee;
                }
                $quickAccess[$index]['link'] = Context::getContext()->link->getBaseLink() . basename(_PS_ADMIN_DIR_) . '/' . $quick['link'];
            }

            if (false === strpos($quickAccess[$index]['link'], 'token')) {
                $separator = strpos($quickAccess[$index]['link'], '?') ? '&' : '?';
                $quickAccess[$index]['link'] .= $separator . 'token=' . Tools::getAdminToken($tokenString);
            }
        }

        return $quickAccess;
    }

    /**
     * Toggle new window.
     *
     * @return bool
     *
     * @throws PrestaShopException
     */
    public function toggleNewWindow()
    {
        if (!array_key_exists('new_window', $this)) {
            throw new PrestaShopException('property "new_window" is missing in object ' . get_class($this));
        }

        $this->setFieldsToUpdate(['new_window' => true]);

        $this->new_window = !(int) $this->new_window;

        return $this->update(false);
    }
}
