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
use Symfony\Component\DomCrawler\Crawler;
use Tests\Integration\PrestaShopBundle\Controller\GridControllerTestCase;
use Tests\Integration\PrestaShopBundle\Controller\TestEntityDTO;

class AddressControllerTest extends GridControllerTestCase
{
    private $countryId;

    public function __construct($name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);

        $this->createEntityRoute = 'admin_addresses_create';
        $this->gridRoute = 'admin_addresses_index';
        $this->testEntityName = 'address';
        $this->deleteEntityRoute = 'admin_addresses_delete';
        $this->formHandlerServiceId = 'prestashop.core.form.identifiable_object.handler.address_form_handler';
    }

    /**
     * Tests all provided entity filters
     * All filters are tested in one test make tests run faster
     *
     * @throws TypeException
     */
    public function testAddressFilters(): void
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
        return new TestEntityDTO(
            $this->testEntityId,
            [
                'firstName' => 'testfirstname',
                'lastName' => 'testlastname',
                'address' => 'testaddress1',
                'postCode' => '11111',
                'city' => 'testcity',
                'country' => 'lithuania',
            ]
        );
    }

    /**
     * @return void
     */
    protected function createTestEntity(): void
    {
        // We get the country ID for Lithuania, and we set this country in the context so the controller will
        // generate a form adapted to this country (especially regarding states selector)
        $this->countryId = Country::getByIso('LT');
        $legacyContext = $this->client->getContainer()->get('prestashop.adapter.legacy.context');
        $backupCountry = $legacyContext->getContext()->country;
        $legacyContext->getContext()->country = new Country($this->countryId);

        parent::createTestEntity();
        // We can now reset the original context country
        $legacyContext->getContext()->country = $backupCountry;
    }

    /**
     * @param $tr
     * @param $i
     *
     * @return TestEntityDTO
     */
    protected function getEntity(Crawler $tr, int $i): TestEntityDTO
    {
        return new TestEntityDTO(
            (int) trim($tr->filter('.column-id_address')->text()),
            [
                'firstName' => trim($tr->filter('.column-lastname')->text()),
                'lastName' => trim($tr->filter('.column-address1')->text()),
                'address' => trim($tr->filter('.column-postcode')->text()),
                'postCode' => trim($tr->filter('.column-city')->text()),
                'city' => trim($tr->filter('.column-city')->text()),
                'country' => trim($tr->filter('.column-country_name')->text()),
            ]
        );
    }

    /**
     * Gets modifications that are needed to fill address form
     *
     * @return array
     */
    protected function getCreateEntityFormModifications(): array
    {
        $testEntity = $this->getTestEntity();

        return [
            'customer_address[customer_email]' => 'pub@prestashop.com',
            'customer_address[alias]' => 'test_alias',
            'customer_address[first_name]' => $testEntity->firstName,
            'customer_address[last_name]' => $testEntity->lastName,
            'customer_address[address1]' => $testEntity->address,
            'customer_address[postcode]' => $testEntity->postCode,
            'customer_address[city]' => $testEntity->city,
            'customer_address[id_country]' => $this->countryId,
        ];
    }
}
