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

use Country;
use PrestaShop\PrestaShop\Core\Exception\TypeException;
use Tests\Integration\PrestaShopBundle\Controller\GridControllerTestCase;
use Tests\Integration\PrestaShopBundle\Controller\TestEntityDTO;

class AddressControllerTest extends GridControllerTestCase
{
    private $countryId;

    public function __construct($name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);

        $this->gridRoute = 'admin_addresses_index';
        $this->testEntityName = 'address';
        $this->deleteEntityRoute = 'admin_addresses_delete';
    }

    /**
     * Tests all provided entity filters
     * All filters are tested in one test make tests run faster
     *
     * @throws TypeException
     */    public function testAddressFilters(): void
    {
        foreach ($this->getTestFilters() as $testFilter) {
            $this->assertFiltersFindOnlyTestEntity($testFilter);
        }
    }

    /**
     * @return array
     */
    protected function getTestFilters(): array
    {
        return [
            ['address[id_address]' => $this->getTestEntity()->getId()],
            ['address[firstname]' => 'firstname'],
            ['address[lastname]' => 'testLa'],
            ['address[address1]' => 'address1'],
            ['address[postcode]' => '11111'],
            ['address[city]' => 'stcity'],
            ['address[id_country]' => $this->countryId],
        ];
    }

    /**
     * @return TestEntityDTO
     */
    protected function getTestEntity(): TestEntityDTO
    {
        return new TestAddressDTO(
            $this->testEntityId,
            'testfirstname',
            'testlastname',
            'testaddress1',
            '11111',
            'testcity',
            'lithuania'
        );
    }

    /**
     * @return void
     */
    protected function createTestEntity(): void
    {
        // We get the country ID for Lithuania, and we set this country in the context so the controller will generate a
        // adapted to this country (especially regarding states selctor)
        $this->countryId = Country::getByIso('LT');
        $legacyContext = $this->client->getContainer()->get('prestashop.adapter.legacy.context');
        $backupCountry = $legacyContext->getContext()->country;
        $legacyContext->getContext()->country = new Country($this->countryId);

        $router = $this->client->getContainer()->get('router');
        $createAddressUrl = $router->generate('admin_addresses_create');
        $crawler = $this->client->request('GET', $createAddressUrl);
        $submitButton = $crawler->selectButton('save-button');
        $addressForm = $submitButton->form();

        $addressForm = $this->formFiller->fillForm($addressForm, $this->getAddressModifications());

        /*
         * Without changing followRedirects to false when submitting the form
         * $dataChecker->getLastCreatedId() returns null.
         */
        $this->client->followRedirects(false);
        $this->client->submit($addressForm);
        $this->client->followRedirects(true);
        $dataChecker = $this->client->getContainer()->get('test.integration.core.form.identifiable_object.data_handler.address_form_data_handler_checker');
        $this->testEntityId = $dataChecker->getLastCreatedId();
        $this->assertNotNull($this->testEntityId);

        // We can now reset the original context country
        $legacyContext->getContext()->country = $backupCountry;
    }

    /**
     * @param $tr
     * @param $i
     *
     * @return TestEntityDTO
     */
    protected function getEntity($tr, $i): TestEntityDTO
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

    /**
     * Gets modifications that are needed to fill address form
     *
     * @return array
     */
    public function getAddressModifications(): array
    {
        $testAddress = $this->getTestEntity();

        return [
            'customer_address[customer_email]' => 'pub@prestashop.com',
            'customer_address[alias]' => 'test_alias',
            'customer_address[first_name]' => $testAddress->getFirstName(),
            'customer_address[last_name]' => $testAddress->getLastName(),
            'customer_address[address1]' => $testAddress->getAddress(),
            'customer_address[postcode]' => $testAddress->getPostCode(),
            'customer_address[city]' => $testAddress->getCity(),
            'customer_address[id_country]' => $this->countryId,
        ];
    }
}
