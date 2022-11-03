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
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */
class Cart extends CartCore
{
    /**
     * Update the address id of the cart
     *
     * @param int $id_address Current address id to change
     * @param int $id_address_new New address id
     */
    public function updateAddressId($id_address, $id_address_new)
    {
        $to_update = false;
        if (!isset($this->id_address_invoice) || $this->id_address_invoice == $id_address) {
            $to_update = true;
            $this->id_address_invoice = $id_address_new;
        }
        if (!isset($this->id_address_delivery) || $this->id_address_delivery == $id_address) {
            $to_update = true;
            $this->id_address_delivery = $id_address_new;
        }
        if ($to_update) {
            $this->update();
        }

        $sql = 'UPDATE `' . _DB_PREFIX_ . 'cart_product`
		SET `id_address_delivery` = ' . (int) $id_address_new . '
		WHERE  `id_cart` = ' . (int) $this->id . '
			AND `id_address_delivery` = ' . (int) $id_address;
        Db::getInstance()->execute($sql);

        $sql = 'UPDATE `' . _DB_PREFIX_ . 'customization`
			SET `id_address_delivery` = ' . (int) $id_address_new . '
			WHERE  `id_cart` = ' . (int) $this->id . '
				AND `id_address_delivery` = ' . (int) $id_address;
        Db::getInstance()->execute($sql);
    }

    public function delete()
    {
        if ($this->OrderExists()) { //NOT delete a cart which is associated with an order
            return false;
        }

        $uploaded_files = Db::getInstance()->executeS(
            '
			SELECT cd.`value`
			FROM `' . _DB_PREFIX_ . 'customized_data` cd
			INNER JOIN `' . _DB_PREFIX_ . 'customization` c ON (cd.`id_customization`= c.`id_customization`)
			WHERE cd.`type`= 0 AND c.`id_cart`=' . (int) $this->id
        );

        foreach ($uploaded_files as $must_unlink) {
            unlink(_PS_UPLOAD_DIR_ . $must_unlink['value'] . '_small');
            unlink(_PS_UPLOAD_DIR_ . $must_unlink['value']);
        }

        Db::getInstance()->execute(
            '
			DELETE FROM `' . _DB_PREFIX_ . 'customized_data`
			WHERE `id_customization` IN (
				SELECT `id_customization`
				FROM `' . _DB_PREFIX_ . 'customization`
				WHERE `id_cart`=' . (int) $this->id . '
			)'
        );

        Db::getInstance()->execute(
            '
			DELETE FROM `' . _DB_PREFIX_ . 'customization`
			WHERE `id_cart` = ' . (int) $this->id
        );

        if (!Db::getInstance()->execute('DELETE FROM `' . _DB_PREFIX_ . 'cart_rule` WHERE `id_cart` = ' . (int) $this->id)
         || !Db::getInstance()->execute('DELETE FROM `' . _DB_PREFIX_ . 'cart_product` WHERE `id_cart` = ' . (int) $this->id)) {
            return false;
        }

        return parent::delete();
    }
}
