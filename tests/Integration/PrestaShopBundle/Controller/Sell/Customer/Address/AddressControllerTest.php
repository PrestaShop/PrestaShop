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
    private const TEST_COUNTRY_ID = 21;

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

    /**
     * @var Client
     */
    private $client;

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
        $this->client = static::createClient();
        $this->client->followRedirects(true);
        $this->createTestAddress();
        $router = $this->client->getKernel()->getContainer()->get('router');
        $addressUrl = $router->generate('admin_addresses_index');
        $crawler = $this->client->request('GET', $addressUrl);
        $addresses = $this->getAddressList($crawler);
        self::assertEquals(3, count($addresses));
        $this->assertTestAddressExists($addresses);
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
     * Tests all the filters of address controller
     * Putting them all  into one test because it still works as intended and also is 2-3 times faster then creating a separate test for each.
     */
    public function testAddressFilters()
    {
        $testFilters = [
            ['address[id_address]' => $this->getTestAddress()->getId()],
            ['address[firstname]' => $this->getTestAddress()->getFirstName()],
            ['address[lastname]' => $this->getTestAddress()->getLastName()],
            ['address[address1]' => $this->getTestAddress()->getAddress()],
            ['address[postcode]' => $this->getTestAddress()->getPostCode()],
            ['address[city]' => $this->getTestAddress()->getCity()],
            ['address[id_country]' => self::TEST_COUNTRY_ID],
        ];

        foreach ($testFilters as $testFilter) {
            $this->assertAddressFiltersFindOnlyTestAddress($testFilter);
        }
    }

    /**
     * Asserts that there are 3 addresses before filtering, filters by given filters
     * and then asserts that there is only 1 address left
     *
     * @param array $filters
     */
    private function assertAddressFiltersFindOnlyTestAddress(array $filters)
    {
        $this->client->followRedirects(true);
        $router = $this->client->getContainer()->get('router');
        $addressUrl = $router->generate('admin_addresses_index');
        $crawler = $this->client->request('GET', $addressUrl);
        $addresses = $this->getAddressList($crawler);

        /* Make sure we have 3 addresses before we filter, to make sure list is not affected in some other way */
        self::assertEquals(3, count($addresses));
        $filterForm = $this->fillFiltersForm($crawler, $filters);
        $crawler = $this->client->submit($filterForm);
        $addresses = $this->getAddressList($crawler);
        self::assertEquals(1, count($addresses));

        /** Need to make sure not only that there is 1 address left, but also that it's the intended test address */
        $this->assertTestAddressExists($addresses);
    }

    /**
     * Validates that test address exists in provided list
     *
     * @param TestAddressDTO[]
     */
    private function assertTestAddressExists(array $addresses): void
    {
        $addressIds = [];

        foreach ($addresses as $address) {
            $addressIds[] = $address->getId();
        }
        self::assertTrue(in_array($this->getTestAddress()->getId(), $addressIds));
    }

    /**
     * Creates test address that can be used for testing filters
     */
    private function createTestAddress(): void
    {
        $router = $this->client->getContainer()->get('router');
        $createAddressUrl = $router->generate('admin_addresses_create');
        $crawler = $this->client->request('GET', $createAddressUrl);
        $submitButton = $crawler->selectButton('save-button');
        $addressForm = $submitButton->form();

        $addressForm = $this->formFiller->fillForm($addressForm, $this->getAddressModifications());

        /**
         * Without changing followRedirects to false when submitting the form
         * $dataChecker->getLastCreatedId() returns null.
         */
        $this->client->followRedirects(false);
        $this->client->submit($addressForm);
        $this->client->followRedirects(true);
        $dataChecker = $this->client->getContainer()->get('test.integration.core.form.identifiable_object.data_handler.address_form_data_handler_checker');
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
            'customer_address[id_country]' => self::TEST_COUNTRY_ID,
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
