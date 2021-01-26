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

use FormField;
use PrestaShop\PrestaShop\Core\Domain\Address\QueryResult\EditableCustomerAddress;
use PrestaShop\PrestaShop\Core\Domain\Address\ValueObject\AddressId;
use PrestaShop\PrestaShop\Core\Domain\Country\ValueObject\CountryId;
use PrestaShop\PrestaShop\Core\Domain\Customer\ValueObject\CustomerId;
use PrestaShop\PrestaShop\Core\Domain\State\ValueObject\StateId;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\BrowserKit\Client;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\DomCrawler\Field\ChoiceFormField;
use Symfony\Component\DomCrawler\Form;

class AddressControllerTest extends WebTestCase
{
    public function setUp()
    {
        $client = static::createClient();
        $router = $client->getKernel()->getContainer()->get('router');
        $crawler = $this->createTestAddress($client);
        $addressUrl = $router->generate('admin_addresses_index');
        $crawler = $client->request('GET', $addressUrl);
        $this->validateStartingList($crawler);
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
        /** Make sure we have 3 addresses before we filter by firstname */
        self::assertEquals(3, count($addresses));
        $filterForm = $this->fillFiltersForm($crawler, ['address[firstname]' => $this->getTestAddress()->getFirstName()]);
        $crawler = $client->submit($filterForm);
        $addresses = $this->getAddressList($crawler);
        self::assertEquals(1, count($addresses));
    }

    /**
     * Checks if exactly 3 addresses exist and if test address exist
     *
     * @param Crawler $crawler
     */
    private function validateStartingList(Crawler $crawler)
    {
        $addresses = $this->getAddressList($crawler);
        self::assertEquals(3, count($addresses));
        $addressArray = $this->getAddressModifications();
        self::assertEquals($addresses[2]->getFirstName(), $addressArray['customer_address[first_name]']);
    }

    private function createTestAddress(Client $client): Crawler
    {
        $router = $client->getKernel()->getContainer()->get('router');
        $createAddressUrl = $router->generate('admin_addresses_create');
        $crawler = $client->request('GET', $createAddressUrl);
        $submitButton = $crawler->selectButton('save-button');
        $addressForm = $submitButton->form();
        $addressForm = $this->fillForm($addressForm, $this->getAddressModifications());
        return $client->submit($addressForm);
    }

    public function getAddressModifications()
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
     *
     * @todo This needs to be moved somewhere else because it can be reused by multiple controller tests
     *
     * @param Form $form
     *
     * @param array $formModifications
     *
     * @return Form
     */
    private function fillForm(Form $form, array $formModifications): Form
    {
        foreach ($formModifications as $fieldName => $formValue) {
            if (is_array($formValue)) {
                // For multi select checkboxes or select inputs
                /** @var ChoiceFormField[]|ChoiceFormField $formFields */
                $formFields = $form->get($fieldName);
                // Multiple checkboxes are returned as array
                if (is_array($formFields)) {
                    foreach ($formFields as $formField) {
                        if ('checkbox' === $formField->getType()) {
                            $optionValue = $formField->availableOptionValues()[0];
                            if (in_array($optionValue, $formValue)) {
                                $formField->tick();
                            } else {
                                $formField->untick();
                            }
                        } else {
                            $formField->select($formValue);
                        }
                    }
                } else {
                    $formFields->select($formValue);
                }
            } else {
                /** @var FormField $formField */
                $formField = $form->get($fieldName);
                $formField->setValue($formValue);
            }
        }

        return $form;
    }

    /**
     * @todo This needs to be moved somewhere else because it can be reused by multiple controller tests
     * @param Crawler $crawler
     * @param array $formModifications
     *
     * @return Form
     */
    private function fillFiltersForm(Crawler $crawler, array $formModifications): Form
    {
        $button = $crawler->selectButton('address[actions][search]');
        $filtersForm = $button->form();
        foreach ($formModifications as $fieldName => $formValue) {
            $formField = $filtersForm->get($fieldName);
            $formField->setValue($formValue);
        }

        return $filtersForm;
    }

    /**
     * @param Crawler $crawler
     *
     * @return array
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
     * @return Address
     */
    private function getTestAddress(): Address
    {
        return new Address(
                3,
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
     *
     * I think it makes sense to return an DTO here instead of array. It doesn't make sense to reuse EditableCustomerAddress
     * because it has more values that are needed. So new DTO it is, but where should I put architecture wise?
     * Call it AddressForListTest and put it somewhere next to the EditableCustomerAddress? Keep it here?
     * @param $tr
     * @param $i
     *
     * @return Address
     */
    private function getAddress(Crawler $tr, int $i): Address
    {
        return new Address(
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
