<?php


namespace Tests\Integration\PrestaShopBundle\Controller\Sell\Customer\Address;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DomCrawler\Crawler;

class AddressControllerTest extends WebTestCase
{
    public function testAddressSearch()
    {
        $client = static::createClient();
        $router = $client->getKernel()->getContainer()->get('router');
        $addressUrl = $router->generate('admin_addresses_index');
        $crawler = $client->request('GET', $addressUrl);
        $table = $this->getListData($crawler);
        //var_export($table);
        $filterForm = $this->fillFiltersForm($crawler, ['address[city]' => 'Miami']);

        $crawler = $client->submit($filterForm);
        $table = $this->getListData($crawler);
       // $this->assertCurrentlyInAddressList($client);
    }

    private function fillFiltersForm(Crawler $crawler, array $formModifications)
    {
        $button = $crawler->selectButton('address[actions][search]');
        $filtersForm = $button->form();
        foreach ($formModifications as $fieldName => $formValue) {
            $formField = $filtersForm->get($fieldName);
            $formField->setValue($formValue);
        }

        return $filtersForm;
    }

    private function getListData(Crawler $crawler)
    {
        return $crawler->filter('#address_grid_table')->filter('tbody tr')->each(function ($tr, $i) {
            return $tr->filter('td')->each(function ($td, $i) {
                return trim($td->text());
            });
        });
    }

}
