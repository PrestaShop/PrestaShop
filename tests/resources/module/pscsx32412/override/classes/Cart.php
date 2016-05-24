<?php


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

        $sql = 'UPDATE `'._DB_PREFIX_.'cart_product`
		SET `id_address_delivery` = '.(int)$id_address_new.'
		WHERE  `id_cart` = '.(int)$this->id.'
			AND `id_address_delivery` = '.(int)$id_address;
        Db::getInstance()->execute($sql);

        $sql = 'UPDATE `'._DB_PREFIX_.'customization`
			SET `id_address_delivery` = '.(int)$id_address_new.'
			WHERE  `id_cart` = '.(int)$this->id.'
				AND `id_address_delivery` = '.(int)$id_address;
        Db::getInstance()->execute($sql);
    }

    public function delete()
    {
        if ($this->OrderExists()) { //NOT delete a cart which is associated with an order
            return false;
        }

        $uploaded_files = Db::getInstance()->executeS('
			SELECT cd.`value`
			FROM `'._DB_PREFIX_.'customized_data` cd
			INNER JOIN `'._DB_PREFIX_.'customization` c ON (cd.`id_customization`= c.`id_customization`)
			WHERE cd.`type`= 0 AND c.`id_cart`='.(int)$this->id
        );

        foreach ($uploaded_files as $must_unlink) {
            unlink(_PS_UPLOAD_DIR_.$must_unlink['value'].'_small');
            unlink(_PS_UPLOAD_DIR_.$must_unlink['value']);
        }

        Db::getInstance()->execute('
			DELETE FROM `'._DB_PREFIX_.'customized_data`
			WHERE `id_customization` IN (
				SELECT `id_customization`
				FROM `'._DB_PREFIX_.'customization`
				WHERE `id_cart`='.(int)$this->id.'
			)'
        );

        Db::getInstance()->execute('
			DELETE FROM `'._DB_PREFIX_.'customization`
			WHERE `id_cart` = '.(int)$this->id
        );

        if (!Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'cart_cart_rule` WHERE `id_cart` = '.(int)$this->id)
         || !Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'cart_product` WHERE `id_cart` = '.(int)$this->id)) {
            return false;
        }

        return parent::delete();
    }
}
