/**
 * @param idShoppingCart {number}
 * @return string
 */
function getOrderXmlCreate(idShoppingCart: number): string {
  return ('<?xml version="1.0" encoding="UTF-8"?>\n'
  + '<prestashop xmlns:xlink="http://www.w3.org/1999/xlink">\n'
  + '  <order>\n'
  + '    <id_address_delivery>2</id_address_delivery>\n'
  + '    <id_address_invoice>5</id_address_invoice>\n'
  + `    <id_cart>${idShoppingCart}</id_cart>\n`
  + '    <id_currency>1</id_currency>\n'
  + '    <id_lang>1</id_lang>\n'
  + '    <id_customer>2</id_customer>\n'
  + '    <id_carrier>1</id_carrier>\n'
  + '    <module><![CDATA[ps_wirepayment]]></module>\n'
  + '    <payment><![CDATA[Bank transfer]]></payment>\n'
  + '    <recyclable>0</recyclable>\n'
  + '    <gift>0</gift>\n'
  + '    <gift_message><![CDATA[]]></gift_message>\n'
  + '    <total_paid><![CDATA[22.940000]]></total_paid>\n'
  + '    <total_paid_real><![CDATA[1.230000]]></total_paid_real>\n'
  + '    <total_products><![CDATA[19.120000]]></total_products>\n'
  + '    <total_products_wt><![CDATA[22.940000]]></total_products_wt>\n'
  + '    <conversion_rate><![CDATA[1.000000]]></conversion_rate>\n'
  + '  </order>\n'
  + '</prestashop>');
}

/**
 * @param idShoppingCart {number}
 * @param data {{{ idOrder: string, invoiceNumber: string, invoiceDate: string, secureKey: string },}}
 * @return string
 */
function getOrderXmlUpdate(
  idShoppingCart: number,
  data: { idOrder: string, invoiceNumber: string, invoiceDate: string, secureKey: string },
): string {
  return ('<?xml version="1.0" encoding="UTF-8"?>\n'
      + '<prestashop xmlns:xlink="http://www.w3.org/1999/xlink">\n'
      + '  <order>\n'
      + `    <id>${data.idOrder}</id>\n`
      + '    <id_address_delivery>2</id_address_delivery>\n'
      + '    <id_address_invoice>5</id_address_invoice>\n'
      + `    <id_cart>${idShoppingCart}</id_cart>\n`
      + '    <id_currency>1</id_currency>\n'
      + '    <id_lang>1</id_lang>\n'
      + '    <id_customer>2</id_customer>\n'
      + '    <id_carrier>2</id_carrier>\n'
      + '    <current_state>1</current_state>\n'
      + '    <module><![CDATA[ps_checkpayment]]></module>\n'
      + `    <invoice_number><![CDATA[${data.invoiceNumber}]]></invoice_number>`
      + `    <invoice_date><![CDATA[${data.invoiceDate}]]></invoice_date>\n`
      + '    <shipping_number><![CDATA[123ABCDEF]]></shipping_number>\n'
      + '    <valid>1</valid>\n'
      + '    <note><![CDATA[Da Ba De Da Da]]></note>\n'
      + '    <id_shop_group>1</id_shop_group>\n'
      + '    <id_shop>1</id_shop>\n'
      + `    <secure_key><![CDATA[${data.secureKey}]]></secure_key>\n`
      + '    <payment><![CDATA[Payment by check]]></payment>\n'
      + '    <recyclable>1</recyclable>\n'
      + '    <gift>1</gift>\n'
      + '    <gift_message><![CDATA[Gift Message]]></gift_message>\n'
      + '    <total_paid><![CDATA[22.940000]]></total_paid>\n'
      + '    <total_paid_real><![CDATA[4.560000]]></total_paid_real>\n'
      + '    <total_products><![CDATA[19.120000]]></total_products>\n'
      + '    <total_products_wt><![CDATA[22.940000]]></total_products_wt>\n'
      + '    <conversion_rate><![CDATA[1.000000]]></conversion_rate>\n'
      + '    <reference><![CDATA[JKLMNOPQR]]></reference>\n'
      + '  </order>\n'
      + '</prestashop>');
}

/**
 * Get xml of order to put on POST/PUT request
 * @param idShoppingCart {number}
 * @param data {{ idOrder: string, invoiceNumber: string, invoiceDate: string, secureKey: string }|null}
 */
export default function getOrderXml(
  idShoppingCart: number,
  data: any = null,
): string {
  if (data) {
    return getOrderXmlUpdate(idShoppingCart, data);
  }

  return getOrderXmlCreate(idShoppingCart);
}
