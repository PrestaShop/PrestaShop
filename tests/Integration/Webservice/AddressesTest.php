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

declare(strict_types=1);

namespace Tests\Integration\Webservice;

use Address;
use Symfony\Component\DomCrawler\Crawler;
use WebserviceKey;

class AddressesTest extends AbstractWebserviceTest
{
    private const INITIAL_COUNT = 5;

    public function testReturnEmptyWSKey(): void
    {
        $output = $this->requestWebserviceXML('', 'GET', 'addresses');

        self::assertEquals(1, $output->filter('prestashop > errors > error')->count());
        self::assertEquals(17, $output->filter('prestashop > errors > error')->first()->filter('code')->text());
        self::assertEquals(
            'Authentication key is empty',
            $output->filter('prestashop > errors > error')->first()->filter('message')->text()
        );
    }

    public function testReturnBadKey(): void
    {
        $output = $this->requestWebserviceXML('ABCDE', 'GET', 'addresses');

        self::assertEquals(1, $output->filter('prestashop > errors > error')->count());
        self::assertEquals(18, $output->filter('prestashop > errors > error')->first()->filter('code')->text());
        self::assertEquals(
            'Invalid authentication key format',
            $output->filter('prestashop > errors > error')->first()->filter('message')->text()
        );
    }

    public function testReturnDisabledKey(): void
    {
        $this->wsKey->active = false;
        $this->wsKey->save();

        $output = $this->requestWebserviceXML($this->wsKey->key, 'GET', 'addresses');

        self::assertEquals(2, $output->filter('prestashop > errors > error')->count());
        self::assertEquals(20, $output->filter('prestashop > errors > error')->first()->filter('code')->text());
        self::assertEquals(
            'Authentification key is not active',
            $output->filter('prestashop > errors > error')->first()->filter('message')->text()
        );

        self::assertEquals(21, $output->filter('prestashop > errors > error')->last()->filter('code')->text());
        self::assertEquals(
            'No permission for this authentication key',
            $output->filter('prestashop > errors > error')->last()->filter('message')->text()
        );
    }

    public function testReturnNoPermissions(): void
    {
        WebserviceKey::setPermissionForAccount(
            $this->wsKey->id,
            [
                'addresses' => [],
            ]
        );

        $output = $this->requestWebserviceXML($this->wsKey->key, 'GET', 'addresses');

        self::assertEquals(1, $output->filter('prestashop > errors > error')->count());
        self::assertEquals(21, $output->filter('prestashop > errors > error')->first()->filter('code')->text());
        self::assertEquals(
            'No permission for this authentication key',
            $output->filter('prestashop > errors > error')->first()->filter('message')->text()
        );

        $output = $this->requestWebserviceXML($this->wsKey->key, 'HEAD', 'addresses');

        self::assertEquals(1, $output->filter('prestashop > errors > error')->count());
        self::assertEquals(21, $output->filter('prestashop > errors > error')->first()->filter('code')->text());
        self::assertEquals(
            'No permission for this authentication key',
            $output->filter('prestashop > errors > error')->first()->filter('message')->text()
        );

        $output = $this->requestWebserviceXML($this->wsKey->key, 'DELETE', 'addresses');

        self::assertEquals(1, $output->filter('prestashop > errors > error')->count());
        self::assertEquals(21, $output->filter('prestashop > errors > error')->first()->filter('code')->text());
        self::assertEquals(
            'No permission for this authentication key',
            $output->filter('prestashop > errors > error')->first()->filter('message')->text()
        );

        $output = $this->requestWebserviceXML($this->wsKey->key, 'POST', 'addresses');

        self::assertEquals(1, $output->filter('prestashop > errors > error')->count());
        self::assertEquals(21, $output->filter('prestashop > errors > error')->first()->filter('code')->text());
        self::assertEquals(
            'No permission for this authentication key',
            $output->filter('prestashop > errors > error')->first()->filter('message')->text()
        );

        $output = $this->requestWebserviceXML($this->wsKey->key, 'PUT', 'addresses');

        self::assertEquals(1, $output->filter('prestashop > errors > error')->count());
        self::assertEquals(21, $output->filter('prestashop > errors > error')->first()->filter('code')->text());
        self::assertEquals(
            'No permission for this authentication key',
            $output->filter('prestashop > errors > error')->first()->filter('message')->text()
        );
    }

    public function testReturnGet(): void
    {
        // Set access
        WebserviceKey::setPermissionForAccount(
            $this->wsKey->id,
            [
                'addresses' => [
                    'GET' => 'on',
                ],
            ]
        );

        $output = $this->requestWebserviceXML($this->wsKey->key, 'GET', 'addresses');

        self::assertEquals(self::INITIAL_COUNT, $output->filter('prestashop > addresses > address')->count());

        $id = $output->filter('prestashop > addresses > address')->first()->attr('id');

        $output = $this->requestWebserviceXML($this->wsKey->key, 'GET', 'addresses/' . $id);

        $address = new Address($id);

        $children = $output->filter('prestashop > address')->children();
        foreach ($children as $item) {
            /** @var \DOMElement $item */
            $property = $item->nodeName;
            self::assertEquals($address->{$property}, $item->nodeValue);
        }
    }

    public function testManipulateData(): void
    {
        // Set access
        WebserviceKey::setPermissionForAccount(
            $this->wsKey->id,
            [
                'addresses' => [
                    'GET' => 'on',
                    'DELETE' => 'on',
                    'PUT' => 'on',
                    'POST' => 'on',
                ],
            ]
        );

        // Add an object in database
        $data = '<prestashop xmlns:xlink="http://www.w3.org/1999/xlink">
            <address>
                <id><![CDATA[]]></id>
                <id_customer><![CDATA[0]]></id_customer>
                <id_manufacturer><![CDATA[0]]></id_manufacturer>
                <id_supplier><![CDATA[0]]></id_supplier>
                <id_warehouse><![CDATA[0]]></id_warehouse>
                <id_country xlink:href="http://127.0.0.1/PrestaShop/api/countries/21"><![CDATA[21]]></id_country>
                <id_state xlink:href="http://127.0.0.1/PrestaShop/api/states/35"><![CDATA[35]]></id_state>
                <alias><![CDATA[supplier]]></alias>
                <company><![CDATA[Fashion]]></company>
                <lastname><![CDATA[supplier]]></lastname>
                <firstname><![CDATA[supplier]]></firstname>
                <vat_number><![CDATA[]]></vat_number>
                <address1><![CDATA[767 Fifth Ave.]]></address1>
                <address2><![CDATA[]]></address2>
                <postcode><![CDATA[10153]]></postcode>
                <city><![CDATA[New York]]></city>
                <other><![CDATA[]]></other>
                <phone><![CDATA[(212) 336-1440]]></phone>
                <phone_mobile><![CDATA[]]></phone_mobile>
                <dni><![CDATA[]]></dni>
                <deleted><![CDATA[0]]></deleted>
            </address>
        </prestashop>';
        $expected = new Crawler();
        $expected->addXmlContent($data);

        $output = $this->requestWebserviceXML($this->wsKey->key, 'POST', 'addresses', $data);

        // Check values from the input data
        $children = $output->filter('address')->children();
        foreach ($children as $item) {
            /** @var \DOMElement $item */
            $property = $item->nodeName;
            /** @var \DOMElement $expectedItem */
            $expectedItem = $expected->filter('prestashop > address >' . $property)->getNode(0);
            if ($property == 'id') {
                self::assertNotEmpty($item->nodeValue);
                continue;
            }
            if ($property == 'date_add' || $property == 'date_upd') {
                continue;
            }
            self::assertEquals(
                $expectedItem->nodeValue,
                $item->nodeValue,
                sprintf(
                    'In field %s, the expected value "%s" is not equals to "%s"',
                    $property,
                    $expectedItem->nodeValue,
                    $item->nodeValue
                )
            );
        }

        $id = $output->filter('address > id')->getNode(0)->nodeValue;

        // Check count
        $output = $this->requestWebserviceXML($this->wsKey->key, 'GET', 'addresses');
        self::assertEquals(self::INITIAL_COUNT + 1, $output->filter('prestashop > addresses > address')->count());

        // Update object in database
        $data = '<prestashop xmlns:xlink="http://www.w3.org/1999/xlink">
            <address>
                <id><![CDATA[' . $id . ']]></id>
                <id_customer><![CDATA[0]]></id_customer>
                <id_manufacturer><![CDATA[0]]></id_manufacturer>
                <id_supplier><![CDATA[0]]></id_supplier>
                <id_warehouse><![CDATA[0]]></id_warehouse>
                <id_country xlink:href="http://127.0.0.1/PrestaShop/api/countries/21"><![CDATA[21]]></id_country>
                <id_state xlink:href="http://127.0.0.1/PrestaShop/api/states/35"><![CDATA[35]]></id_state>
                <alias><![CDATA[supplier]]></alias>
                <company><![CDATA[Fashion]]></company>
                <lastname><![CDATA[supplier]]></lastname>
                <firstname><![CDATA[supplier]]></firstname>
                <vat_number><![CDATA[]]></vat_number>
                <address1><![CDATA[767 Fifth Ave.]]></address1>
                <address2><![CDATA[]]></address2>
                <postcode><![CDATA[10153]]></postcode>
                <city><![CDATA[Paris]]></city>
                <other><![CDATA[]]></other>
                <phone><![CDATA[+33 1 23 45 67 89]]></phone>
                <phone_mobile><![CDATA[+33 6 89 67 45 23]]></phone_mobile>
                <dni><![CDATA[]]></dni>
                <deleted><![CDATA[0]]></deleted>
            </address>
        </prestashop>';
        $expected = new Crawler();
        $expected->addXmlContent($data);

        $output = $this->requestWebserviceXML($this->wsKey->key, 'PUT', 'addresses/' . $id, $data);

        // Check values from the input data
        $children = $output->filter('address')->children();
        foreach ($children as $item) {
            /** @var \DOMElement $item */
            $property = $item->nodeName;
            /** @var \DOMElement $expectedItem */
            $expectedItem = $expected->filter('prestashop > address >' . $property)->getNode(0);
            if ($property == 'id') {
                self::assertNotEmpty($item->nodeValue);
                continue;
            }
            if ($property == 'date_add' || $property == 'date_upd') {
                continue;
            }
            self::assertEquals(
                $expectedItem->nodeValue,
                $item->nodeValue,
                sprintf(
                    'In field %s, the expected value "%s" is not equals to "%s"',
                    $property,
                    $expectedItem->nodeValue,
                    $item->nodeValue
                )
            );
        }

        // Check count
        $output = $this->requestWebserviceXML($this->wsKey->key, 'GET', 'addresses');
        self::assertEquals(self::INITIAL_COUNT + 1, $output->filter('prestashop > addresses > address')->count());

        // Delete object in database
        $output = $this->requestWebserviceXML($this->wsKey->key, 'DELETE', 'addresses/' . $id);

        // Check count
        $output = $this->requestWebserviceXML($this->wsKey->key, 'GET', 'addresses');
        self::assertEquals(self::INITIAL_COUNT, $output->filter('prestashop > addresses > address')->count());
    }
}
