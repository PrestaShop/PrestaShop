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

namespace Tests\Integration\PrestaShopBundle\Controller\Sell\Customer\Address;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\BrowserKit\Client;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\DomCrawler\Form;
use Tests\Integration\PrestaShopBundle\Controller\FormFiller\FormFiller;

class AddressControllerTest extends WebTestCase
{
    /**
     * This will be modified during the setUp
     *
     * @var int
     */
    private $testAddressId;

    /**
     * @var FormFiller
     */
    private $formFiller;

    public function __construct($name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);

        $this->formFiller = new FormFiller();
    }

    /**
     * Creates a test address and ensures that there are 3 addresses so filtered values can be counted correctly.
     */
    public function setUp()
    {
        $client = static::createClient();
        $this->createTestAddress($client);
        $router = $client->getKernel()->getContainer()->get('router');
        $addressUrl = $router->generate('admin_addresses_index');
        $crawler = $client->request('GET', $addressUrl);
        $addresses = $this->getAddressList($crawler);
        self::assertEquals(3, count($addresses));
        $this->validateTestAddressExists($addresses);
    }

    /**
     * Removes the created test address and ensures that it's successfully removed from the list.
     */
    public function tearDown()
    {
        $client = static::createClient();
        $client->followRedirects(true);
        $router = $client->getKernel()->getContainer()->get('router');
        $addressDeleteUrl = $router->generate('admin_addresses_delete', ['addressId' => $this->testAddressId]);
        $crawler = $client->request('POST', $addressDeleteUrl);
        $addresses = $this->getAddressList($crawler);
        self::assertEquals(2, count($addresses));
    }

    /**
     * Tests filter for first name
     */
    public function testFirstNameFilter(): void
    {
        $client = static::createClient();
        $client->followRedirects(true);
        $router = $client->getKernel()->getContainer()->get('router');
        $addressUrl = $router->generate('admin_addresses_index');
        $crawler = $client->request('GET', $addressUrl);
        $addresses = $this->getAddressList($crawler);
        /* Make sure we have 3 addresses before we filter by firstname */
        self::assertEquals(3, count($addresses));
        $filterForm = $this->fillFiltersForm($crawler, ['address[firstname]' => $this->getTestAddress()->getFirstName()]);
        $crawler = $client->submit($filterForm);
        $addresses = $this->getAddressList($crawler);
        self::assertEquals(1, count($addresses));
        $this->validateTestAddressExists($addresses);
    }

    /**
     * Tests filter for first name
     */
    public function testIdFilter(): void
    {
        $client = static::createClient();
        $client->followRedirects(true);
        $router = $client->getKernel()->getContainer()->get('router');
        $addressUrl = $router->generate('admin_addresses_index');
        $crawler = $client->request('GET', $addressUrl);
        $addresses = $this->getAddressList($crawler);
        /* Make sure we have 3 addresses before we filter by firstname */
        self::assertEquals(3, count($addresses));
        $filterForm = $this->fillFiltersForm($crawler, ['address[id_address]' => $this->getTestAddress()->getId()]);
        $crawler = $client->submit($filterForm);
        $addresses = $this->getAddressList($crawler);
        self::assertEquals(1, count($addresses));
        $this->validateTestAddressExists($addresses);
    }

    /**
     * Validates that test address exists in provided list
     *
     * @param TestAddressDTO[]
     */
    private function validateTestAddressExists(array $addresses): void
    {
        $addressIds = [];

        foreach ($addresses as $address) {
            $addressIds[] = $address->getId();
        }
        self::assertTrue(in_array($this->getTestAddress()->getId(), $addressIds));
    }

    /**
     * Creates test address that can be used for testing filters
     *
     * @param Client $client
     */
    private function createTestAddress(Client $client): void
    {
        $router = $client->getKernel()->getContainer()->get('router');
        $createAddressUrl = $router->generate('admin_addresses_create');
        $crawler = $client->request('GET', $createAddressUrl);
        $submitButton = $crawler->selectButton('save-button');
        $addressForm = $submitButton->form();
        $addressForm = $this->formFiller->fillForm($addressForm, $this->getAddressModifications());
        $client->submit($addressForm);
        $dataChecker = $client->getContainer()->get('test.integration.core.form.identifiable_object.data_handler.address_form_data_handler_checker');
        $this->testAddressId = $dataChecker->getLastCreatedId();
    }

    /**
     * Gets modifications that are needed to fill address form
     *
     * @return array
     */
    public function getAddressModifications(): array
    {
        $testAddress = $this->getTestAddress();

        return [
            'customer_address[customer_email]' => 'pub@prestashop.com',
            'customer_address[alias]' => 'test_alias',
            'customer_address[first_name]' => $testAddress->getFirstName(),
            'customer_address[last_name]' => $testAddress->getLastName(),
            'customer_address[address1]' => $testAddress->getAddress(),
            'customer_address[postcode]' => $testAddress->getPostCode(),
            'customer_address[city]' => $testAddress->getCity(),
            'customer_address[id_country]' => 21,
        ];
    }

    /**
     * @param Crawler $crawler
     * @param array $formModifications
     *
     * @return Form
     */
    private function fillFiltersForm(Crawler $crawler, array $formModifications): Form
    {
        $button = $crawler->selectButton('address[actions][search]');
        $filtersForm = $button->form();
        $this->formFiller->fillForm($filtersForm, $formModifications);

        return $filtersForm;
    }

    /**
     * @param Crawler $crawler
     *
     * @return TestAddressDTO[]
     */
    private function getAddressList(Crawler $crawler): array
    {
        return $crawler->filter('#address_grid_table')->filter('tbody tr')->each(function ($tr, $i) {
            return $this->getAddress($tr, $i);
        });
    }

    /**
     * Default addresses are not fit for filtering very well because most of the information is identical. So I need
     * new address with unique values to test various filters
     *
     * @return TestAddressDTO
     */
    private function getTestAddress(): TestAddressDTO
    {
        return new TestAddressDTO(
                $this->testAddressId,
                'testfirstname',
                'testlastname',
                'testaddress1',
                '11111',
                'testcity',
                'lithuania'
        );
    }

    /**
     * I think it makes sense to return an DTO here instead of array. It doesn't make sense to reuse EditableCustomerAddress
     * because it has more values that are needed. So new DTO it is, but where should I put architecture wise?
     * Call it AddressForListTest and put it somewhere next to the EditableCustomerAddress? Keep it here?
     *
     * @param $tr
     * @param $i
     *
     * @return TestAddressDTO
     */
    private function getAddress(Crawler $tr, int $i): TestAddressDTO
    {
        return new TestAddressDTO(
            (int) trim($tr->filter('.column-id_address')->text()),
            trim($tr->filter('.column-firstname')->text()),
            trim($tr->filter('.column-lastname')->text()),
            trim($tr->filter('.column-address1')->text()),
            trim($tr->filter('.column-postcode')->text()),
            trim($tr->filter('.column-city')->text()),
            trim($tr->filter('.column-country_name')->text())
        );
    }
}
