import {faker} from '@faker-js/faker';

/**
 * Get xml of store to put on Post/Put request
 * @param idStore {string|null}
 */
export function getStoreXml(idStore: string | null = null): string {
  return ('<?xml version="1.0" encoding="UTF-8"?>'
    + '<prestashop xmlns:xlink="http://www.w3.org/1999/xlink">'
    + `<store>${idStore !== null ? (`<id><![CDATA[${idStore}]]></id>`) : ''}`
    + '<id_country><![CDATA[11]]></id_country>'
    + '<id_state><![CDATA[]]></id_state>'
    + '<hours>'
    + '<language id="1"><![CDATA[[["4h30AM - 5h30PM"],["5h30AM - 5h30PM"],["6h30AM - 5h30PM"],["7h30AM - 5h30PM"],'
    + '["8h30AM - 5h30PM"],["9h30AM - 5h30PM"],["10h30AM - 5h30PM"]]]]></language>'
    + '<language id="2"><![CDATA[[["4h30 - 17h30"],["5h30 - 17h30"],["6h30 - 17h30"],["7h30 - 17h30"],'
    + '["8h30 - 17h30"],["9h30 - 17h30"],["10h30 - 17h30"]]]]></language>'
    + '</hours>'
    + '<postcode><![CDATA[605-0862]]></postcode>'
    + '<city><![CDATA[Kyoto]]></city>'
    + '<latitude><![CDATA[35.01160000]]></latitude>'
    + '<longitude><![CDATA[135.00000000]]></longitude>'
    + '<phone><![CDATA[+81 75-525-7170]]></phone>'
    + '<fax><![CDATA[+81 75-525-7777]]></fax>'
    + '<email><![CDATA[ghibli@prestashop-test.com]]></email>'
    + '<active><![CDATA[1]]></active>'
    + '<date_add><![CDATA[2023-06-05 13:50:32]]></date_add>'
    + '<date_upd><![CDATA[2023-06-05 13:52:01]]></date_upd>'
    + '<name>'
    + '<language id="1"><![CDATA[Ghibli Store]]></language>'
    + '<language id="2"><![CDATA[Magasin Ghibli]]></language>'
    + '</name>'
    + '<address1>'
    + '<language id="1"><![CDATA[2 Chome-218-1 Kiyomizu]]></language>'
    + '<language id="2"><![CDATA[2 Chome-218-1 Kiyomizu Fr]]></language>'
    + '</address1>'
    + '<address2>'
    + '<language id="1"><![CDATA[Higashiyama Ward]]></language>'
    + '<language id="2"><![CDATA[Higashiyama Ward Fr]]></language>'
    + '</address2>'
    + '<note>'
    + '<language id="1"><![CDATA[My Neighbor Totoro]]></language>'
    + '<language id="2"><![CDATA[Mon voisin Totoro]]></language>'
    + '</note>'
    + '</store>'
    + '</prestashop>');
}

export function getUpdateStoreXml(idStore: string | null = null): string {
  return ('<?xml version="1.0" encoding="UTF-8"?>'
    + '<prestashop xmlns:xlink="http://www.w3.org/1999/xlink">'
    + `<store>${idStore !== null ? (`<id><![CDATA[${idStore}]]></id>`) : ''}`
    + `<id_country><![CDATA[${faker.number.int({min: 1, max: 5})}]]></id_country>`
    + '<id_state><![CDATA[]]></id_state>'
    + '<hours>'
    + '<language id="1"><![CDATA[[["8h30AM - 5h30PM"],["8h30AM - 6h30PM"],["8h30AM - 7h30PM"],["8h30AM - 8h30PM"],'
    + '["8h30AM - 9h30PM"],["8h30AM - 10h30PM"],["8h30AM - 11h30PM"]]]]></language>'
    + '<language id="2"><![CDATA[[["8h30 - 17h30"],["8h30 - 18h30"],["8h30 - 19h30"],["8h30 - 20h30"],'
    + '["8h30 - 21h30"],["8h30 - 22h30"],["8h30 - 23h30"]]]]></language>'
    + '</hours>'
    + `<postcode><![CDATA[${faker.number.int({min: 10000, max: 99999})}]]></postcode>`
    + `<city><![CDATA[${faker.location.city()}]]></city>`
    + '<latitude><![CDATA[35.01159999]]></latitude>'
    + '<longitude><![CDATA[135.76810001]]></longitude>'
    + '<phone><![CDATA[+81 75-525-1234]]></phone>'
    + '<fax><![CDATA[+81 75-525-5678]]></fax>'
    + `<email><![CDATA[${faker.internet.email()}]]></email>`
    + '<active><![CDATA[1]]></active>'
    + '<date_add><![CDATA[2023-06-05 13:50:32]]></date_add>'
    + '<date_upd><![CDATA[2023-06-05 13:52:01]]></date_upd>'
    + '<name>'
    + '<language id="1"><![CDATA[Ghibli Store]]></language>'
    + '<language id="2"><![CDATA[Magasin Ghibli]]></language>'
    + '</name>'
    + '<address1>'
    + `<language id="1"><![CDATA[${faker.location.street()}]]></language>`
    + `<language id="2"><![CDATA[${faker.location.street()}]]></language>`
    + '</address1>'
    + '<address2>'
    + `<language id="1"><![CDATA[${faker.location.street()}]]></language>`
    + `<language id="2"><![CDATA[${faker.location.street()}]]></language>`
    + '</address2>'
    + '<note>'
    + `<language id="1"><![CDATA[${faker.word.words(5)}]]></language>`
    + `<language id="2"><![CDATA[${faker.word.words(5)}]]></language>`
    + '</note>'
    + '</store>'
    + '</prestashop>');
}
