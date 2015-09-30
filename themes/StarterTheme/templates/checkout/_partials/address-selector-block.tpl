<select ps-value="{$value_id}">
  <option ps-each-address="prestashop.customer.addresses | propertyList" ps-value="address.id">[[ address.alias ]]</option>
</select>

<div class="address_formatted" ps-html="{$value_id} | customerAddress"></div>
