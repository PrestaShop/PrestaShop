import {faker} from '@faker-js/faker';

/**
 * Get xml of country to put on Post/Put request
 * @param idCountry {string|null}
 */
export default function getCountryXml(idCountry : string | null = null): string {
  return ('<?xml version="1.0" encoding="UTF-8"?>'
    + '<prestashop xmlns:xlink="http://www.w3.org/1999/xlink">'
    + `<country>${idCountry !== null ? (`<id><![CDATA[${idCountry}]]></id>`) : ''}`
    + `<id_zone><![CDATA[${faker.datatype.number({min: 1, max: 5})}]]></id_zone>`
    + '<id_currency><![CDATA[1]]></id_currency>'
    + `<call_prefix><![CDATA[${faker.datatype.number({min: 100, max: 200})}]]></call_prefix>`
    + `<iso_code><![CDATA[${faker.word.adjective(3)}]]></iso_code>`
    + `<active><![CDATA[${faker.helpers.arrayElement([0, 1])}]]></active>`
    + `<contains_states><![CDATA[${faker.helpers.arrayElement([0, 1])}]]></contains_states>`
    + `<need_identification_number><![CDATA[${faker.helpers.arrayElement([0, 1])}]]></need_identification_number>`
    + `<need_zip_code><![CDATA[${faker.helpers.arrayElement([0, 1])}]]></need_zip_code>`
    + `<zip_code_format><![CDATA[${faker.helpers.arrayElement(['NNLLNN', 'LLNNLL'])}]]></zip_code_format>`
    + `<display_tax_label><![CDATA[${faker.helpers.arrayElement([0, 1])}]]></display_tax_label>`
    + '<name>'
    + '<language id="1"><![CDATA[Test in English]]></language>'
    + '<language id="2"><![CDATA[Test en Français]]></language>'
    + '</name>'
    + '</country>'
    + '</prestashop>');
}
