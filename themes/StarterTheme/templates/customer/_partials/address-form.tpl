<section class="login-form">
  <form action="{$urls.pages.address}" method="post">

    <section class="form-fields">

      {block name="address_form_item_alias"}
        <label>
          <span>{l s='Address alias'}</span>
          {block name="required_field"}{include file="_partials/form-required-field.tpl" field_name=alias required=$required_fields}{/block}
          <input type="text" name="alias" id="alias" value="{$address.alias}" />
          {block name="form_field_error"}{include file="_partials/form-field-errors.tpl" errors=$form_errors.alias}{/block}
        </label>
      {/block}

      {foreach from=$ordered_address_fields item=field_name}

        {if $field_name == 'firstname'}
          {block name="address_form_item_firstname"}
            <label>
              <span>{l s='First name'}</span>
              {block name="required_field"}{include file="_partials/form-required-field.tpl" field_name=$field_name required=$required_fields}{/block}
              <input type="text" name="firstname" id="firstname" value="{$address.firstname}" />
            </label>
          {/block}
        {/if}

        {if $field_name == 'lastname'}
          {block name="address_form_item_lastname"}
            <label>
              <span>{l s='Last name'}</span>
              {block name="required_field"}{include file="_partials/form-required-field.tpl" field_name=$field_name required=$required_fields}{/block}
              <input type="text" name="lastname" id="lastname" value="{$address.lastname}" />
              {block name="form_field_error"}{include file="_partials/form-field-errors.tpl" errors=$form_errors[$field_name]}{/block}
            </label>
          {/block}
        {/if}

        {if $field_name == 'address1'}
          {block name="address_form_item_address1"}
            <label>
              <span>{l s='Address'}</span>
              {block name="required_field"}
                {include file="_partials/form-required-field.tpl" field_name=$field_name required=$required_fields}
              {/block}
              <input type="text" name="address1" id="address1" value="{$address.address1}" />
              {block name="form_field_error"}{include file="_partials/form-field-errors.tpl" errors=$form_errors[$field_name]}{/block}
            </label>
          {/block}
        {/if}

        {if $field_name == 'address2'}
          {block name="address_form_item_address2"}
            <label>
              <span>{l s='Address'}</span>
              {block name="required_field"}{include file="_partials/form-required-field.tpl" field_name=$field_name required=$required_fields}{/block}
              <input type="text" name="address2" id="address2" value="{$address.address2}" />
              {block name="form_field_error"}{include file="_partials/form-field-errors.tpl" errors=$form_errors[$field_name]}{/block}
            </label>
          {/block}
        {/if}

        {if $field_name == 'postcode'}
          {block name="address_form_item_postcode"}
            <label>
              <span>{l s='Zip/Postal Code'}</span>
              {block name="required_field"}{include file="_partials/form-required-field.tpl" field_name=$field_name required=$required_fields}{/block}
              <input type="text" name="postcode" id="postcode" value="{$address.postcode}" />
              {block name="form_field_error"}{include file="_partials/form-field-errors.tpl" errors=$form_errors[$field_name]}{/block}
            </label>
          {/block}
        {/if}

        {if $field_name == 'city'}
          {block name="address_form_item_city"}
            <label>
              <span>{l s='City'}</span>
              {block name="required_field"}{include file="_partials/form-required-field.tpl" field_name=$field_name required=$required_fields}{/block}
              <input type="text" name="city" id="city" value="{$address.city}" />
              {block name="form_field_error"}{include file="_partials/form-field-errors.tpl" errors=$form_errors[$field_name]}{/block}
            </label>
          {/block}
        {/if}

        {if $field_name == 'Country:name' || $field_name == 'country'}
          {block name="address_form_item_country"}
            <label>
              <span>{l s='Country'}</span>
              {block name="required_field"}{include file="_partials/form-required-field.tpl" field_name=$field_name required=$required_fields}{/block}
              {block name="form_item_country"}
                {include file="customer/_partials/form-item-country.tpl" countries=$countries sl_country=$address.id}
              {/block}
              {block name="form_field_error"}{include file="_partials/form-field-errors.tpl" errors=$form_errors[$field_name]}{/block}
            </label>
          {/block}
        {/if}

        {if $field_name == 'State:name' && $address.id_state}
          {block name="address_form_item_state"}
          <label>
            <span>{l s='Country'}</span>
            {block name="required_field"}{include file="_partials/form-required-field.tpl" field_name=$field_name required=$required_fields}{/block}
              {block name="form_item_state"}
                {include file="customer/_partials/form-item-state.tpl" states=$countries[$address.id][states] sl_state=$address.id_state}
              {/block}
            {block name="form_field_error"}{include file="_partials/form-field-errors.tpl" errors=$form_errors[$field_name]}{/block}
          </label>
          {/block}
        {/if}

        {if $field_name == 'phone'}
          {block name="address_form_item_phone"}
            <label>
              <span>{l s='Home phone'}</span>
              {block name="required_field"}{include file="_partials/form-required-field.tpl" field_name=$field_name required=$required_fields}{/block}
              <input type="text" name="phone" id="phone" value="{$address.phone}" />
              {block name="form_field_error"}{include file="_partials/form-field-errors.tpl" errors=$form_errors[$field_name]}{/block}
            </label>
          {/block}
        {/if}

        {if $field_name == 'phone_mobile'}
          {block name="address_form_item_phone_mobile"}
            <label>
              <span>{l s='Mobile phone'}</span>
              {block name="required_field"}{include file="_partials/form-required-field.tpl" field_name=$field_name required=$required_fields}{/block}
              <input type="text" name="phone_mobile" id="phone_mobile" value="{$address.phone_mobile}" />
              {block name="form_field_error"}{include file="_partials/form-field-errors.tpl" errors=$form_errors[$field_name]}{/block}
            </label>
          {/block}
        {/if}

        {if $field_name == 'company'}
          {block name="address_form_item_company"}
            <label>
              <span>{l s='Company'}</span>
              {block name="required_field"}{include file="_partials/form-required-field.tpl" field_name=$field_name required=$required_fields}{/block}
              <input type="text" name="company" id="company" value="{$address.company}" />
              {block name="form_field_error"}{include file="_partials/form-field-errors.tpl" errors=$form_errors[$field_name]}{/block}
            </label>
          {/block}
        {/if}

        {if $field_name == 'vat_number'}
          {block name="address_form_item_vat_number"}
            <label>
              <span>{l s='VAT number'}</span>
              {block name="required_field"}{include file="_partials/form-required-field.tpl" field_name=$field_name required=$required_fields}{/block}
              <input type="text" name="vat_number" id="vat_number" value="{$address.vat_number}" />
              {block name="form_field_error"}{include file="_partials/form-field-errors.tpl" errors=$form_errors[$field_name]}{/block}
            </label>
          {/block}
        {/if}

        {if $field_name == 'dni'}
          {block name="address_form_item_dni"}
            <label>
              <span>{l s='Identification number'}</span>
              {block name="required_field"}{include file="_partials/form-required-field.tpl" field_name=$field_name required=$required_fields}{/block}
              <input type="text" name="dni" id="dni" value="{$address.dni}" />
              {block name="form_field_error"}{include file="_partials/form-field-errors.tpl" errors=$form_errors[$field_name]}{/block}
            </label>
          {/block}
        {/if}

      {/foreach}
    </section>

    <footer class="form-footer">
      <input type="hidden" class="hidden" name="id_address" value="{$address.id}" />
      <input type="hidden" class="hidden" name="back" value="{$back}" />
      <input type="hidden" class="hidden" name="mod" value="{$mod}" />
      <input type="hidden" name="token" value="{$token}" />

      <button type="submit" id="submitAddress" name="submitAddress">
        {l s='Save'}
      </button>
    </footer>

  </form>
</section>
