<select name="id_address" ps-value="prestashop.cart.id_address_delivery">
  <option ps-each-address="prestashop.customer.addresses | propertyList" ps-value="address.id">[[ address.alias ]]</option>
</select>

<div class="address_formatted" ps-html="prestashop.cart.id_address_delivery | customerAddress"></div>
