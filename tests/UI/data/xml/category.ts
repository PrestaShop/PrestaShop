import {faker} from '@faker-js/faker';

/**
 * Get xml of category to put on Post/Put request
 * @param idCategory {string|null}
 */
export default function getCategoryXml(idCategory: string | null = null): string {
  const department1: string = faker.commerce.department();
  const department2: string = faker.commerce.department();
  const linkRewriteEn: string = `${faker.helpers.slugify(department1).toLowerCase()}`
    + `-${faker.string.alphanumeric(4).toLowerCase()}`;
  const linkRewriteFr: string = `${faker.helpers.slugify(department2).toLowerCase()}`
    + `-${faker.string.alphanumeric(4).toLowerCase()}`;

  return `<?xml version="1.0" encoding="UTF-8"?>
<prestashop xmlns:xlink="http://www.w3.org/1999/xlink">
  <category>
    ${idCategory !== null ? `<id><![CDATA[${idCategory}]]></id>` : ''}
    <id_parent><![CDATA[2]]></id_parent>
    <active><![CDATA[${faker.helpers.arrayElement([0, 1])}]]></active>
    <id_shop_default><![CDATA[1]]></id_shop_default>
    <name>
      <language id="1"><![CDATA[${department1} ${faker.string.alphanumeric(4)}]]></language>
      <language id="2"><![CDATA[${department2} ${faker.string.alphanumeric(4)}]]></language>
    </name>
    <link_rewrite>
      <language id="1"><![CDATA[${linkRewriteEn}]]></language>
      <language id="2"><![CDATA[${linkRewriteFr}]]></language>
    </link_rewrite>
    <description>
      <language id="1"><![CDATA[${faker.lorem.sentence()}]]></language>
      <language id="2"><![CDATA[${faker.lorem.sentence()}]]></language>
    </description>
    <additional_description>
      <language id="1"><![CDATA[${faker.lorem.sentence()}]]></language>
      <language id="2"><![CDATA[${faker.lorem.sentence()}]]></language>
    </additional_description>
    <meta_title>
      <language id="1"><![CDATA[${faker.commerce.department()}]]></language>
      <language id="2"><![CDATA[${faker.commerce.department()}]]></language>
    </meta_title>
    <meta_description>
      <language id="1"><![CDATA[${faker.lorem.sentence()}]]></language>
      <language id="2"><![CDATA[${faker.lorem.sentence()}]]></language>
    </meta_description>
  </category>
</prestashop>`;
}
